<?php
class ControllerLogin extends \Framework\Core\AdminBaseController {
    public function index() {
        $this->response->setOutput($this->load->view('login/view/login'));
	}

    public function submit() {
        $this->load->library('Admin');
        if ($this->admin->login($this->request->post['username'], $this->request->post['password'])) {
            $this->response->redirect($this->url->link('home/dashboard', '', true));
        } else {
            $this->session->data['error'] = 'Invalid login credentials.';
            $this->response->redirect($this->url->link('login', '', true));
        }
    }

    public function logout() {
        $this->load->library('Admin');
        $this->admin->logout();
        $this->response->redirect($this->url->link('login'));
    }
}
