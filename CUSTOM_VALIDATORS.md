# What is it?

All custom validators takes a closure function as a single argument.

This is a function that accepts a string (password or email) and returns boolean (true or false).
Closure can accept additional parameters with `use ()` expression.

This function must return:
- true if email or password is valid (passed validation)
- false othervise

# Password custom validators

Initialized with `setPasswordValidator()` method.

Called at:
- register
- @todo

## bjeavons/zxcvbn-php

`composer require bjeavons/zxcvbn-php`

```php
use ZxcvbnPhp\Zxcvbn;

$config = $config->setPasswordValidator(static function($password) use ($config) {
    return (bool)((new Zxcvbn())->passwordStrength($password)['score'] >= intval($config->password_min_score));
});


```
# E-Mail custom validators

Initialized with `setEmailValidator()` method.

Called at:
- register
-

## phpauth/phpauth.email-validator (recommended)

```php

$config = $config->setEMailValidator(static function ($email) {
    return \PHPAuth\EMailValidator::isValid($email);
});


```

## mattketmo/email-checker (PHP 7.1+)

Throwaway email detection library.

`composer require mattketmo/email-checker`

```php

use EmailChecker\EmailChecker;

 $config = $config->setEMailValidator(static function ($email) {
    return (new EmailChecker())->isValid($email);
});
```

## fgribreau/mailchecker (PHP 7.3+)

Temporary (disposable/throwaway) email detection library. Covers 1987 fake email providers.
NB: Package does not use namespaces.

`composer require fgribreau/mailchecker`

```php
 $config = $config->setEMailValidator(static function ($email) {
    return MailChecker::isValid($email);
});
```

## stymiee/email-validator

`composer require stymiee/email-validator`


