SilverStripe RemodelAdmin
===================================

Basically a subclass of ModelAdmin for holding useful and common customisations.  Based on the work of UncleCheese (http://www.leftandmain.com/uncategorized/2011/02/25/taming-the-beast-remodeling-modeladmin/).

Maintainer Contacts
-------------------
* Matt Clegg (<cleggmatt@gmail.com>)
* Nathan Cox (<nathan@flyingmonkey.co.nz>)

Requirements
------------
* SilverStripe 2.4+

Documentation
-------------
[GitHub Wiki](https://github.com/nathancox/remodeladmin)

Installation Instructions
-------------------------

1. Place the files in a directory called remodeladmin in the root of your SilverStripe installation
2. Visit yoursite.com/dev/build to rebuild the database

Usage Overview
--------------

Default Summary Fields:

Can specify which summary fields are selected by default by putting something like this in the model:

```php
	static $summary_fields = array(
		'Name' => 'Name',
		'Email' => 'Email',
		'Created' => 'Date',
		'PageID' => 'Page'
   );
   
	static $default_summary_fields = array(
		'Name',
		'Email',
		'Created'
   );
```

Name, Email, Created and Page will all be available in the list of fields but only Name, Email and Created will be checked by default.
If $default_summary_fields isn't set then everything in $summary_fields will be on by default.



Known Issues
------------
[Issue Tracker](https://github.com/nathancox/silverstripe-remodeladmin/issues)
