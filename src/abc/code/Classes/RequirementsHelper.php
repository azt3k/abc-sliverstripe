<?php

class RequirementsHelper {
	
	protected static $extra_requirements = array(
		'block'		=>	array(),
		'unblock'	=>	array()
	);
	
	/**
	 *	@param (string | array) $files - a string or an array of strings representing the relative (to the SS root) paths of the files you wish to block
	 */
	public static function require_block($files) {
		if (!is_array($files)) $files = array($files);
		foreach ($files as $file) {
			self::$extra_requirements['block'][] = array($file);
		}
	}

	/**
	 *	@param (string | array) $files - a string or an array of strings representing the relative (to the SS root) paths of the files you wish to block
	 */	
	public static function require_unblock($files) {
		if (!is_array($files)) $files = array($files);
		foreach ($files as $file) {
			self::$extra_requirements['unblock'][] = array($file);
		}
	}

	/**
	 *  @return (array) the extra requirements tracked by this class
	 */	
	public static function get_requirements(){
		return self::$extra_requirements;
	}
}