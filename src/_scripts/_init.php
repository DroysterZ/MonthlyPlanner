<?php

$dao = new Database();

$indices = [];

$indices["item"] = [
	"mappings" => [
		"properties" => [
			"id" => [
				"type" => "integer"
			],
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
			],
			"recorrente" => [
				"type" => "boolean"
			]
		]
	]
];

$indices["loja"] = [
	"mappings" => [
		"properties" => [
			"id" => [
				"type" => "integer"
			],
			"nome" => [
				"type" => "text",
				"fields" => [
					"keyword" => [
						"type" => "keyword",
						"ignore_above" => 256
					]
				]
			],
			"local" => [
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

$indices["compra"] = [
	"mappings" => [
		"properties" => [
			"id" => [
				"type" => "integer"
			],
			"data" => [
				"type" => "date"
			],
			"itens" => [
				"type" => "integer"
			],
			"loja" => [
				"type" => "integer"
			]
		]
	]
];

$indices["oferta"] = [
	"mappings" => [
		"properties" => [
			"id" => [
				"type" => "integer"
			],
			"item" => [
				"type" => "integer"
			],
			"loja" => [
				"type" => "integer"
			],
			"link" => [
				"type" => "text",
				"fields" => [
					"keyword" => [
						"type" => "keyword",
						"ignore_above" => 256
					]
				]
			],
		]
	]
];

foreach ($indices as $index => $parameters) {
	$params = [];
	$params['index'] = $index;
	try {
		$dao->getClient()->indices()->delete($params);
	} catch (Exception $e) {
	}

	$params['body'] = $parameters;
	$dao->getClient()->indices()->create($params);
}
