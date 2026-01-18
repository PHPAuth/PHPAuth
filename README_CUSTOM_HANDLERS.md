# Custom Handlers Configuration in PHPAuth

PHPAuth provides a flexible system for integrating custom handlers that allow you to extend and customize the authentication process.
These handlers are implemented using callbacks and can be configured via the `Config` class.

## Overview

Custom handlers in PHPAuth accept closure functions as single arguments. These functions typically:
- Accept specific parameters (like email, password, or captcha response)
- Return boolean values (`true` for valid/passed, `false` otherwise)
- Can include additional parameters via the `use ()` expression
- Are optional - if not defined, default behaviors are used

## Available Custom Handlers

### 1. Email Validator (`setEmailValidator()`)

Validates email addresses beyond basic format checking (e.g., checks against disposable email services).

**Method:** `setEmailValidator(callable $handler)`

**Handler Signature:** `function(string $email): bool`

**Usage:**
```php
$config = $config->setEmailValidator(static function ($email) {
    // Custom validation logic
    return !in_array($email, $bannedEmailsList);
});
```

**Example with PHPAuth Email Validator:**
```php
use PHPAuth\EMailValidator;

$config = $config->setEmailValidator(static function ($email) {
    return \PHPAuth\EMailValidator::isValid($email);
});
```

**Example with fgribreau/mailchecker**

```php
// composer require fgribreau/mailchecker (NB: 5* branch does not use namespaces)
$config = $config->setEMailValidator(static function ($email) {
    return MailChecker::isValid($email);
});
```

**Internal Implementation:**

```php
if (is_callable($this->emailValidator) && !call_user_func_array($this->emailValidator, [ $email ])) {
    $this->addAttempt();
    $state['message'] = $this->__lang('email.address_in_banlist');

    return $state;
}
```

**Without custom validator:** Emails are considered valid if they pass basic format validation.

### 2. Password Validator (`setPasswordValidator()`)

Validates password strength and complexity.

**Method:** `setPasswordValidator(callable $handler)`

**Handler Signature:** `function(string $password): bool`

**Example:**
```php
$config = $config->setPasswordValidator(static function($password) {
    $password_min_score = 3; // Minimum strength threshold
    // using "bjeavons/zxcvbn-php" library
    return (bool)((new Zxcvbn())->passwordStrength($password)['score'] >= $password_min_score);
});
```

**Internal Implementation:**
```php
private function isPasswordStrong(string $password): bool
{
    if (is_callable($this->passwordValidator)) {
        return ($this->passwordValidator)($password);
    }
    return true; // Default: always valid
}
```

**Without custom validator:** Passwords are always considered strong enough.

### 3. Captcha Validator (`setCaptchaValidator()`)

Validates captcha responses (e.g., Google reCAPTCHA).

**Method:** `setCaptchaValidator(callable $handler, array $captcha_config = [])`

**Handler Signature:** `function(string $captcha_response): bool`

**Usage:**
```php
$config = $config->setCaptchaValidator(static function($captcha_response) use ($reCaptcha_config) {
    if (empty($reCaptcha_config)) {
        return true;
    }

    if ($reCaptcha_config['enabled'] == false) {
        return true;
    }

    if (empty($reCaptcha_config['secret_key'])) {
        throw new RuntimeException('No secret provided');
    }

    if (!is_string($reCaptcha_config['secret_key'])) {
        throw new RuntimeException('The provided secret must be a string');
    }

    $recaptcha = new ReCaptcha($reCaptcha_config['secret_key']);
    $checkout = $recaptcha->verify($captcha_response, \PHPAuth\Helpers::getIp());

    if (!$checkout->isSuccess()) {
        return false;
    }

    return true;
}, $reCaptcha_config);
```

**Internal implementation**

```php
/**
 * Verifies a captcha answer. Return true if captcha is correct, false otherwise.
 *
 * @param string $captcha_response
 *
 * @return boolean
 */
protected function checkCaptcha(string $captcha_response): bool
{
    if (is_callable($this->captchaHandler)) {
        return call_user_func_array($this->captchaHandler, [ $captcha_response ] );
    }

    return true;
}
```


### 4. Mailer Handler (`setMailer()`)

Configures the email sending mechanism.

See [README_CUSTOM_MAILER.md]

**Method:** `setMailer($driver)`

**Options:**
- `false`: Disable email functionality
- `null`: Use the default `NativeMailer`
- `MailerInterface` instance: Use custom mailer implementation

**Usage:**
```php
// Use default mailer
$config->setMailer(null);

// Disable mailer
$config->setMailer(false);

// Use custom mailer implementing MailerInterface
$config->setMailer(new CustomMailer());
```


## Complete Configuration Example

```php
use PHPAuth\Config;
use PHPAuth\EMailValidator;
use ZxcvbnPhp\Zxcvbn;
use ReCaptcha\ReCaptcha;

// Initialize configuration
$config = new Config($dbh, 'phpauth_config', Config::CONFIG_TYPE_SQL);

// Configure email validator (check against disposable emails)
$config->setEmailValidator(static function ($email) {
    return EMailValidator::isValid($email);
});

// Configure password validator (zxcvbn strength check)
$config->setPasswordValidator(static function($password) {
    $password_min_score = 3;
    return (bool)((new Zxcvbn())->passwordStrength($password)['score'] >= $password_min_score);
});

// Configure captcha validator
$reCaptcha_config = [
    'recaptcha_enabled' => true,
    'recaptcha_secret_key' => 'your_secret_key'
];

$config->setCaptchaValidator(static function($captcha_response) use ($reCaptcha_config) {
    if (!$reCaptcha_config['recaptcha_enabled']) {
        return true;
    }

    $recaptcha = new ReCaptcha($reCaptcha_config['recaptcha_secret_key']);
    return $recaptcha->verify($captcha_response, $_SERVER['REMOTE_ADDR'])->isSuccess();
}, $reCaptcha_config);

// Configure mailer
$config->setMailer(null); // Use default

// Use the configured $config with PHPAuth\Auth
$auth = new Auth($dbh, $config);
```

## Important Notes

1. **Return Values:** Custom handlers must return `true` for successful validation and `false` for failures.

2. **Error Handling:** When validators return `false`, PHPAuth will:
    - For passwords: Reject the password during registration
    - For emails: Add an attempt and return an error state
    - For captcha: Consider the captcha invalid
    - For mail: Sending email completed with error.

3. **Default Behaviors:**
    - Without email validator: Only basic email format validation
    - Without password validator: All passwords accepted
    - Without captcha validator: Captcha validation skipped
    - Without mailer handler: internal implementation (Native Mailer with `mail()` function) will be used.

4. **Performance:** Custom validators are called during critical operations (registration, login), so ensure they are efficient.

## Future Changes

- Email banlist (`database_emails_banned.sql`) will be removed - use custom email validators instead
- Minimum PHP version will be 7.4+, in V2 - 8.2+
- Built-in translations will be removed - use `PHPAuth/PHPAuth.l10n` package instead
- Configuration chaining support planned for version 2.0

## Recommended Packages

**Password Validators:**
- `bjeavons/zxcvbn-php` - Password strength estimation
- `rollerworks/password-strength-validator` - Comprehensive validation
- `rollerworks/password-common-list` - This package provides a Symfony Validator for the xato-net-10-million-passwords-1000000 CommonPassword list.
- `valorin/pwned-validator` - Check against breached passwords
- `garybell/password-validator` - Password validation determined by password entropy
- `jbafford/password-strength-bundle` - This bundle provides a validator for ensuring strong passwords in Symfony2 applications.
- `schuppo/password-strength` - package provides a validator for ensuring strong passwords in Laravel 4 applications.

**Email Validators:**
- `phpauth/phpauth.email-validator` - PHPAuth's official validator
- `mattketmo/email-checker` - Disposable email detection
- `fgribreau/mailchecker` - Extensive temporary email provider detection

These custom handlers provide a powerful way to extend PHPAuth's functionality while maintaining a clean separation between the authentication logic and your specific validation requirements.

**CAPTCHA**

-

**Mailer**


