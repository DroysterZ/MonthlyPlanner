<?php
class DAO extends Database {
	public function insert(&$bean) {
		$ret = $this->validate($bean, 'insert');
		if ($ret->getReturn()) {
			switch ($this->getType()) {
				case 'elasticsearch':
					$data = $bean->toArray();
					$params['index'] = $bean->getTable();
					$params['body'] = $data;
					if ($bean->getId()) {
						$params['id'] = $bean->getId();
					}

					try {
						$retInsert = $this->getClient()->index($params);
						$bean->setId($retInsert['_id']);
						$ret->setReturn(true);
						$ret->addMsg(Msg::getMessage('success_insert'));
						$ret->addData($retInsert, 'insert_response');
					} catch (Exception $e) {
						$ret->setReturn(false);
						$ret->addMsg(Msg::getMessage('fail_insert'));
						$ret->addMsg($e->getMessage());
					}
					break;
			}
		}
		return $ret;
	}

	public function select(&$bean, $parameters = []) {
		$ret = $this->validate($bean, 'select');
		if ($ret->getReturn()) {
			$query = $this->selectSQL($bean, $parameters);

			switch ($this->getType()) {
				case 'elasticsearch':
					$ret = $this->getClient()->search($query);
					break;
			}
			return $ret;
		}
	}

	public function selectSQL(&$bean, $parameters = []) {
		switch ($this->getType()) {
			case 'elasticsearch':
				$query = [];
				$query['index'] = $bean->getTable();
				if ($bean->getId()) {
					$ids = $bean->getId();
					if (!is_array($ids)) {
						$ids = [$ids];
					}

					$query['body']['query']['ids']['values'] = $ids;
				} else {
					foreach ($bean->toArray() as $k => $v) {
						if (!is_null($v)) {
							$query['body']['query']['bool']['must'][]['match'][$k] = $v;
						}
					}
				}

				return $query;
				break;
		}
	}

	public function createMapFromResultSet($rst) {
		switch ($this->getType()) {
			case 'elasticsearch':
				$results = $rst["hits"]["hits"];
				$map = [];
				foreach ($results as $data) {
					$map[$data["_id"]] = $data["_source"];
				}
				return $map;
				break;
		}
	}

	public function update(&$bean) {
		$ret = $this->validate($bean, 'update');
		if ($ret->getReturn()) {
			switch ($this->getType()) {
				case 'elasticsearch':
					$data = $bean->toArray();
					$params['index'] = $bean->getTable();
					$params['id'] = $bean->getId();
					$params['body'] = $data;

					try {
						$retUpdate = $this->getClient()->index($params);
						$ret->setReturn(true);
						$ret->addMsg(Msg::getMessage('success_update'));
						$ret->addData($retUpdate, 'update_response');
					} catch (Exception $e) {
						$ret->setReturn(false);
						$ret->addMsg(Msg::getMessage('fail_update'));
						$ret->addMsg($e->getMessage());
					}
					break;
			}
		}
		return $ret;
	}

	public function delete(&$bean) {
		$ret = $this->validate($bean, 'delete');
		if ($ret->getReturn()) {
			switch ($this->getType()) {
				case 'elasticsearch':
					$params['index'] = $bean->getTable();
					$params['id'] = $bean->getId();
					try {
						$retDelete = $this->getClient()->delete($params);
						$ret->setReturn(true);
						$ret->addMsg(Msg::getMessage('success_delete'));
						$ret->addData($retDelete, 'delete_response');
					} catch (Exception $e) {
						$ret->setReturn(false);
						$ret->addMsg(Msg::getMessage('fail_delete'));
						$ret->addMsg($e->getMessage());
					}
					break;
			}
		}
		return $ret;
	}

	public function validate(&$bean, $action = null) {
		$ret = new Returner();
		$ret->setReturn(true);

		$compatibility = ['elasticsearch'];
		if (!in_array($this->getType(), $compatibility)) {
			$ret->setReturn(false);
			$ret->addMsg("error_db_type_undefined");
		}

		if ($action == 'insert') {
			if ($this->getType() == "elasticsearch") {
				if ($bean->getId()) {
					$ret->setReturn(false);
					$ret->addMsg(Msg::getMessage('error_es_id_on_insert'));
				}
			}
		}

		if ($action == 'select') {
		}

		if ($action == 'update') {
			if (!$bean->getId()) {
				$ret->setReturn(false);
				$ret->addMsg(Msg::getMessage('id_needed'));
			}
		}

		if ($action == 'delete') {
			if (!$bean->getId()) {
				$ret->setReturn(false);
				$ret->addMsg(Msg::getMessage('id_needed'));
			}
		}

		return $ret;
	}

	public function mapResult($result) {
		$newResult = [];
		switch ($this->getType()) {
			case 'elasticsearch':
				$dataset = $result['hits']['hits'];
				foreach ($dataset as $data) {
					$temp = $data['_source'];
					$temp['_id'] = $data['_id'];
					$newResult[] = $temp;
				}
				break;
		}

		return $newResult;
	}
}

class Database {
	private $type;
	private $client;
	private $conn = false;

	public function getType() {
		return strtolower($this->type);
	}
	public function setType($v) {
		$this->type = $v;
	}

	public function getClient() {
		return $this->client;
	}
	public function setClient($v) {
		$this->client = $v;
	}

	public function getConn() {
		return $this->conn;
	}
	public function setConn($v) {
		$this->conn = $v;
	}

	function __construct($host = null, $port = null, $user = null, $pass = null, $type = null) {
		global $INI;
		$dbParams = $INI['DATABASE'];

		$host = $host ?? $dbParams['HOST'];
		$port = $port ?? $dbParams['PORT'];
		$user = $user ?? $dbParams['USER'];
		$pass = $pass ?? $dbParams['PASS'];

		$type = $type ?? $dbParams['TYPE'];
		$this->setType(mb_strtolower($type));

		switch ($this->getType()) {
			case 'elasticsearch':
				require_once 'src/libs/elasticsearch/vendor/autoload.php';
				try {
					$this->setClient(Elasticsearch\ClientBuilder::create()->setHosts([$this->strConn($host, $port, $user, $pass)])->build());
					$this->setConn(true);
				} catch (Exception $e) {
					$this->setClient($e->getMessage());
				}
				break;

			default:
				$this->setClient(false);
				break;
		}
	}

	function strConn($host, $port, $user, $pass) {
		global $INI;
		$dbParams = $INI['DATABASE'];
		switch ($this->getType()) {
			case 'elasticsearch':
				$method = $dbParams['SECURE'] ? "https" : "http";
				$strConn = "$method://$user:$pass@$host:$port";
				return $strConn;
				break;

			default:
				return "";
				break;
		}
	}
}
