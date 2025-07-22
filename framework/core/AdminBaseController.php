<?php
namespace Framework\Core;

class AdminBaseController extends Controller
{
    protected $document;
    protected $admin;
    protected $token;
    
    public function __construct(Registry $registry)
    {
        parent::__construct($registry);
        $this->document = $this->registry->get("document");
        $this->admin = $this->registry->get("admin");
        $this->token = $this->generateToken();
        
        // Verify admin authentication for all admin controllers
        $this->verifyAdminAccess();
    }

    /**
     * Enhanced model loader
     * Usage: $this->load->model("blog/posts");
     *        $data['posts'] = $this->model_blog_posts->getPosts();
     */
    public function load(): object 
    {
        return new class($this->registry) {
            private $registry;
            
            public function __construct(Registry $registry) {
                $this->registry = $registry;
            }
            
            public function model(string $modelPath): object {
                $parts = explode('/', $modelPath);
                if (count($parts) !== 2) {
                    throw new \InvalidArgumentException('Invalid model path format');
                }
                
                [$group, $model] = $parts;
                $modelClassName = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', ucfirst($group) . ucfirst($model));
                $propertyName = 'model_' . $group . '_' . $model;
                
                if (!isset($this->registry->get('controller')->$propertyName)) {
                    $modelFile = DIR_APP . "model/{$group}/{$model}.php";
                    
                    if (!file_exists($modelFile)) {
                        throw new \RuntimeException("Model file not found: {$modelFile}");
                    }
                    
                    require_once($modelFile);
                    
                    if (!class_exists($modelClassName)) {
                        throw new \RuntimeException("Model class not found: {$modelClassName}");
                    }
                    
                    $instance = new $modelClassName($this->registry);
                    $this->registry->get('controller')->$propertyName = $instance;
                }
                
                return $this->registry->get('controller')->$propertyName;
            }
            
            public function controller(string $controllerPath): string {
                $parts = explode('/', $controllerPath);
                if (count($parts) !== 2) {
                    throw new \InvalidArgumentException('Invalid controller path format');
                }
                
                [$group, $controller] = $parts;
                $controllerFile = DIR_APP . "controller/{$group}/{$controller}.php";
                $controllerClassName = 'Controller' . preg_replace('/[^a-zA-Z0-9]/', '', ucfirst($group) . ucfirst($controller));
                
                if (!file_exists($controllerFile)) {
                    throw new \RuntimeException("Controller file not found: {$controllerFile}");
                }
                
                require_once($controllerFile);
                
                if (!class_exists($controllerClassName)) {
                    throw new \RuntimeException("Controller class not found: {$controllerClassName}");
                }
                
                $controllerInstance = new $controllerClassName($this->registry);
                return $controllerInstance->index();
            }
            
            public function view(string $viewPath, array $data = []): string {
                $viewFile = DIR_APP . "view/{$viewPath}.php";
                
                if (!file_exists($viewFile)) {
                    throw new \RuntimeException("View file not found: {$viewFile}");
                }
                
                extract($data);
                ob_start();
                require($viewFile);
                return ob_get_clean();
            }
        };
    }

    /**
     * AJAX Response Handler
     */
    protected function ajaxResponse(array $data, bool $success = true): void 
    {
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode([
            'success' => $success,
            'data' => $data,
            'token' => $this->token
        ]));
    }

    /**
     * Enhanced Redirect with Token
     */
    public function redirect(string $url, int $statusCode = 302): void 
    {
        $url = HTTP_SERVER . ADMIN_DIR . '/' . ltrim($url, '/');
        $separator = str_contains($url, '?') ? '&' : '?';
        $this->response->redirect($url . $separator . 'token=' . $this->token, $statusCode);
        exit();
    }

    /**
     * Page Reload with optional delay
     */
    public function reload(int $delay = 0): void 
    {
        $script = $delay > 0 
            ? "setTimeout(() => location.reload(), " . ($delay * 1000) . ")" 
            : "location.reload()";
        
        $this->response->addHeader('Content-Type: application/javascript');
        $this->response->setOutput('<script>' . $script . '</script>');
    }

    /**
     * Secure Link Generator
     */
    public function link(string $route): string 
    {
        $route = ltrim($route, '/');
        $baseUrl = HTTP_SERVER . ADMIN_DIR . '/';
        $separator = str_contains($route, '?') ? '&' : '?';
        return $baseUrl . $route . $separator . 'token=' . $this->token;
    }

    /**
     * Admin Access Verification
     */
    protected function verifyAdminAccess(): void 
    {
        if (!$this->admin->is_logged_in()) {
            $this->response->redirect($this->url->link('login', '', true));
        }
        
        // Verify CSRF token for POST requests
        if ($this->request->isMethod('POST') && 
            !$this->request->validateCsrfToken($this->request->get('token'))) {
            $this->response->setStatusCode(403)->json([
                'error' => 'Invalid CSRF token'
            ]);
        }
    }

    /**
     * Generate CSRF Token
     */
    protected function generateToken(): string 
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Log Admin Activity
     */
    protected function logActivity(string $action, array $data = []): void 
    {
        $this->db->query(
            "INSERT INTO admin_activity_log 
             SET admin_id = :admin_id,
                 action = :action,
                 data = :data,
                 ip = :ip,
                 user_agent = :user_agent,
                 date_added = NOW()",
            [
                'admin_id' => $this->admin->getId(),
                'action' => $action,
                'data' => json_encode($data),
                'ip' => $this->request->getClientIp(),
                'user_agent' => $this->request->getHeader('User-Agent')
            ]
        );
    }
}
