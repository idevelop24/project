<?php

class HeaderController extends \Framework\Core\AdminBaseController {
	
	public function index(){
		
		//read title
		$data["page_title"] = $this->document->getTitle();
		
		$styles = $this->document->getStyles();
		foreach ($styles as $css) 
		{
			$data["styles"][] = [
				"href" => $css["href"],
				"rel" => $css["rel"],
				"media" => $css["media"]
			];
		}
       return $data;
	}
}
?>