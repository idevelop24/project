<?php
class ControllerBlogPosts extends \Framework\Core\AdminBaseController {
    
    public function index() {
        $this->grid();
    }

    public function grid() {
        $this->load->model('blog/posts');
        $this->load->library('Paginator');

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $data['posts'] = array();

        $filter_data = array(
            'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'                  => $this->config->get('config_limit_admin')
        );

        $post_total = $this->model_blog_posts->getTotalPosts();

        $results = $this->model_blog_posts->getPosts($filter_data);

        foreach ($results as $result) {
            $data['posts'][] = array(
                'id'        => $result['id'],
                'title'     => $result['title'],
                'category_name'      => $result['category_name'],
                'sort_order' => $result['sort_order'],
                'edit'      => $this->url->link('blog/posts/edit', 'id=' . $result['id']),
                'delete'    => $this->url->link('blog/posts/delete', 'id=' . $result['id'])
            );
        }

        $pagination = new Paginator();
        $pagination->total = $post_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('blog/posts', 'page={page}');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($post_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($post_total - $this->config->get('config_limit_admin'))) ? $post_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $post_total, ceil($post_total / $this->config->get('config_limit_admin')));

        $this->document->setTitle('Posts Grid');
        
        $this->render('blog/posts/view/grid', $data);
    }
    
    public function add() {
        $this->load->model('blog/posts');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_blog_posts->addPost($this->request->post);

            $this->session->data['success'] = 'Post added successfully.';

            $this->response->redirect($this->url->link('blog/posts'));
        }

        $this->getForm();
    }

    protected function getForm() {
        $this->document->setTitle('Add New Post');
        $this->load->model('blog/posts_groups');
        $data['categories'] = $this->model_blog_posts_groups->getPostCategories();
        $this->render('blog/posts/view/add', $data);
    }

    protected function validateForm() {
        if (!isset($this->request->post['title']) || (utf8_strlen($this->request->post['title']) < 3) || (utf8_strlen($this->request->post['title']) > 100)) {
            $this->error['title'] = 'Title must be between 3 and 100 characters.';
        }

        if (!isset($this->request->post['content']) || (utf8_strlen($this->request->post['content']) < 3)) {
            $this->error['content'] = 'Content must be at least 3 characters.';
        }

        if (!isset($this->request->post['posts_categories_id'])) {
            $this->error['category'] = 'Please select a category.';
        }

        return !$this->error;
    }
    
    public function edit() {
        // Similar structure for edit action
    }
}
