<?php
namespace Framework\Library;

class Admin {
    private $db;
    private $session;
    private $config;
    private $log;
    private $admin_id = 0;
    private $username = '';
    private $permissions = [];
    private $data = [];

    public function __construct(\Framework\Core\Registry $registry) {
        $this->db = $registry->get('db');
        $this->session = $registry->get('session');
        $this->config = $registry->get('config');
        $this->log = $registry->get('log');
        
        // Auto-login if session exists
        if ($this->session->has('admin_id')) {
            $this->admin_id = $this->session->get('admin_id');
            $this->loadAdmin($this->admin_id);
        }
    }

    public function login(string $username, string $password, bool $remember = false): bool {
        $admin_query = $this->db->query(
            "SELECT * FROM `admins` 
             WHERE `username` = :username 
             AND `status` = 1",
            ['username' => $username]
        );

        if ($admin_query->num_rows) {
            $admin = $admin_query->row;
            
            if (password_verify($password, $admin['password'])) {
                if (password_needs_rehash($admin['password'], PASSWORD_DEFAULT)) {
                    $new_hash = password_hash($password, PASSWORD_DEFAULT);
                    $this->db->query(
                        "UPDATE `admins` SET `password` = :password 
                         WHERE `admin_id` = :admin_id",
                        ['password' => $new_hash, 'admin_id' => $admin['admin_id']]
                    );
                }

                $this->admin_id = $admin['admin_id'];
                $this->username = $admin['username'];
                $this->data = $admin;

                // Load permissions
                $this->loadPermissions();

                // Update last login
                $this->db->query(
                    "UPDATE `admins` SET 
                     `last_login` = NOW(), 
                     `ip` = :ip 
                     WHERE `admin_id` = :admin_id",
                    ['ip' => $this->session->get('ip'), 'admin_id' => $this->admin_id]
                );

                // Set session
                $this->session->set('admin_id', $this->admin_id);
                $this->session->set('admin_permissions', $this->permissions);

                // Remember me
                if ($remember) {
                    $this->createRememberToken();
                }

                // Log activity
                $this->logActivity('login');

                return true;
            }
        }

        return false;
    }

    public function logout(): void {
        // Remove session token if exists
        if ($this->session->has('admin_token')) {
            $this->db->query(
                "DELETE FROM `admin_sessions` 
                 WHERE `token` = :token",
                ['token' => $this->session->get('admin_token')]
            );
        }

        // Clear session
        $this->session->remove('admin_id');
        $this->session->remove('admin_permissions');
        $this->session->remove('admin_token');

        $this->admin_id = 0;
        $this->username = '';
        $this->permissions = [];
        $this->data = [];
    }

    public function isLogged(): bool {
        return $this->admin_id > 0;
    }

    public function getId(): int {
        return $this->admin_id;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function getData(string $key = '') {
        return $key ? ($this->data[$key] ?? null) : $this->data;
    }

    public function hasPermission(string $permission): bool {
        return isset($this->permissions[$permission]);
    }

    public function getPermissions(): array {
        return $this->permissions;
    }

    private function loadAdmin(int $admin_id): void {
        $admin_query = $this->db->query(
            "SELECT * FROM `admins` 
             WHERE `admin_id` = :admin_id 
             AND `status` = 1",
            ['admin_id' => $admin_id]
        );

        if ($admin_query->num_rows) {
            $this->admin_id = $admin_query->row['admin_id'];
            $this->username = $admin_query->row['username'];
            $this->data = $admin_query->row;
            $this->loadPermissions();
        } else {
            $this->logout();
        }
    }

    private function loadPermissions(): void {
        $this->permissions = [];

        // Get from session if available
        if ($this->session->has('admin_permissions')) {
            $this->permissions = $this->session->get('admin_permissions');
            return;
        }

        // Load from database
        $query = $this->db->query(
            "SELECT ag.permissions 
             FROM `admin_to_group` atg 
             LEFT JOIN `admin_groups` ag ON atg.group_id = ag.group_id 
             WHERE atg.admin_id = :admin_id",
            ['admin_id' => $this->admin_id]
        );

        foreach ($query->rows as $result) {
            if ($result['permissions']) {
                $permissions = json_decode($result['permissions'], true);
                foreach ($permissions as $key => $value) {
                    $this->permissions[$key] = $value;
                }
            }
        }

        // Store in session
        $this->session->set('admin_permissions', $this->permissions);
    }

    private function createRememberToken(): void {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + (30 * 24 * 60 * 60)); // 30 days

        $this->db->query(
            "INSERT INTO `admin_sessions` 
             SET `admin_id` = :admin_id,
                 `token` = :token,
                 `ip` = :ip,
                 `user_agent` = :user_agent,
                 `expires` = :expires,
                 `date_added` = NOW()",
            [
                'admin_id' => $this->admin_id,
                'token' => password_hash($token, PASSWORD_DEFAULT),
                'ip' => $this->session->get('ip'),
                'user_agent' => $this->session->get('user_agent'),
                'expires' => $expires
            ]
        );

        $this->session->set('admin_token', $token);
    }

    public function verifyRememberToken(): bool {
        if (!$this->session->has('admin_token')) {
            return false;
        }

        $token = $this->session->get('admin_token');
        $query = $this->db->query(
            "SELECT * FROM `admin_sessions` 
             WHERE `token` = :token AND `expires` > NOW()",
            ['token' => password_hash($token, PASSWORD_DEFAULT)]
        );

        if ($query->num_rows) {
            $session = $query->row;
            $this->admin_id = $session['admin_id'];
            $this->loadAdmin($this->admin_id);
            return true;
        }

        return false;
    }

    public function logActivity(string $action, array $data = []): void {
        $this->db->query(
            "INSERT INTO `admin_logs` 
             SET `admin_id` = :admin_id,
                 `action` = :action,
                 `data` = :data,
                 `ip` = :ip,
                 `user_agent` = :user_agent,
                 `date_added` = NOW()",
            [
                'admin_id' => $this->admin_id,
                'action' => $action,
                'data' => json_encode($data),
                'ip' => $this->session->get('ip'),
                'user_agent' => $this->request->server['HTTP_USER_AGENT'] ?? ''
            ]
        );
    }

    // Admin management methods
    public function addAdmin(array $data): int {
        $this->db->query(
            "INSERT INTO `admins` 
             SET `username` = :username,
                 `password` = :password,
                 `firstname` = :firstname,
                 `lastname` = :lastname,
                 `email` = :email,
                 `telephone` = :telephone,
                 `status` = :status,
                 `verified` = :verified,
                 `token` = :token,
                 `date_added` = NOW(),
                 `date_modified` = NOW()",
            [
                'username' => $data['username'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'firstname' => $data['firstname'],
                'lastname' => $data['lastname'],
                'email' => $data['email'],
                'telephone' => $data['telephone'],
                'status' => $data['status'] ?? 0,
                'verified' => $data['verified'] ?? 0,
                'token' => $data['token'] ?? null
            ]
        );

        $admin_id = $this->db->lastInsertId();

        if (isset($data['groups'])) {
            foreach ($data['groups'] as $group_id) {
                $this->db->query(
                    "INSERT INTO `admin_to_group` 
                     SET `admin_id` = :admin_id,
                         `group_id` = :group_id",
                    ['admin_id' => $admin_id, 'group_id' => $group_id]
                );
            }
        }

        return $admin_id;
    }

    // ... Additional admin management methods can be added here
}