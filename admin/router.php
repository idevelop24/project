<?php


class Router extends \Framework\Core\Controller
{
	private $paths;
	private $methods;
	public function __construct($registry){
		parent::__construct($registry);
		
		$this->paths = 
		[
			"posts"			=>		"blog/posts",
			"dashboard"		=>		"home/dashboard"
		];
		$this->methods = 
		[
			"add"			=>		"New",
			"edit"			=>		"Change",
			"delete"		=>		"Drop",
			"status"		=>		"Status",
			"modal"			=>		"ManageModals"
		];
	}
	
	public function Route($url){
		if (isset ($url) and $url != "dashboard")
		{
			$url = explode("/",rtrim($url,"/"));
			if(isset ($url[0]))
			{
				$controllerName = (string) $url[0] ."Controller";
					if(file_exists("controller/".$this->getRouts($url[0]).".php"))
					{
						require_once ("controller/".$this->getRouts($url[0]).".php");
							$dispatch = new $controllerName($this->registry);
								unset($url[0]);
								
						if(isset ($url[1]))
						{
							$method = $this->getMethods((string) strtolower($url[1])) ;
							$dispatch->$method();
						}
						else{
							$dispatch->index();
						}
					}
					else
						throw new \Exception("Class '$controllerName' does not exist.");
			}
		}
		else
		{
			
			require_once ("controller/".$this->getRouts($url).".php");
			$dashboard = new DashboardController($this->registry);
			$dashboard->index();
			
		}
		
			
	}
	
	public function getRouts(string $key){
		return isset($this->paths[$key]) ? $this->paths[$key] : null;
	}
	
	public function getMethods(string $key){
		return isset($this->methods[$key]) ? $this->methods[$key] : null;
	}
}