<?php

class NavbarController extends \Framework\Core\AdminBaseController {
	
	public function index():array{
		$this->loadModel("blog/posts");
		$this->loadModel("blog/posts_categories");
		$data["posts_count"] =$this->model_blog_posts->countRows();
		$data["posts_cats_count"] =$this->model_blog_posts_categories->countRows();
	   return $data ;
	}
}

?>