<?php

// Define path constant
$path = str_replace('\\', '/', __DIR__);
$path_fragments = explode('/', $path);
$dir_name = $path_fragments[count($path_fragments) - 1];
define('ABC_PATH', $dir_name . '/src/abc');

// Configure Image Extension
AbcImageExtension::$fallback_image = ABC_PATH.'/images/no-image.jpg';

// attach Extensions
Image::add_extension('AbcImageExtension');
File::add_extension('AbcFileExtension');
LeftAndMain::add_extension('AbcLeftAndMainExtension');
Security::add_extension('AbcSecurityExtension');

// Basic Requirements
Requirements::javascript(ABC_PATH."/javascript/library/modernizr-2.6.2.min.js");

// DatePicker config
Object::useCustomClass('DateField_View_JQuery', 'jQueryUIDateField_View');