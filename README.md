# wp-hashids
This plugin provides a [Hashids](http://hashids.org/php/) implementation for WordPress.

## Requirements
WordPress, PHP 7.0 or greater and Composer.

## Installation
Install using Composer:

```
$ composer require ssnepenthe/wp-hashids
```

OR

```
$ cd /path/to/project/wp-content/plugins
$ git clone git@github.com:ssnepenthe/wp-hashids.git
$ cd wp-hashids
$ composer install
```

## Usage
Once the plugin is activated, browse to `wp-admin > settings > permalinks` and set a custom structure which contains the tag `%hashid%`.
