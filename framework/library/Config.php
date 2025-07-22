<?php
namespace Framework\Library;

class Config {
    private string $config_file;
    private array $config_data = [];
    private array $loaded_files = [];

    public function __construct(string $config_file) {
        // Try environment-specific config first, fall back to default
        $env_file = $this->getEnvironmentConfigPath($config_file);
        $this->config_file = file_exists($env_file) ? $env_file : $config_file;
        $this->load();
    }

    private function getEnvironmentConfigPath(string $original_path): string {
        if (!defined('ENVIRONMENT')) {
            return $original_path;
        }
        
        $info = pathinfo($original_path);
        return $info['dirname'] . '/' . $info['filename'] . '.' . ENVIRONMENT . '.' . $info['extension'];
    }

    private function load(): void {
        if (isset($this->loaded_files[$this->config_file])) {
            return;
        }

        if (!is_file($this->config_file)) {
            // Instead of throwing exception, use empty array
            $this->config_data = [];
            $this->loaded_files[$this->config_file] = true;
            return;
        }

        $_ = [];
        require($this->config_file);
        $this->config_data = array_merge($this->config_data, $_);
        $this->loaded_files[$this->config_file] = true;
    }

    public function get(string $key, $default = null) {
        return $this->config_data[$key] ?? $default;
    }

    public function has(string $key): bool {
        return isset($this->config_data[$key]);
    }

    public function set(string $key, $value, bool $persist = false): void {
        $this->config_data[$key] = $value;
        if ($persist) {
            $this->write();
        }
    }

    public function write(): void {
        $tempFile = tempnam(dirname($this->config_file), 'config_');
        try {
            file_put_contents($tempFile, $this->generateConfigContent());
            if (!rename($tempFile, $this->config_file)) {
                throw new \RuntimeException("Failed to write config file");
            }
        } finally {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    private function generateConfigContent(): string {
        $output = "<?php\n\n";
        $categories = [];

        foreach ($this->config_data as $key => $value) {
            $prefix = explode('_', $key)[0];
            $categories[$prefix][$key] = $value;
        }

        foreach ($categories as $category => $configs) {
            $output .= "// $category\n";
            foreach ($configs as $key => $value) {
                $output .= "\$_['$key'] = " . $this->formatValue($value) . ";\n";
            }
            $output .= "\n";
        }

        return $output;
    }

    private function formatValue($value): string {
        if (is_array($value)) {
            return "['" . implode("','", $value) . "']";
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (is_numeric($value)) {
            return (string)$value;
        }
        if (is_string($value) && defined($value)) {
            return $value;
        }
        return "'" . addslashes($value) . "'";
    }
}