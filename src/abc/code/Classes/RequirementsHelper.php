<?php

class RequirementsHelper {

	protected static $extra_requirements = array(
		'block'		=>	array(),
		'unblock'	=>	array()
	);

	/**
	 *	@param (string | array) $files - a string or an array of strings representing the relative (to the SS root) paths of the files you wish to block
	 *	@return string the name of the called class
	 */
	public static function require_block($files) {
		$class = get_called_class();
		if (!is_array($files)) $files = array($files);
		foreach ($files as $file) {
			$class::$extra_requirements['block'][] = $file;
		}
		return $class;
	}

	/**
	 *	@param (string | array) $files - a string or an array of strings representing the relative (to the SS root) paths of the files you wish to block
	 *	@return string the name of the called class
	 */
	public static function require_unblock($files) {
		$class = get_called_class();
		if (!is_array($files)) $files = array($files);
		foreach ($files as $file) {
			$class::$extra_requirements['unblock'][] = $file;
		}
		return $class;
	}

	/**
	 *  @return (array) the extra requirements tracked by this class
	 */
	public static function get_requirements() {
		$class = get_called_class();
		return $class::$extra_requirements;
	}

	/**
	 *	@return string the name of the called class
	 */
	public static function process_requirements() {

		$class 			= get_called_class();
		$requirements 	= $class::get_requirements();

		foreach ($requirements['block'] as $file) {
			Requirements::block($file);
		}

		foreach ($requirements['unblock'] as $file) {
			Requirements::unblock($file);
		}

		return $class;

	}
}
