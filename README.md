# wp-hashids
This plugin provides a [Hashids](http://hashids.org/php/) implementation for WordPress.

## Requirements
WordPress 4.7 or greater, PHP 7.0 or greater and Composer.

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

If you wish to override the Hashids salt, minimum slug length or alphabet, browse to `wp-admin > settings > wp hashids` and update the appropriate settings.

Keep in mind that any changes to the plugin settings will immediately change all post URLs that use the `%hashid%` rewrite tag.

Alternatively, you can configure the plugin via the following constants:

`WP_HASHIDS_ALPHABET` - valid options are `lower`, `upper`, `lowerupper`, `lowernumber`, `uppernumber`, and `all`.

`WP_HASHIDS_MIN_LENGTH` - can be any integer >= 0.

`WP_HASHIDS_SALT` - should be a unique string to ensure hashids are unique to your site.

**It is recommended to configure the plugin via constant definitions.**

The reason is that if for any reason one of the plugin settings gets removed from your database, all of your post URLs might end up changing.

When all three constants are configured, the `WP Hashids` settings page will not be visible.

## Considerations
As mentioned previously, changing any config value will result in all of your post URLs changing. This should only be done immediately after installing the plugin.

This plugin will mask your post IDs in URLs, however, it makes no attempt to hide them anywhere else. Do not use it if you are trying to prevent users from seeing your post IDs.

Custom post types are supported but you will need to configure a custom permastruct using `add_permastruct()` if you want to remove the post name from the URL.
