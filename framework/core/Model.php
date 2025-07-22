<?php

namespace Framework\Core;

class Model
{
    protected $registry;
	protected $loadedModels = [];
	protected $config;
	protected $db;
    protected $image;
	protected $log;
	protected $cache;
	protected $qr;
	protected $request;
	protected $response;
	
    public function __construct(Registry $registry){
        $this->registry = $registry;
		$this->config = $this->registry->get("config");
        $this->db = $this->registry->get("db");
		$this->image = $registry->get('image');
		$this->log = $registry->get('log');
		$this->cache = $registry->get('cache');
		$this->qr = $registry->get('qr');
		$this->request = $registry->get('request');
		$this->response = $registry->get('response');
		
    }
	
	/**
     * Load a model instance
     * @param string $modelPath Format: 'group/model_name' (e.g., 'blog/post')
     * @return object The loaded model instance
     */
    public function load(string $modelPath) {
        // Check if model is already loaded
        if (isset($this->loadedModels[$modelPath])) {
            return $this->loadedModels[$modelPath];
        }

        $parts = explode('/', $modelPath);
        if (count($parts) !== 2) {
            throw new \InvalidArgumentException('Invalid model path format. Use "group/model"');
        }

        [$group, $model] = $parts;
        $modelClassName = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', ucfirst($group) . ucfirst($model));
        $modelFile = DIR_APP . "model/{$group}/{$model}.php";

        if (!file_exists($modelFile)) {
            throw new \RuntimeException("Model file not found: {$modelFile}");
        }

        require_once($modelFile);

        if (!class_exists($modelClassName)) {
            throw new \RuntimeException("Model class {$modelClassName} not found");
        }

        $modelInstance = new $modelClassName($this->registry);
        $this->loadedModels[$modelPath] = $modelInstance;

        return $modelInstance;
    }
	
	public function __get(string $key) {
		return $this->registry->get($key);
	}

	public function __set(string $key, $value) {
		$this->registry->set($key, $value);
	}


	public function beginTransaction(): bool {
		return $this->db->beginTransaction();
	}

	public function commit(): bool {
		return $this->db->commit();
	}

	public function rollBack(): bool {
		return $this->db->rollBack();
	}
}