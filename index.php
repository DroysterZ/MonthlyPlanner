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
	include_once ROOT . $_REQUEST["action"];
} else {
	include_once ROOT . "view/index.php";
}

if ($view) {
	include_once ROOT . $view;
} else {
	echo "ERRO 404";
}
