SilverStripe RemodelAdmin
===================================

Basically a subclass of ModelAdmin for holding useful and common customisations.  Based on the work of UncleCheese (http://www.leftandmain.com/uncategorized/2011/02/25/taming-the-beast-remodeling-modeladmin/) and Matt Clegg (https://github.com/mattclegg/remodeladmin)

Maintainer Contacts
-------------------
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

Managed Models:

In you admin class extending RemodelAdmin you can specify the managed Models for this admin:

```php
	static $managed_models = array (
		'NewsPage',
		'BlogEntry'
	);
```

If you want to define different parents for the different models, you can do this as follows:
	
```php
	static $parent = array(
		'NewsPage' => 'archive',
		'BlogEntry' => 'blog'
	);
```

Hide Child Pages:

If you want to hide the managed page types in the site tree, add the following in your _config.php:

```php
	Object::add_extension('BlogHolder', 'HideChildrenDecorator');
```

All child elements of the BlogHolder pages will be hidden. The pages are not loaded to the sitetree, wich 
results in much hiegher performance. 

Known Issues
------------
[Issue Tracker](https://github.com/nathancox/silverstripe-remodeladmin/issues)