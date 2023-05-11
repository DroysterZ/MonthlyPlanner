<?php
// http://projeto.monthlyplanner.local/index.php?action=src/_scripts/_init.php?a=delete
// http://projeto.monthlyplanner.local/index.php?action=src/_scripts/_init.php?a=create

$dao = new Database();

$indices = [];

$indices["listas"] = [
	"mappings" => [
		"properties" => [
			"itens" => [
				"type" => "text",
				"fields" => [
					"keyword" => [
						"type" => "keyword",
						"ignore_above" => 256
					]
				]
			]
		]
	]
];

$indices["item"] = [
	"mappings" => [
		"properties" => [
			"nome" => [
				"type" => "text",
				"fields" => [
					"keyword" => [
						"type" => "keyword",
						"ignore_above" => 256
					]
				]
			],
			"preco" => [
				"type" => "float"
			]
		]
	]
];

// $indices["loja"] = [
// 	"mappings" => [
// 		"properties" => [
// 			"id" => [
// 				"type" => "integer"
// 			],
// 			"nome" => [
// 				"type" => "text",
// 				"fields" => [
// 					"keyword" => [
// 						"type" => "keyword",
// 						"ignore_above" => 256
// 					]
// 				]
// 			],
// 			"local" => [
// 				"type" => "text",
// 				"fields" => [
// 					"keyword" => [
// 						"type" => "keyword",
// 						"ignore_above" => 256
// 					]
// 				]
// 			]
// 		]
// 	]
// ];

// $indices["compras"] = [
// 	"mappings" => [
// 		"properties" => [
// 			"id" => [
// 				"type" => "integer"
// 			],
// 			"data" => [
// 				"type" => "date"
// 			],
// 			"itens" => [
// 				"type" => "integer"
// 			],
// 			"loja" => [
// 				"type" => "integer"
// 			],
// 			"valor" => [
// 				"type" => "float"
// 			],
// 			"recorrencia" => [
// 				"type" => "boolean"
// 			]
// 		]
// 	]
// ];

// $indices["oferta"] = [
// 	"mappings" => [
// 		"properties" => [
// 			"id" => [
// 				"type" => "integer"
// 			],
// 			"item" => [
// 				"type" => "integer"
// 			],
// 			"loja" => [
// 				"type" => "integer"
// 			],
// 			"link" => [
// 				"type" => "text",
// 				"fields" => [
// 					"keyword" => [
// 						"type" => "keyword",
// 						"ignore_above" => 256
// 					]
// 				]
// 			],
// 		]
// 	]
// ];

// $indices["recorrencias"] = [
// 	"mappings" => [
// 		"properties" => [
// 		]
// 	]
// ];


foreach ($indices as $index => $parameters) {
	$params = [];
	$params['index'] = $index;

	try {
		if ($_REQUEST["a"] == "delete") {
			$dao->getClient()->indices()->delete($params);
		} else if ($_REQUEST["a"] == "create") {
			$params['body'] = $parameters;
			$dao->getClient()->indices()->create($params);
		}
	} catch (Exception $e) {
	}
}
