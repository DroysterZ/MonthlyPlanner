<?php
class ListasBean extends Bean {
	private $nome;
	public function getNome() {
		return $this->nome;
	}
	public function setNome($v) {
		$this->nome = $v;
	}

	private $preco;
	public function getPreco() {
		return $this->preco;
	}
	public function setPreco($v) {
		$this->preco = $v;
	}
}

class ListasDAO extends DAO {
	public function selectLista(&$bean) {
		$bean->setId("GPc1CIgBi7GP_kZHtjrT");
		$map = $this->createMapFromResultSet($this->select($bean));

		$itemIds = [];
		foreach ($map as $found) {
			foreach ($found["itens"] as $id) {
				$itemIds[] = $id;
			}
		}

		$itemBean = new Bean("item");
		$itemBean->setId($itemIds);
		$itemMap = $this->createMapFromResultSet($this->select($itemBean));
		return $itemMap;
	}
}

class ListasAction {
	public function executeListaHome(&$bean, &$view) {
		$view = "view/actions/home.php";
		$dao = new ListasDAO();
		$data = $dao->selectLista($bean);
		return $data;
	}
}
