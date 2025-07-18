<?php
namespace Framework\Library;

class Cache {
    private int $expire;
    private string $cacheDir;

    public function __construct(int $expire = 3600) {
        $this->expire = $expire;
        $this->cacheDir = DIR_CACHE . 'mysql/';
        
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }

    public function has(string $key): bool {
        return !empty($this->findCacheFiles($key));
    }

    public function get(string $key): array {
        $files = $this->findCacheFiles($key);
        if (empty($files)) return [];

        $fp = fopen($files[0], 'rb');
        flock($fp, LOCK_SH);
        $data = json_decode(stream_get_contents($fp), true);
        flock($fp, LOCK_UN);
        fclose($fp);

        return $data ?? [];
    }

    public function set(string $key, array|string|null $value, int $expire = 0): void {
        $this->delete($key);
        $expire = $expire ?: $this->expire;
        $filepath = $this->getCachePath($key, time() + $expire);
        
        $tmpFile = tempnam($this->cacheDir, 'tmp_');
        if (file_put_contents($tmpFile, json_encode($value))) {
            if (!rename($tmpFile, $filepath)) {
                @unlink($tmpFile);
                throw new \RuntimeException("Cache rename failed");
            }
        } else {
            @unlink($tmpFile);
            throw new \RuntimeException("Cache write failed");
        }
    }

    public function delete(string $key): void {
        foreach ($this->findCacheFiles($key) as $file) {
            $this->safeUnlink($file);
        }
    }

    public function deleteAll(): void {
        foreach (glob($this->cacheDir . 'cache.*') as $file) {
            $this->safeUnlink($file);
        }
    }

    public function __destruct() {
        foreach (glob($this->cacheDir . 'cache.*') as $file) {
            if (time() > (int) substr(strrchr($file, '.'), 1)) {
                $this->safeUnlink($file);
            }
        }
    }

    private function findCacheFiles(string $key): array {
        return glob($this->getCachePath($key) . '.*');
    }

    private function getCachePath(string $key, int $timestamp = null): string {
        $sanitized = preg_replace('/[^A-Z0-9\._-]/i', '', $key);
        return $this->cacheDir . 'cache.' . $sanitized . 
               ($timestamp ? ".$timestamp" : '');
    }

    private function safeUnlink(string $file): void {
        @unlink($file);
        clearstatcache(false, $file);
    }
}