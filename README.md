# wp-hashids
This plugin provides a [Hashids](http://hashids.org/php/) implementation for WordPress.

## Requirements
WordPress 4.7 or greater, PHP 5.6 or greater and Composer.

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

## Configuration
Sensible defaults are automatically set for you.

If you wish to set custom values, there are two methods for doing so:

1) Define any number of `WP_HASHIDS_*` constants (preferred)
2) Browse to `wp-admin > settings > wp hashids` and set the values via the provided interface.

The following constants can be used for configuration:

* `WP_HASHIDS_ALPHABET` - valid options are `lower`, `upper`, `lowerupper`, `lowernumber`, `uppernumber`, and `all`. You will need to manually flush rewrite rules after changing this constant by going to `settings > permalinks`.
* `WP_HASHIDS_MIN_LENGTH` - can be any integer >= 0.
* `WP_HASHIDS_SALT` - should be a unique string to ensure hashids are unique to your site.

Keep in mind that any changes to the plugin settings will immediately change all post URLs that use the `%hashid%` rewrite tag, and as such, should only be modified immediately after plugin activation.

If all three constants are configured, the `WP Hashids` settings page will not be visible.

## Warning
**HASHIDS IS ONLY INTENDED TO OBFUSCATE NUMBERS.**

**IT DOES NOT PROVIDE ENCRYPTION AND IT IS NOT SECURE.**

**YOU SHOULD ASSUME THAT YOUR SALT CAN AND WILL BE READ BY ANYONE THAT CAN VIEW YOUR SITE!**

**FOR THIS REASON, YOU SHOULD NOT (RE)USE ANY SENSITIVE INFORMATION (LIKE PASSWORDS OR CORE SALTS) FOR YOUR HASHIDS SALT.**

**YOUR SALT SHOULD BE A UNIQUE VALUE THAT IS ONLY USED BY THIS PLUGIN.**

**FOR MORE INFORMATION, CHECK THE LINKS LISTED [HERE](http://hashids.org/#decoding).**

## Considerations
As mentioned previously, changing any config value will result in all of your post URLs changing. This should only be done immediately after installing the plugin.

This plugin provides a method of obfuscating post IDs in URLs, however it makes no attempt to hide them anywhere else. If you need to completely mask post IDs from your users, look elsewhere.

Custom post types are supported but you will need to configure a custom permastruct using `add_permastruct()` if you want to remove the post name from the URL.
