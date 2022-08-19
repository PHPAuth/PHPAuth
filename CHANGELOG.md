## 1.3.4

- Update composer.json
- removed hardcoded version from composer.json
- Actual version defined with VERSION TAG at releases page

## 1.3.2

- Version fix
- Resolve null setcookie value for PHP 8+

## 1.3.1

Fixed issue #517 (rowCount() compared with zero, not true)

## 1.3.0

- PHP 7.1 minimal required
- for methods login() and changePassword() captcha response is string now
- removed return type from __set() method
- Added the translation field required by the php version
- updated text messages
- Added in option to modify same site cookie preferences
- updated database definition to configure the same-site cookie setting
- cookies now secure by default
- cookies now http-only by default
- addSession() now return false if cookies does not set
- Removed line to update cookie array after signing up
- Fix syntax in postgresql dump file
- changed info on instantiating Config.php object (in README)
- clearing cookie on logout

##  v1.2.1

In this release some improvements have been implemented.

Most functions now return a bunch of information (user_id).

##  v1.2.0

PHPAuth is getting a rewrite to bring the code up to date and add more functions. So this release is v1.0.0 and will count upwards from here
