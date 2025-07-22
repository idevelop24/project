<?php
namespace Framework\Library;

class Request {
    public array $get = [];
    public array $post = [];
    public array $cookie = [];
    public array $files = [];
    public array $server = [];
    private array $json = [];
    
    public function __construct() {
        $this->get = $this->clean($_GET);
        $this->post = $this->clean($_POST);
        $this->cookie = $this->clean($_COOKIE);
        $this->files = $this->processFiles($_FILES);
        $this->server = $this->clean($_SERVER);
        $this->parseJsonInput();
    }
    
    public function getMethod(): string {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    public function isMethod(string $method): bool {
        return $this->getMethod() === strtoupper($method);
    }
    
    public function clean(mixed $data): mixed {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                unset($data[$key]);
                $data[$this->clean($key)] = $this->clean($value);
            }
            return $data;
        }
        
        if (is_scalar($data)) {
            return trim(htmlspecialchars($data, ENT_QUOTES, 'UTF-8'));
        }
        
        return $data;
    }
    
    private function processFiles(array $files): array {
        $result = [];
        foreach ($files as $key => $file) {
            if (is_array($file['name'])) {
                foreach ($file['name'] as $i => $name) {
                    $result[$key][$i] = [
                        'name' => $file['name'][$i],
                        'type' => $file['type'][$i],
                        'tmp_name' => $file['tmp_name'][$i],
                        'error' => $file['error'][$i],
                        'size' => $file['size'][$i]
                    ];
                }
            } else {
                $result[$key] = $file;
            }
        }
        return $result;
    }
    
    private function parseJsonInput(): void {
        if ($this->isMethod('POST') && 
            strpos($this->server['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
            $input = file_get_contents('php://input');
            $this->json = json_decode($input, true) ?? [];
        }
    }

    public function getJson(?string $key = null, $default = null) {
        if ($key === null) return $this->json;
        return $this->json[$key] ?? $default;
    }
    
    public function validateUpload(string $field, array $allowedTypes, int $maxSize = 0): bool {
        if (!$this->hasUpload($field)) {
            return false;
        }
        
        $file = $this->files[$field];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        return in_array($ext, $allowedTypes) && 
               ($maxSize <= 0 || $file['size'] <= $maxSize);
    }
    
    public function getUploadedFile(string $field): ?array {
        return $this->hasUpload($field) ? $this->files[$field] : null;
    }
    
    public function hasUpload(string $field): bool {
        return !empty($this->files[$field]['tmp_name']) && 
               is_uploaded_file($this->files[$field]['tmp_name']);
    }
    
    public function getHeader(string $name): ?string {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        return $this->server[$key] ?? null;
    }
    
    public function isAjax(): bool {
        return strtolower($this->getHeader('X-Requested-With') ?? '') === 'xmlhttprequest';
    }
    
    public function isApiRequest(): bool {
        return strpos($this->server['REQUEST_URI'] ?? '', '/api/') === 0;
    }
    
    public function getClientIp(): string {
        $headers = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];
        
        foreach ($headers as $header) {
            if ($ip = $this->server[$header] ?? false) {
                return trim(explode(',', $ip)[0]);
            }
        }
        
        return '0.0.0.0';
    }
    
    public function validateCsrfToken(string $token): bool {
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        return hash_equals($sessionToken, $token);
    }

    public function isSecure(): bool {
        return (!empty($this->server['HTTPS']) && $this->server['HTTPS'] !== 'off') || ($this->server['SERVER_PORT'] ?? 80) == 443;
    }
}