<?php
class Msg {
	public static function getMessage($key) {
		global $LANG;
		Util::includeAllFromFolder('src/lang', [mb_strtolower($LANG)], true);
		$lang = _getLangs();
		return $lang[$key] ?? "!$key";
	}
}
