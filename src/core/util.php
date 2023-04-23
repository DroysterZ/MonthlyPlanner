<?php

class Util {
	/**
	 * @param Mixed $data
	 * @param Boolean $exit If TRUE, call exit()
	 * @return String
	 * 
	 * ** Only for debug purposes **
	 * This function prints $data in human readable form
	 */
	public static function print($data, $exit = false) {
		echo "<pre>";
		print_r($data);
		echo "</pre>";
		if ($exit) {
			exit();
		}
	}

	/**
	 * @param String $path Path to folder
	 * @param Array $files (Optional) Files to ignore
	 * @param Bool $strict (Optional) If TRUE, import ONLY files specified in $files
	 * 
	 * Calls include_once for all PHP files in a given folder
	 */
	public static function includeAllFromFolder($path, $files = [], $strict = false) {
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

	/**
	 * @param String $msg Message to show, if not specified please send $debug_backtrace
	 * @param Array $debug_backtrace Debug info = debug_backtrace()
	 * @return String
	 * 
	 * Throw a exception with the given $msg or $debug_backtrace
	 */
	public static function throwException($msg = null, $debug_backtrace = null) {
		if ($msg) {
			throw new Exception($msg);
		} else {
			$bt = $debug_backtrace;
			$caller = array_shift($bt);
			throw new Exception("Call for undefined function: " . get_class($caller['object']) . "::" . $caller['args'][0] . " on file " . $caller['file'] . " on line " . $caller['line']);
		}
	}
}
