<?php

class Router extends \Framework\Core\Controller
{
    public function Route($url)
    {
        $url = trim($url, '/');
        $parts = explode('/', $url);
        
        $route = !empty($parts[0]) ? array_shift($parts) : 'home/dashboard';
        
        // Whitelist of allowed routes to prevent directory traversal
        $allowedRoutes = [
            'blog/posts',
            'blog/posts_groups',
            'home/dashboard',
            'inc/header',
            'inc/footer',
            'inc/navbar',
            'inc/paginator',
            'login',
            'posts'
        ];
        
        if (!in_array($route, $allowedRoutes)) {
            // Check for partial matches, e.g. "posts" instead of "blog/posts"
            foreach ($allowedRoutes as $allowedRoute) {
                if (str_ends_with($allowedRoute, '/' . $route) || $allowedRoute === $route) {
                    $route = $allowedRoute;
                    break;
                }
            }

            if (!in_array($route, $allowedRoutes)) {
                http_response_code(404);
                echo "Error: Page not found.";
                exit;
            }
        }
        
        $action = !empty($parts[0]) ? array_shift($parts) : 'index';
        
        if ($route === 'posts') {
            $route = 'blog/posts';
        }
        // Construct the full path to the controller file
        $controllerFile = __DIR__ . '/' . $route . '/Controller.php';

        if (file_exists($controllerFile)) {
            require_once($controllerFile);
            
            $classNameParts = explode('/', $route);
            $classNameParts = array_map('ucfirst', $classNameParts);
            $controllerClassName = 'Controller' . implode('', $classNameParts);
            
            if (class_exists($controllerClassName)) {
                $controller = new $controllerClassName($this->registry);
                
                if (method_exists($controller, $action)) {
                    $controller->$action();
                } else {
                     // Default to 'index' if the action doesn't exist
                    if (method_exists($controller, 'index')) {
                        $controller->index();
                    } else {
                        http_response_code(404);
                        echo "Error: Action '{$action}' not found in controller '{$controllerClassName}'.";
                        exit;
                    }
                }
            } else {
                http_response_code(404);
                echo "Error: Controller class '{$controllerClassName}' not found in file '{$controllerFile}'.";
                exit;
            }
        } else {
             // Fallback to the old router logic for backwards compatibility
            $this->fallbackRoute($url);
        }
    }

    private function fallbackRoute($url) {
        $paths = [
			"posts"			=>		"blog/posts",
			"dashboard"		=>		"home/dashboard"
		];
		$methods = [
			"add"			=>		"New",
			"edit"			=>		"Change",
			"delete"		=>		"Drop",
			"status"		=>		"Status",
			"modal"			=>		"ManageModals"
		];

        if (isset ($url) and $url != "dashboard")
		{
			$url = explode("/",rtrim($url,"/"));
			if(isset ($url[0]))
			{
				$controllerName = (string) $url[0] ."Controller";
					if(file_exists("controller/".$this->getRouts($url[0], $paths).".php"))
					{
						require_once ("controller/".$this->getRouts($url[0], $paths).".php");
							$dispatch = new $controllerName($this->registry);
								unset($url[0]);
								
						if(isset ($url[1]))
						{
							$method = $this->getMethods((string) strtolower($url[1]), $methods) ;
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
			
			require_once ("controller/".$this->getRouts($url, $paths).".php");
			$dashboard = new DashboardController($this->registry);
			$dashboard->index();
			
		}
    }

    public function getRouts(string $key, array $paths){
		return isset($paths[$key]) ? $paths[$key] : null;
	}
	
	public function getMethods(string $key, array $methods){
		return isset($methods[$key]) ? $methods[$key] : null;
	}
}
