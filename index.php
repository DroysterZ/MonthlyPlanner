<?php
define("ROOT", __DIR__ . "/");
global $INI;
$INI = parse_ini_file(ROOT . 'config.ini', true);

global $LANG;
$LANG = $INI['LANG'] ?? 'ptbr';

include_once ROOT . 'src/core/util.php';
Util::includeAllFromFolder(ROOT . 'src/core', ['util']);

if (isset($_REQUEST["action"])) {
	include_once ROOT . $_REQUEST["action"];
}
