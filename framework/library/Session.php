<?php
namespace Framework\Library;

class Session {
    private $registry;
    private $prefix;
    private $data = [];
    private $started = false;
    private $cookieParams = [
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ];

    public function __construct(\Framework\Core\Registry $registry, string $prefix = '') {
        $this->registry = $registry;
        $this->prefix = $prefix;
        $this->configure();
    }

    private function configure(): void {
        $config = $this->registry->get('config');
        $request = $this->registry->get('request');

        $this->cookieParams = [
            'lifetime' => $config->get('session_expire') ?? 0,
            'path' => $config->get('session_path') ?? '/',
            'domain' => $config->get('session_domain') ?? '',
            'secure' => $request->isSecure(),
            'httponly' => true,
            'samesite' => $config->get('session_samesite') ?? 'Lax'
        ];

        session_set_cookie_params($this->cookieParams);
    }

    public function start(): void {
        if ($this->started) {
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start([
                'name' => $this->prefix . 'session_id',
                'use_strict_mode' => true,
                'cookie_lifetime' => $this->cookieParams['lifetime'],
                'cookie_path' => $this->cookieParams['path'],
                'cookie_domain' => $this->cookieParams['domain'],
                'cookie_secure' => $this->cookieParams['secure'],
                'cookie_httponly' => $this->cookieParams['httponly'],
                'cookie_samesite' => $this->cookieParams['samesite'],
                'gc_maxlifetime' => $this->cookieParams['lifetime'] ?: 1440,
                'sid_length' => 128,
                'sid_bits_per_character' => 6
            ]);
        }

        $this->data = &$_SESSION;
        $this->started = true;
        $this->registry->get('log')->write('Session started: ' . $this->prefix, Log::LEVEL_DEBUG);
    }

    public function has(string $key): bool {
        return isset($this->data[$key]);
    }

    public function get(string $key, $default = null) {
        return $this->data[$key] ?? $default;
    }

    public function set(string $key, $value): void {
        $this->data[$key] = $value;
    }

    public function remove(string $key): void {
        if (isset($this->data[$key])) {
            unset($this->data[$key]);
        }
    }

    public function destroy(): void {
        if ($this->started) {
            session_destroy();
            $this->data = [];
            $this->started = false;
        }
    }

    public function getId(): string {
        return session_id();
    }

    public function regenerateId(bool $deleteOldSession = true): bool {
        return session_regenerate_id($deleteOldSession);
    }
}