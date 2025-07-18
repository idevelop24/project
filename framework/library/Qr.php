<?php
namespace Framework\Library;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Output\QRImage;

class Qr {
    private QROptions $options;
    private array $presets = [
        'product' => [
            'size' => 8,
            'bgColor' => [255, 255, 255],
            'outputType' => QRCode::OUTPUT_IMAGE_SVG
        ],
        'auth' => [
            'eccLevel' => QRCode::ECC_H,
            'imageTransparent' => true
        ]
    ];

    public function __construct() {
        $this->options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel' => QRCode::ECC_L,
            'imageBase64' => true,
            'imageTransparent' => false,
            'bgColor' => [107, 30, 38],
            'version' => 5,
            'scale' => 5,
            'quietzoneSize' => 4
        ]);
    }

    public function setOption(string $option, $value): void {
        if (!property_exists($this->options, $option)) {
            throw new \InvalidArgumentException("Invalid option: {$option}");
        }
        $this->options->{$option} = $value;
    }

    public function render(string $data): string {
        try {
            return (new QRCode($this->options))->render($data);
        } catch (\Exception $e) {
            throw new \RuntimeException("QR generation failed: " . $e->getMessage());
        }
    }

    public function renderPreset(string $data, string $presetName): string {
        if (!isset($this->presets[$presetName])) {
            throw new \InvalidArgumentException("Invalid preset: {$presetName}");
        }

        foreach ($this->presets[$presetName] as $option => $value) {
            $this->setOption($option, $value);
        }

        return $this->render($data);
    }

    public function saveToFile(string $data, string $path, array $options = []): void {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            throw new \InvalidArgumentException("Directory does not exist: {$dir}");
        }

        $tempOptions = clone $this->options;
        $tempOptions->outputType = QRCode::OUTPUT_IMAGE_PNG;
        $tempOptions->imageBase64 = false;

        foreach ($options as $key => $value) {
            $this->setOption($key, $value);
        }

        try {
            (new QRCode($tempOptions))->render($data, $path);
        } catch (\Exception $e) {
            throw new \RuntimeException("Failed to save QR code: " . $e->getMessage());
        }
    }

    public function withLogo(string $data, string $logoPath, float $logoScale = 0.2): string {
        $this->validateLogo($logoPath);

        $options = clone $this->options;
        $options->addLogoSpace = true;
        $options->logoSpaceWidth = (int)($options->scale * $logoScale * 10);
        $options->logoSpaceHeight = (int)($options->scale * $logoScale * 10);

        try {
            $qrOutput = (new QRCode($options))->render($data);
            return $this->embedLogo($qrOutput, $logoPath);
        } catch (\Exception $e) {
            throw new \RuntimeException("Logo embedding failed: " . $e->getMessage());
        }
    }

    public function setSize(int $sizePixel, int $margin = 4): void {
        $this->options->scale = max(1, min(20, $sizePixel));
        $this->options->quietzoneSize = max(1, $margin);
    }

    private function validateLogo(string $logoPath): void {
        if (!is_file($logoPath)) {
            throw new \RuntimeException("Logo file not found: {$logoPath}");
        }
        
        $mime = mime_content_type($logoPath);
        if (!in_array($mime, ['image/png', 'image/jpeg'])) {
            throw new \RuntimeException("Invalid logo format. Only PNG/JPEG allowed");
        }
    }

    private function embedLogo(string $qrCode, string $logoPath): string {
        // Implementation depends on your image library
        // This would use your Image class to merge the logo
        return $qrCode; // Placeholder
    }
}