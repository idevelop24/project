<?php
namespace Framework\Library;
class Image {
	private string $file;
    private $image;
    private int $width;
    private int $height;
    private string $bits;
    private string $mime;
    private array $exif = [];
    private float $quality = 90;
	
	public function __construct() {
        if (!extension_loaded('gd')) {
            throw new \RuntimeException('GD extension is not loaded');
        }
    }
	
	public function start(string $file): void {
        if (!self::isValidImage($file)) {
            throw new \InvalidArgumentException("Invalid image file: {$file}");
        }

        $this->file = $file;
        $info = getimagesize($file);

        $this->width = $info[0];
        $this->height = $info[1];
        $this->bits = $info['bits'] ?? '';
        $this->mime = $info['mime'] ?? '';

        switch ($this->mime) {
            case 'image/jpeg':
                $this->image = imagecreatefromjpeg($file);
                break;
            case 'image/png':
                $this->image = imagecreatefrompng($file);
                imageinterlace($this->image, false);
                break;
            case 'image/gif':
                $this->image = imagecreatefromgif($file);
                break;
            case 'image/webp':
                $this->image = imagecreatefromwebp($file);
                break;
            default:
                throw new \RuntimeException("Unsupported image type: {$this->mime}");
        }

        $this->preserveExif();
    }

	
	public function getFile(): string {
		return $this->file;
	}

	
	public function getImage(): object {
		return $this->image;
	}

	
	public function getWidth(): int {
		return $this->width;
	}

	
	public function getHeight(): int {
		return $this->height;
	}

	
	public function getBits(): string {
		return $this->bits;
	}

	
	public function getMime(): string {
		return $this->mime;
	}
	
	public static function isValidImage(string $path): bool {
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $info = @getimagesize($path);
        return $info && in_array($info['mime'], $allowed);
    }
	
	public function convertTo(string $format, int $quality = 90) {
		$this->mime = 'image/' . $format;
		// Update internal tracking for save()
	}
	
	public function preserveExif(): void {
        if ($this->mime === 'image/jpeg' && function_exists('exif_read_data')) {
            $this->exif = @exif_read_data($this->file) ?: [];
        }
    }

	
	public function save(string $file, int $quality = 90): void {
		$info = pathinfo($file);

		$extension = strtolower($info['extension']);

		if (is_object($this->image) || is_resource($this->image)) {
			if ($extension == 'jpeg' || $extension == 'jpg') {
				imagejpeg($this->image, $file, $quality);
			} elseif ($extension == 'png') {
				imagepng($this->image, $file);
			} elseif ($extension == 'gif') {
				imagegif($this->image, $file);
			} elseif ($extension == 'webp') {
				imagewebp($this->image, $file);
			}

			imagedestroy($this->image);
		}
	}
	
	/*public function save(string $path, ?int $quality = null): void {
        $quality = $quality ?? $this->quality;
        $tempFile = tempnam(dirname($path), 'img_');
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        try {
            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    imagejpeg($this->image, $tempFile, $quality);
                    break;
                case 'png':
                    imagepng($this->image, $tempFile);
                    break;
                case 'gif':
                    imagegif($this->image, $tempFile);
                    break;
                case 'webp':
                    imagewebp($this->image, $tempFile, $quality);
                    break;
                default:
                    throw new \RuntimeException("Unsupported output format: {$extension}");
            }

            if (!rename($tempFile, $path)) {
                throw new \RuntimeException("Failed to save image to {$path}");
            }
        } finally {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }*/

	
	public function resize(int $width = 0, int $height = 0, string $default = '') : void {
		if (!$this->width || !$this->height) {
			return;
		}

		$xpos = 0;
		$ypos = 0;
		$scale = 1;

		$scale_w = $width / $this->width;
		$scale_h = $height / $this->height;

		if ($default == 'w') {
			$scale = $scale_w;
		} elseif ($default == 'h') {
			$scale = $scale_h;
		} else {
			$scale = min($scale_w, $scale_h);
		}

		if ($scale == 1 && $scale_h == $scale_w && ($this->mime != 'image/png' || $this->mime != 'image/webp')) {
			return;
		}

		$new_width = (int)($this->width * $scale);
		$new_height = (int)($this->height * $scale);
		$xpos = (int)(($width - $new_width) / 2);
		$ypos = (int)(($height - $new_height) / 2);

		$image_old = $this->image;
		$this->image = imagecreatetruecolor($width, $height);

		if ($this->mime == 'image/png') {
			imagealphablending($this->image, false);
			imagesavealpha($this->image, true);

			$background = imagecolorallocatealpha($this->image, 255, 255, 255, 127);

			imagecolortransparent($this->image, $background);

		} elseif ($this->mime == 'image/webp') {
			imagealphablending($this->image, false);
			imagesavealpha($this->image, true);

			$background = imagecolorallocatealpha($this->image, 255, 255, 255, 127);

			imagecolortransparent($this->image, $background);
		} else {
			$background = imagecolorallocate($this->image, 255, 255, 255);
		}

		imagefilledrectangle($this->image, 0, 0, $width, $height, $background);

		imagecopyresampled($this->image, $image_old, $xpos, $ypos, 0, 0, $new_width, $new_height, $this->width, $this->height);
		imagedestroy($image_old);

		$this->width = $width;
		$this->height = $height;
		//return $this;
	}
	
	/* public function resize(int $width = 0, int $height = 0, string $default = ''): self {
        if ($width <= 0 || $height <= 0) {
            throw new \InvalidArgumentException('Invalid dimensions');
        }

        $newImage = imagecreatetruecolor($width, $height);

        // Handle transparency
        if ($this->mime === 'image/png' || $this->mime === 'image/webp') {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefilledrectangle($newImage, 0, 0, $width, $height, $transparent);
        } else {
            $white = imagecolorallocate($newImage, 255, 255, 255);
            imagefilledrectangle($newImage, 0, 0, $width, $height, $white);
        }

        imagecopyresampled($newImage, $this->image, 0, 0, 0, 0, $width, $height, $this->width, $this->height);
        imagedestroy($this->image);
        $this->image = $newImage;
        $this->width = $width;
        $this->height = $height;

        return $this;
    } */

	
	public function watermark(\Opencart\System\Library\Image $watermark, string $position = 'bottomright'): void {
		switch ($position) {
			case 'topleft':
				$watermark_pos_x = 0;
				$watermark_pos_y = 0;
				break;
			case 'topcenter':
				$watermark_pos_x = (int)(($this->width - $watermark->getWidth()) / 2);
				$watermark_pos_y = 0;
				break;
			case 'topright':
				$watermark_pos_x = ($this->width - $watermark->getWidth());
				$watermark_pos_y = 0;
				break;
			case 'middleleft':
				$watermark_pos_x = 0;
				$watermark_pos_y = (int)(($this->height - $watermark->getHeight()) / 2);
				break;
			case 'middlecenter':
				$watermark_pos_x = (int)(($this->width - $watermark->getWidth()) / 2);
				$watermark_pos_y = (int)(($this->height - $watermark->getHeight()) / 2);
				break;
			case 'middleright':
				$watermark_pos_x = ($this->width - $watermark->getWidth());
				$watermark_pos_y = (int)(($this->height - $watermark->getHeight()) / 2);
				break;
			case 'bottomleft':
				$watermark_pos_x = 0;
				$watermark_pos_y = ($this->height - $watermark->getHeight());
				break;
			case 'bottomcenter':
				$watermark_pos_x = (int)(($this->width - $watermark->getWidth()) / 2);
				$watermark_pos_y = ($this->height - $watermark->getHeight());
				break;
			case 'bottomright':
				$watermark_pos_x = ($this->width - $watermark->getWidth());
				$watermark_pos_y = ($this->height - $watermark->getHeight());
				break;
		}

		imagealphablending($this->image, true);
		imagesavealpha($this->image, true);
		imagecopy($this->image, $watermark->getImage(), $watermark_pos_x, $watermark_pos_y, 0, 0, $watermark->getWidth(), $watermark->getHeight());

		imagedestroy($watermark->getImage());
	}

	
	public function crop(int $top_x, int $top_y, int $bottom_x, int $bottom_y): void {
		$image_old = $this->image;
		$this->image = imagecreatetruecolor($bottom_x - $top_x, $bottom_y - $top_y);

		imagecopy($this->image, $image_old, 0, 0, $top_x, $top_y, $this->width, $this->height);
		imagedestroy($image_old);

		$this->width = $bottom_x - $top_x;
		$this->height = $bottom_y - $top_y;
	}

	
	public function rotate(int $degree, string $color = 'FFFFFF'): void {
		$rgb = $this->html2rgb($color);

		$this->image = imagerotate($this->image, $degree, imagecolorallocate($this->image, $rgb[0], $rgb[1], $rgb[2]));

		$this->width = imagesx($this->image);
		$this->height = imagesy($this->image);
	}
	
	public function processBatch(array $files, string $outputDir) {
		foreach ($files as $file) {
			try {
				$this->start($file);
				$this->resize($this->config->width, $this->config->height);
				$this->save($outputDir . basename($file));
			} catch (\Exception $e) {
				$this->log->write('Image processing failed: ' . $e->getMessage());
			}
		}
	}

	
	private function filter(): void {
		$args = func_get_args();

		call_user_func_array('imagefilter', $args);
	}

	
	private function text(string $text, int $x = 0, int $y = 0, int $size = 5, string $color = '000000'): void {
		$rgb = $this->html2rgb($color);

		imagestring($this->image, $size, $x, $y, $text, imagecolorallocate($this->image, $rgb[0], $rgb[1], $rgb[2]));
	}

	
	private function merge(object $merge, int $x = 0, int $y = 0, int $opacity = 100): void {
		imagecopymerge($this->image, $merge->getImage(), $x, $y, 0, 0, $merge->getWidth(), $merge->getHeight(), $opacity);
	}

	
	private function html2rgb(string $color): array {
		if ($color[0] == '#') {
			$color = substr($color, 1);
		}

		if (strlen($color) == 6) {
			[$r, $g, $b] = [$color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]];
		} elseif (strlen($color) == 3) {
			[$r, $g, $b] = [$color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]];
		} else {
			return [];
		}

		$r = hexdec($r);
		$g = hexdec($g);
		$b = hexdec($b);

		return [$r, $g, $b];
	}
	
	private function sanitizeFilename(string $filename): string {
		return preg_replace('/[^a-zA-Z0-9\-\._]/', '', $filename);
	}
	
	public function __destruct() {
        if (is_resource($this->image)) {
            imagedestroy($this->image);
        }
    }
}
