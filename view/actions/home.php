<?php include_once ROOT . "view/parts/head.php" ?>

<body>
	<div class="container mx-auto">
		<div class="content">
			<table>
				<?php foreach ($data as $row) { ?>
					<?php $bean = new ListasBean(); ?>
					<?php $bean->populate($row); ?>
					<tr>
						<td><?php echo $bean->getNome() ?></td>
						<td><?php echo $bean->getPreco() ?></td>
					</tr>
				<?php } ?>
			</table>
		</div>
		<div>
			<form action="index.php" method="post">
				<input type="hidden" name="action" value="src/classes/item.addItem.php">
				<div>
					<label for="nome">Nome</label>
					<input type="text" name="nome" id="nome" />
				</div>
				<div>
					<label for="preco">Preço</label>
					<input type="number" name="preco" id="preco" />
				</div>
				<button type="submit">ADICIONAR</button>
			</form>
		</div>
		<div>
			<form action="index.php">
				<input type="hidden" name="action" value="src/classes/listas.addItem.php">
			</form>
		</div>
		<div class="content">
			<form action="index.php">
				<input type="hidden" name="action" value="src/classes/listas.listaHome.php">
				<?php
				$bean = new Bean("item");
				?>
				<input type="checkbox" name="item[]" id="item1" value="Item 1">
				<input type="checkbox" name="item[]" id="item2" value="Item 2">
				<input type="checkbox" name="item[]" id="item3" value="Item 3">
				<button type="submit">ADICIONAR</button>
			</form>
		</div>
	</div>
</body>

<?php include_once ROOT . "view/parts/foot.php" ?>