<!DOCTYPE html>
<html lang="pt-BR">

<head>
	<base href="projeto.monthlyplanner.local">
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Monthly Planner</title>
	<link rel="stylesheet" href="view/src/bootstrap/css/bootstrap.min.css">
	<script src="view/src/bootstrap/js/bootstrap.min.js"></script>
</head>

<body>
	<table>
		<?php
		foreach ($data as $row) {
			$bean = new ListasBean();
			$bean->populate($row);
		?>
			<tr>
				<td style="border: 1px solid black"><?php echo $bean->getNome() ?></td>
				<td style="border: 1px solid black"><?php echo $bean->getPreco() ?></td>
			</tr>
		<?php } ?>
	</table>
</body>

</html>