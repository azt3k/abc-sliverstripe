ABC SilverStripe Library
========================



What's in this thing Anyway?
----------------------------



This is a base library that is required by some of the other abc modules, it
includes a few things, some of the more useful features are:



### Enhanced requirements handling 

Allows for more granular inclusion of dependencies meaning you can more easily
block front end dependencies from the CMS and fixes some issues with x-include
headers in the security ping



### Basic Utility Classes

-   Zero config PDO based DB abstraction layer for when the ORM doesn't do what
    you need it to

-   DataObjectHelper for extracting meta data from the ORM

-   String and URL manipulation classes



### Extensions

-   Image

-   File



### Form Fields

-   SyntaxHighlightedField - extends a basic text area with syntax highlighting

-   ColourPickerField
