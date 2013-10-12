<?php

class ColourPickerField extends TextField {
	
	public function __construct($name, $title = null, $value = '', $maxLength = null, $form = null) {

		parent::__construct($name, $title, $value, $maxLength, $form);

		Requirements::javascript(ABC_VENDOR_PATH . '/jquery.colorpicker/jquery.colorpicker.js');
		Requirements::customScript(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/'.ABC_PATH.'/javascript/ColourPickerField.js'));
		Requirements::css(ABC_VENDOR_PATH . '/jquery.colorpicker/jquery.colorpicker.css');

		$this->addExtraClass('text');
	}
	
}
