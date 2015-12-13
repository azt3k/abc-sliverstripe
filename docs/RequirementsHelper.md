RequirementsHelper
==================

Provides some useful extensions to the Requirements class e.g.


## Block the bundled version of jQuery from the public site
````php
RequirementsHelper::require_block(array(
    'framework/thirdparty/jquery/jquery.js',
    '//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js',
    '//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.js'
));

LeftAndMainHelper::require_unblock(array(
    'framework/thirdparty/jquery/jquery.js'
));

````
