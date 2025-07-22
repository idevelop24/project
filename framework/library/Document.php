<?php
namespace Framework\Library;

class Document {
    private string $title = '';
    private string $description = '';
    private array $links = [];
    private array $styles = [];
    private array $scripts = [];
    private array $meta = [];
    private array $jsonLd = [];
    private string $assetsBaseUrl = '';

    public function __construct(string $assetsBaseUrl = '') {
        $this->assetsBaseUrl = rtrim($assetsBaseUrl, '/');
    }

    public function setTitle(string $title): void {
        $this->title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function setDescription(string $description): void {
        $this->description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function addLink(string $href, string $rel): void {
        $this->links[$href] = [
            'href' => $this->generateUrl($href),
            'rel' => htmlspecialchars($rel, ENT_QUOTES, 'UTF-8')
        ];
    }

    public function getLinks(): array {
        return $this->links;
    }

    public function addStyle(string $path, array $options = []): void {
        $defaults = [
            'rel' => 'stylesheet',
            'media' => 'screen',
            'version' => null,
            'attributes' => []
        ];
        $options = array_merge($defaults, $options);

        $url = $this->generateAssetUrl($path, $options['version']);
        
        $this->styles[$url] = [
            'href' => $url,
            'rel' => $options['rel'],
            'media' => $options['media'],
            'attributes' => $options['attributes']
        ];
    }

    public function getStyles(): array {
        return $this->styles;
    }

    public function addScript(string $path, string $position = 'footer', array $options = []): void {
        $defaults = [
            'version' => null,
            'attributes' => [],
            'async' => false,
            'defer' => false
        ];
        $options = array_merge($defaults, $options);

        $url = $this->generateAssetUrl($path, $options['version']);
        
        $this->scripts[$position][$url] = [
            'src' => $url,
            'attributes' => $options['attributes'],
            'async' => $options['async'],
            'defer' => $options['defer']
        ];
    }

    public function getScripts(string $position = 'footer'): array {
        return $this->scripts[$position] ?? [];
    }

    public function addBundle(string $bundleName): void {
        $manifestPath = $this->assetsBaseUrl . '/assets/manifest.json';
        if (!is_file($manifestPath)) {
            throw new \RuntimeException("Manifest file not found: {$manifestPath}");
        }

        $manifest = json_decode(file_get_contents($manifestPath), true);
        if (isset($manifest[$bundleName])) {
            foreach ($manifest[$bundleName]['css'] ?? [] as $css) {
                $this->addStyle($css);
            }
            foreach ($manifest[$bundleName]['js'] ?? [] as $js) {
                $this->addScript($js);
            }
        }
    }

    public function addMeta(string $name, string $content): void {
        $this->meta[$name] = [
            'name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
            'content' => htmlspecialchars($content, ENT_QUOTES, 'UTF-8')
        ];
    }

    public function addMetaTag(array $attributes): void {
        $key = md5(serialize($attributes));
        $this->meta[$key] = array_map(function($value) {
            return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }, $attributes);
    }

    public function getMeta(): array {
        return $this->meta;
    }

    public function addJsonLd(string $key, array $data): void {
        $this->jsonLd[$key] = $data;
    }

    public function renderJsonLd(): string {
        $json = json_encode($this->jsonLd, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES);
        return '<script type="application/ld+json">' . $json . '</script>';
    }

    private function generateAssetUrl(string $path, ?string $version): string {
        $url = $this->assetsBaseUrl ? $this->assetsBaseUrl . '/' . ltrim($path, '/') : $path;
        
        if ($version && strpos($url, '?') === false) {
            $url .= '?v=' . $version;
        }
        
        return $url;
    }

    private function generateUrl(string $path): string {
        return $this->assetsBaseUrl ? $this->assetsBaseUrl . '/' . ltrim($path, '/') : $path;
    }
}