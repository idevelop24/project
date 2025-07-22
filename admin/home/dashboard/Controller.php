<?php
class ControllerHomeDashboard extends AdminBaseController {
    
    public function grid() {
        // Load model
        $this->load->model('blog/posts');
        
        // Get posts data
        $data['posts'] = $this->model_blog_posts->getPosts();
        
        // Load views with common elements
        $this->document->setTitle('Posts Grid');
        
        $data['header'] = $this->load->controller('inc/header');
        $data['navbar'] = $this->load->controller('inc/navbar');
        $data['footer'] = $this->load->controller('inc/footer');
        
        $this->response->setOutput($this->load->view('blog/grid', $data));
    }
    
    public function add() {
        // Similar structure for add action
    }
    
    public function edit() {
        // Similar structure for edit action
    }
}