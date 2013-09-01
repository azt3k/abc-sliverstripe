<?php
/**
 * This field lets you put an arbitrary piece of HTML into your forms.
 * 
 * <b>Usage</b>
 * 
 * <code>
 * new LiteralField (
 *    $name = "literalfield",
 *    $content = '<b>some bold text</b> and <a href="http://silverstripe.com">a link</a>'
 * )
 * </code>
 * 
 * @package forms
 * @subpackage fields-dataless
 */
class SyntaxHighlightedField extends TextAreaField {
	
	/**
	 * @var string $content
	 */
	protected $content;

	function __construct($name, $title = null, $value = null, $type="html") {

		// Requirements
		Requirements::javascript(ABC_PATH.'/javascript/library/codemirror/lib/codemirror.js');
		Requirements::css(ABC_PATH.'/javascript/library/codemirror/lib/codemirror.css');
		Requirements::javascript(ABC_PATH.'/javascript/library/codemirror/mode/'.$type.'/'.$type.'.js');		
		Requirements::javascript('abc/javascript/SyntaxHighlightedField.js');
		
		// classes
		$this->addExtraClass('syntax-highlighted');
		$this->addExtraClass('syntax-highlighted-'.$type);
		$this->setAttribute( 'data-type', $type );

		// call parent constructor
		parent::__construct($name, $title = null, $value = null);
	}
}

?>