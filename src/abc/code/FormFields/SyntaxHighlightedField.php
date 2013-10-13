<?php
class SyntaxHighlightedField extends TextAreaField {
	
	/**
	 * @var string $content
	 */
	protected $content;

	function __construct($name, $title = null, $value = null, $type="html") {

		// Requirements
		Requirements::javascript(ABC_VENDOR_PATH . '/codemirror/lib/codemirror.js');
		Requirements::css(ABC_VENDOR_PATH . '/codemirror/lib/codemirror.css');
		Requirements::javascript(ABC_VENDOR_PATH . '/codemirror/mode/'.$type.'/'.$type.'.js');		
		Requirements::javascript(ABC_PATH . '/javascript/SyntaxHighlightedField.js');

		// classes
		$this->addExtraClass('syntax-highlighted');
		$this->addExtraClass('syntax-highlighted-'.$type);
		$this->setAttribute( 'data-type', $type );

		// call parent constructor
		parent::__construct($name, $title = null, $value = null);
	}
}