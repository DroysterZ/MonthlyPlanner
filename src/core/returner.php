<?php
class Returner {
	private $return;
	private $msg;
	private $data;

	public function getReturn() {
		return $this->return;
	}
	public function setReturn($v) {
		$this->return = $v;
	}

	public function getMsg() {
		return $this->msg;
	}
	public function addMsg($v, $id = null) {
		if ($id) {
			$this->msg[$id] = $v;
		} else {
			$this->msg[] = $v;
		}
	}
	public function removeMsg($id) {
		unset($this->msg[$id]);
	}

	public function getData() {
		return $this->data;
	}
	public function addData($v, $id = null) {
		if ($id) {
			$this->data[$id] = $v;
		} else {
			$this->data[] = $v;
		}
	}
	public function removeData($id) {
		unset($this->data[$id]);
	}
}
