<?php
namespace Framework\Core;
class Registry {
	
	private array $data = [];

	public function get(string $key): object|null {
		return isset($this->data[$key]) ? $this->data[$key] : null;
	}

	/* public function set(string $key, object $value) {
		$this->data[$key] = $value;
	} */
	
	public function set(string $key, object $value, string $interface = null) {
		if ($interface && !$value instanceof $interface) {
			throw new \InvalidArgumentException("set failed");
		}
		$this->data[$key] = $value;
	}
	
	public function factory(string $key, callable $factory) {
		$this->data[$key] = fn() => $factory($this);
	}
	
	public function offsetExists($offset): bool {
		return $this->has($offset);
	}
	
	public function has(string $key) {
		return isset($this->data[$key]);
	}

	public function unset(string $key) {
		if (isset($this->data[$key])) {
			unset($this->data[$key]);
		}
	}
}
