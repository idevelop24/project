<?php
namespace Framework\Core;

class AdminBaseModel extends Model {
	
	protected $admin;
    public function __construct(Registry $registry)
    {
        parent::__construct($registry);
        $this->admin = $this->registry->get("admin");
    }
	
    /**
     * Admin-specific model loading with permission check
     */
    public function load(string $modelPath, bool $checkPermission = true) {
        $model = parent::load($modelPath);

        if ($checkPermission && !$this->admin->hasPermission("model/{$modelPath}")) {
            throw new \RuntimeException('You do not have permission to access this model');
        }
        return $model;
    }

    /**
     * Log admin activity
     */
    protected function logActivity(string $action, array $data = []) {
		$this->log->write([
			'admin_id' => $this->admin->getId(), // Changed $admin to $this->admin
			'action' => $action,
			'data' => $data
		]);
	}
	
	protected function validateAdminAccess(string $permission): bool {
		if (!$this->admin->isLogged()) {
			throw new \RuntimeException('Authentication required');
		}
		return $this->admin->hasPermission($permission);
	}
}