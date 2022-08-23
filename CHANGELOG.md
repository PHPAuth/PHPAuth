## 1.3.5

- [!] recommended version for composer is `latest`, NOT `dev-master`
- [!] minimal PHP required 7.2.*
- [!] added mb-string dependency, because using mb_strtolower instead of strtolower (email can have non-english domain)
- [!] All dies replaced with RuntimeException

- [+] added "phpauth/phpauth.l10n" to composer.json suggests
- [+] added custom password and email validator
- [+] add `Config::setLocalization()` method that update config's internal dictionary from given array.
- [+] add `Config::setCustomMailer()` method - future custom closure for sendMail method
- [+] please, use `deleteExpiredData()` instead of `cron()`
- [+] export script from SQL dictionary to PHP file

- [*] Added ConfigInterface and AuthInterface
- [*] refactored `checkTableExists()` method. Uses different detection methods for separate drivers
- [*] Source files moved to sources/ directory. Updated autoload/psr-4 section at composer.json
- [*] Zxcvbn moved to separate method (isPasswordStrong)
- [*] configuration types declared as ConfigInterface public constants
- [*] documentation fixes
- [*] Updated localization dictionary generation: if message not found in custom dictionary - used message from en_GB dictionary.
- [*] Updated getForgottenDictionary() method
- [*] Moved methods to Helper class: `getForgottenDictionary()`, `getIP`, `getRandomKey()`, `getHash()`

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
