<?php

class DashboardController extends \Framework\Core\AdminBaseController {
	
	public function index(){
        
	   $this->document->setTitle("داشبورد");	
	   
	   $this->loadModel("home/dashboard");
	   
       $data["intro"] =  $this->model_home_dashboard->intro();
	   $data["name1"] = "Hamed";
	   $data["placeholder"] = "Load from controller";
	   $this->render("home/dashboard",$data);
	}
}

?>