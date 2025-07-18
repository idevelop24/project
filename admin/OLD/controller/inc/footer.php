<?php

class FooterController extends \Framework\Core\AdminBaseController {
	
	public function index():array{
        
		$data["copyright"] ="EWcms";
		
		$javascript = $this->document->getScripts();
		foreach ($javascript as $js) 
		{
			$data["javascript"][] = [
				"href" => $js["href"]
			];
		}
			return $data ;
	}
}

?>