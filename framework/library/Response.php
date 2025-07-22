<?php
namespace Framework\Library;

class Response {
    private array $headers = [];
    private int $compressionLevel = 0;
    private string $output = '';
    private int $statusCode = 200;
    private bool $headersSent = false;

    public function __construct() {
        $this->addHeader('Content-Type: text/html; charset=UTF-8');
    }

    public function addHeader(string $header): void {
        $this->headers[] = $header;
    }

    public function getHeaders(): array {
        return $this->headers;
    }
    
    public function setStatusCode(int $code, ?string $text = null): void {
        $this->statusCode = $code;
        $this->addHeader(
            sprintf('HTTP/1.1 %d %s', $code, $text ?? $this->getStatusText($code))
        );
    }

    private function getStatusText(int $code): string {
        $statusText = [
        // 2xx Success
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        204 => 'No Content',
        
        // 3xx Redirection
        301 => 'Moved Permanently',
        302 => 'Found',
        304 => 'Not Modified',
        
        // 4xx Client Error
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        409 => 'Conflict',
		410 => '410-Gone',
        422 => 'Unprocessable Entity',
        429 => 'Too Many Requests',
        
        // 5xx Server Error
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout'
    ];
        return $statusTexts[$code] ?? '';
    }
    
    public function setCacheHeaders(int $seconds, bool $public = true): void {
        $this->addHeader(sprintf(
            'Cache-Control: %s, max-age=%d',
            $public ? 'public' : 'private',
            $seconds
        ));
        $this->addHeader('Expires: ' . gmdate('D, d M Y H:i:s', time() + $seconds) . ' GMT');
    }
    
    public function setEtag(string $etag): void {
        $this->addHeader('ETag: "' . $etag . '"');
    }
    
    public function isNotModified(string $etag): bool {
        $clientEtag = $_SERVER['HTTP_IF_NONE_MATCH'] ?? '';
        return trim($clientEtag, '"') === $etag;
    }
    
    public function setCorsHeaders(
        string $origin = '*',
        string $methods = 'GET, POST, OPTIONS',
        string $headers = 'Content-Type'
    ): void {
        $this->addHeader('Access-Control-Allow-Origin: ' . $origin);
        $this->addHeader('Access-Control-Allow-Methods: ' . $methods);
        $this->addHeader('Access-Control-Allow-Headers: ' . $headers);
    }
    
    public function setSecurityHeaders(): void {
        $this->addHeader('X-Content-Type-Options: nosniff');
        $this->addHeader('X-Frame-Options: DENY');
        $this->addHeader('X-XSS-Protection: 1; mode=block');
        $this->addHeader('Referrer-Policy: strict-origin-when-cross-origin');
    }
    
    public function sendFile(
        string $path,
        ?string $filename = null,
        bool $download = true,
        ?string $mimeType = null
    ): void {
        if (!is_file($path)) {
            $this->setStatusCode(404);
            return;
        }

        $filename = $filename ?? basename($path);
        $mimeType = $mimeType ?? mime_content_type($path);
        $filesize = filesize($path);

        $this->addHeader('Content-Type: ' . $mimeType);
        $this->addHeader('Content-Length: ' . $filesize);
        
        if ($download) {
            $this->addHeader(
                'Content-Disposition: attachment; filename="' . $filename . '"'
            );
        }

        $this->sendHeaders();
        
        readfile($path);
        exit;
    }
    
    public function json(
        $data,
        int $status = 200,
        bool $pretty = false,
        int $options = 0
    ): void {
        $this->setStatusCode($status);
        $this->addHeader('Content-Type: application/json');
        $this->setOutput(json_encode(
            $data,
            $pretty ? (JSON_PRETTY_PRINT | $options) : $options
        ));
    }

    public function redirect(string $url, int $status = 302): void {
        $this->setStatusCode($status);
        $this->addHeader('Location: ' . str_replace(['&amp;', "\n", "\r"], ['&', '', ''], $url));
        $this->sendHeaders();
        exit;
    }

    public function setCompression(int $level): void {
        $this->compressionLevel = max(-1, min(9, $level));
    }

    public function setOutput(string $output): void {
        $this->output = $output;
    }

    public function getOutput(): string {
        return $this->output;
    }

    public function sendHeaders(): void {
        if (!$this->headersSent) {
            foreach ($this->headers as $header) {
                header($header);
            }
            $this->headersSent = true;
        }
    }

    public function output(): void {
        if ($this->output) {
            $output = $this->shouldCompress() 
                ? $this->compress($this->output) 
                : $this->output;

            $this->sendHeaders();
            echo $output;
        }
    }

    private function shouldCompress(): bool {
        return $this->compressionLevel > 0 &&
               extension_loaded('zlib') &&
               !ini_get('zlib.output_compression') &&
               !headers_sent() &&
               connection_status() === CONNECTION_NORMAL;
    }

    private function compress(string $data): string {
        $encoding = $this->getCompressionEncoding();
        if ($encoding) {
            $this->addHeader('Content-Encoding: ' . $encoding);
            return gzencode($data, $this->compressionLevel);
        }
        return $data;
    }

    private function getCompressionEncoding(): ?string {
        $acceptEncoding = $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '';
        
        if (strpos($acceptEncoding, 'gzip') !== false) {
            return 'gzip';
        }
        
        if (strpos($acceptEncoding, 'x-gzip') !== false) {
            return 'x-gzip';
        }
        
        return null;
    }
}