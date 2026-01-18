# Custom translations

Custom translation may be removed at from PHPAuth Core.

Use https://github.com/PHPAuth/PHPAuth.l10n package instead:

```php
// composer require phpauth/phpauth.l10n

$config = new \PHPAuth\Config(...);

$language_pack = (new \PHPAuth\PHPAuthLocalization('fr_FR'))->use();
// or
$language_pack = (new \PHPAuth\PHPAuthLocalization('fr_FR'))->use($pdo, 'custom_localization_table');

$config = $config->setLocalization($language_pack);
```

without language pack - default en_GB will be used.
