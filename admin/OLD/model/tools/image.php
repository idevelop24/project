<?php

class Image extends \Framework\Core\Model {
    
	private $images_directory;
	
    public function resize($filename, $sizes, $folder) {
        // Define the path to the original image
        $original_path = $this->images_directory . 'original/' . $filename;
        
        // Define the path to the resized image
        $resize_path = $this->images_directory . $folder . '/' . $filename;
		
		//extract width and height
		$sizes = explode ("_" , $sizes);
		$width = (int) $sizes[0];
		$height = (int) $sizes[1];

        // Ensure the original image exists before resizing
        if (is_file($original_path)) {
            // Use the main Image library to perform resizing
            $this->image->start($original_path);
            $this->image->resize($width, $height);
            $this->image->save($resize_path);
        } else {
            throw new \Exception('Error: Could not find the original image ' . $original_path);
        }
    }
	
	public function getAndConvertImageSize(array $sizes)
	{
		if(is_array ($sizes))
			return (string) $sizes[0].'_'.$sizes[1];
	}

    public function handleImageUpload($file, $directory) {
		
		if (is_dir($directory . 'original/'))
			$this->images_directory = $directory;
           
        // Validate the file extension
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        if (!in_array($file_extension, $this->config->get('admin_image_allowed_extentions'))) {
            throw new \Exception('Error: Invalid file extension.');
			exit();
        }
        
        // Generate a unique prefix
        $prefix = $this->generateUniquePrefix();
        $filename = $prefix . '_-_' . basename($file['name']);
        $original_path = $this->images_directory . 'original/' . $filename;
        

        
        if (move_uploaded_file($file['tmp_name'], $original_path)) {
            // Resize to various sizes
            $this->resize($filename, $this->getAndConvertImageSize($this->config->get('admin_image_size_vs')), 'vs');
			$this->resize($filename, $this->getAndConvertImageSize($this->config->get('admin_image_size_s')), 's');
			$this->resize($filename, $this->getAndConvertImageSize($this->config->get('admin_image_size_sg')), 'sg');
			$this->resize($filename, $this->getAndConvertImageSize($this->config->get('admin_image_size_g')), 'g');
			$this->resize($filename, $this->getAndConvertImageSize($this->config->get('admin_image_size_item')), 'item');
			/*$this->resize($filename, 450, 450, 'item'); */
            return $filename;
        }

        return false;
    }
	
	private function generateUniquePrefix() {
        $lowercase = range('a', 'z');
		$uppercase = range('A', 'Z');
        $alphabets = array_merge($lowercase, $uppercase);
		$numbers = rand(1,9999);
		$time = microtime(true);
        $str = $alphabets[rand(0,51)].$numbers.$time;
        return md5($str);
    }
	
	public function __destruct()
	{
		
	}
}

?>
