<?php

namespace Framework\Core;

class Controller
{
    protected $registry;
	protected $config;
	protected $db;
    protected $request;
	protected $response;
    
    public  function __construct( Registry $registry){
        $this->registry = $registry;
		$this->config = $this->registry->get("config");
        $this->db = $this->registry->get("db");
		$this->request = $this->registry->get("request");
		$this->response = $registry->get('response');
    }
	
	/**
     * Redirect helper
     */
    protected function redirect(string $url, int $statusCode = 302): void {
        $this->response->redirect($url, $statusCode);
    }

    /**
     * Magic getter for registry items
     */
    public function __get(string $key) {
        return $this->registry->get($key);
    }

    /**
     * Magic setter for registry items
     */
    public function __set(string $key, $value) {
        $this->registry->set($key, $value);
    }
}