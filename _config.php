<?php

// Define path constant
$path = str_replace('\\', '/', __DIR__);
$path_fragments = explode('/', $path);
$dir_name = $path_fragments[count($path_fragments) - 1];
define('ABC_VENDOR_PATH', $dir_name . '/thirdparty');
define('ABC_PATH', $dir_name . '/src/abc');

// Configure Image Extension
AbcImageExtension::$fallback_image = ABC_PATH . '/images/no-image.jpg';

// DatePicker config
Object::useCustomClass('DateField_View_JQuery', 'jQueryUIDateField_View');
