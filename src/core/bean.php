<?php
class Bean {
	private $_id;
	private $_table;
	private $_dynamicFields = [];

	public function getTable() {
		return $this->_table;
	}

	public function getId() {
		return $this->_id;
	}
	public function setId($v) {
		$this->_id = $v;
	}

	function __construct($table) {
		$this->_table = $table;
		$dao = new DAO();
		switch ($dao->getType()) {
			case 'elasticsearch':
				$fields = $dao->getClient()->indices()->getMapping(['index' => [$table]]);
				$fields = $fields[$table]['mappings']['properties'];

				$data = [];
				foreach ($fields as $k => $v) {
					$data[$k] = null;
				}
				$this->populate($data);
				break;

			default:
				break;
		}
	}

	public function __call($name, $arguments) {
		$op = substr($name, 0, 3);
		$field = lcfirst(substr($name, 3));

		if ($op == 'get') {
			if (array_key_exists($field, $this->_dynamicFields)) {
				return $this->_dynamicFields[$field];
			} else {
				throwException(null, debug_backtrace());
			}
		} else if ($op == 'set') {
			if (array_key_exists($field, $this->_dynamicFields)) {
				$this->_dynamicFields[$field] = array_shift($arguments);
			} else {
				throwException(null, debug_backtrace());
			}
		} else {
			throwException(null, debug_backtrace());
		}
	}

	/**
	 * @param Array $data Key-value array to populate bean
	 * 
	 * Populate bean with a specified array
	 */
	public function populate($data) {
		foreach ($data as $k => $v) {
			$method = "set" . ucfirst($k);
			if (method_exists($this, $method)) {
				$this->$method($v);
			} else {
				$this->_dynamicFields[$k] = $v;
			}
		}
	}

	/**
	 * @return Array
	 * 
	 * Convert to array
	 */
	public function toArray() {
		$data = [];
		$vars = (new ReflectionClass($this))->getProperties(ReflectionProperty::IS_PRIVATE);

		foreach ($vars as $v) {
			if (substr($v, 0, 1) != "_" && isset($this->$v['name'])) {
				$data[$v['name']] = $this->$v['name'];
			}
		}

		foreach ($this->_dynamicFields as $k => $v) {
			$data[$k] = $v;
		}

		return $data;
	}
}
