<?php
include_once ROOT . "src/classes/listas.php";

class ItemDAO extends DAO {
	public function selectSQL($bean, $parameters = []) {
		$query = [];
		$query["index"] = $bean->getTable();

		if ($parameters["e"] == "selectItens") {
			$query["body"]["query"]["match_all"]["boost"] = 1;
		}

		return $query;
	}
	public function selectItens($bean) {
		$map = $this->createMapFromResultSet($this->select($bean, ["e" => "selectItens"]));
		return $map;
	}
}

class ItemAction {
	public function executeAddItem(&$bean, &$view) {
		$dao = new ItemDAO();
		$bean = new Bean("item");
		$bean->populate($_REQUEST);
		$dao->insert($bean);

		$listasAction = new ListasAction();
		return $listasAction->executeListaHome($bean, $view);
	}

	public function executelistaItem(&$bean, &$view) {
		$view = "view/actions/home.php";

		$dao = new ItemDAO();
		$bean = new Bean("item");
		$bean->populate($_REQUEST);
		$data = $dao->selectItens($bean);
		return $data;
	}
}
