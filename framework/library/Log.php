<?php
namespace Framework\Library;

class Log {
    private $handle;
    private string $filePath;
    private int $maxSize;
    
    const LEVEL_DEBUG = 0;
    const LEVEL_INFO = 1;
    const LEVEL_WARNING = 2;
    const LEVEL_ERROR = 3;
    const LEVEL_CRITICAL = 4;

    private array $levelMap = [
        self::LEVEL_DEBUG => 'DEBUG',
        self::LEVEL_INFO => 'INFO',
        self::LEVEL_WARNING => 'WARNING',
        self::LEVEL_ERROR => 'ERROR',
        self::LEVEL_CRITICAL => 'CRITICAL'
    ];

    private int $threshold = self::LEVEL_INFO;

    public function __construct(string $filename, int $maxSize = 1048576 /* 1MB */) {
        $this->filePath = DIR_LOGS . $filename;
        $this->maxSize = $maxSize;
        
        if (!is_dir(DIR_LOGS)) {
            mkdir(DIR_LOGS, 0755, true);
        }
        
        $this->handle = fopen($this->filePath, 'a');
        if (!$this->handle) {
            throw new \RuntimeException("Failed to open log file: {$this->filePath}");
        }
    }

    public function write($message, int $level = self::LEVEL_INFO): void {
        $this->ensureLogFile();
        
        if ($level < $this->threshold) {
            return;
        }

        $levelName = $this->levelMap[$level] ?? 'UNKNOWN';
        $logEntry = sprintf(
            "[%s] [%s] %s\n",
            date('Y-m-d H:i:s'),
            $levelName,
            is_string($message) ? $message : json_encode($message, JSON_PRETTY_PRINT)
        );

        flock($this->handle, LOCK_EX);
        fwrite($this->handle, $logEntry);
        flock($this->handle, LOCK_UN);
        
        $this->rotateIfNeeded();
    }

    public function writeJson(array $data, int $level = self::LEVEL_INFO): void {
        $this->write([
            'timestamp' => microtime(true),
            'data' => $data
        ], $level);
    }

    public function setThreshold(int $threshold): void {
        $this->threshold = $threshold;
    }

    private function rotateIfNeeded(): void {
        if (filesize($this->filePath) > $this->maxSize) {
            $this->rotate();
        }
    }

    private function rotate(): void {
        flock($this->handle, LOCK_EX);
        fclose($this->handle);
        
        $newName = $this->filePath . '.' . date('YmdHis');
        rename($this->filePath, $newName);
        
        $this->handle = fopen($this->filePath, 'a');
        flock($this->handle, LOCK_UN);
    }

    private function ensureLogFile(): void {
        if (!is_resource($this->handle)) {
            $this->handle = fopen($this->filePath, 'a');
            if (!$this->handle) {
                throw new \RuntimeException("Failed to reopen log file");
            }
        }
    }

    public function __destruct() {
        if (is_resource($this->handle)) {
            flock($this->handle, LOCK_UN);
            fclose($this->handle);
        }
    }
}