<?php
function debug($data, $exit = false) {
	echo "<pre>";
	print_r($data);
	echo "</pre>";
	if ($exit) {
		exit();
	}
}

function includeAllFromFolder($path, $files = [], $strict = false) {
	$handler = opendir($path);
	while ($file = readdir($handler)) {
		$filename = $path . "/" . $file;
		$pathinfo = pathinfo($filename);

		if (!is_dir($filename) && $pathinfo['extension'] == 'php') {
			$inArray = in_array($pathinfo['filename'], $files) || in_array($pathinfo['basename'], $files);
			if ($strict && $inArray) {
				include_once($filename);
			} else if (!$inArray) {
				include_once($filename);
			}
		}
	}
}

function throwException($msg = null, $debug_backtrace = null) {
	if ($msg) {
		throw new Exception($msg);
	} else {
		$bt = $debug_backtrace;
		$caller = array_shift($bt);
		throw new Exception("Call for undefined function: " . get_class($caller['object']) . "::" . $caller['args'][0] . " on file " . $caller['file'] . " on line " . $caller['line']);
	}
}

define("ROOT", __DIR__ . "/");
global $INI;
$INI = parse_ini_file(ROOT . 'config.ini', true);

global $LANG;
$LANG = $INI['LANG'] ?? 'ptbr';

global $view;

includeAllFromFolder(ROOT . 'src/core');

if (isset($_REQUEST["action"])) {
	$request = $_REQUEST["action"];
	$pathinfo = pathinfo($_REQUEST["action"]);

	$action = "";
	$filename = $pathinfo["filename"];
	if (strpos($filename, ".") !== false) {
		$class = substr($filename, 0, strpos($filename, "."));
		$action = substr($filename, strpos($filename, ".") + 1);
		$file = str_replace("." . $action, "", $pathinfo["basename"]);
		include_once ROOT . $pathinfo["dirname"] . "/" . $file;

		$classBean = ucfirst($class) . "Bean";
		$classAction = ucfirst($class) . "Action";

		if (class_exists($classBean)) {
			$objBean = new $classBean;
		} else {
			$objBean = new Bean($class);
		}
		$objAction = new $classAction;
		$objBean->populate($_REQUEST);
		
		$method = "execute" . ucfirst($action);
		$data = $objAction->$method($objBean, $view);
	} else {
		include_once ROOT . $request;
	}
}

if ($view) {
	include_once ROOT . $view;
} else {
	echo "ERRO 404";
}
