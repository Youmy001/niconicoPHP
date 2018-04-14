<?php
/**
 * Created by PhpStorm.
 * User: youmy
 * Date: 16/11/29
 * Time: 20:40
 */

namespace NiconicoPHP;


abstract class Enum {
	
	private static $constCacheArray = NULL;
	
	private function __construct(){
		/*
		  Preventing instance :)
		*/
	}
	
	private static function getConstants () {
		if (self::$constCacheArray == NULL) {
			self::$constCacheArray = [];
		}
		$calledClass = get_called_class();
		if (!array_key_exists($calledClass, self::$constCacheArray)) {
			$reflect = new \ReflectionClass($calledClass);
			self::$constCacheArray[$calledClass] = $reflect->getConstants();
		}
		return self::$constCacheArray[$calledClass];
	}
	
	public static function has ($value) {
		$values = array_values(self::getConstants());
		return in_array($value, $values, $strict = true);
	}
	
}