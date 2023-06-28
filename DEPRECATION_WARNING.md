# Future changes

## EMail Validator

1. `database_defs/database_emails_banned.sql` will be removed. Please, use custom email validator:

```php
// composer require phpauth/phpauth.email-validator

use PHPAuth\EMailValidator;

$config = $config->setEMailValidator(static function ($email) {
    return \PHPAuth\EMailValidator::isValid($email);
});
```

without custom validator email always valid (not in banlist)

## PHP Version

Minimum PHP Version required 7.4

## Custom translations

Will be removed at from PHPAuth "Core"

Use https://github.com/PHPAuth/PHPAuth.l10n package instead:

```php
// composer require phpauth/phpauth.l10n

$config = new \PHPAuth\Config($pdo, null, \PHPAuth\Config::CONFIG_TYPE_SQL);

$language_pack = (new \PHPAuth\PHPAuthLocalization('fr_FR'))->use();
// or
$language_pack = (new \PHPAuth\PHPAuthLocalization('fr_FR'))->use($pdo, 'custom_localization_table');

$config = $config->setLocalization( $language_pack );
```

without language pack - default en_GB will be used.

# since 2.0

Planned: Chaining config load





