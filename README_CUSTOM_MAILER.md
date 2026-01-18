# PHPAuth Mailer System

PHPAuth provides a flexible mailer system that allows you to use either the built-in mailer or
implement your own custom email delivery solution.

## Default Mailer

PHPAuth includes a default `NativeMailer` that uses PHP's native `mail()` function.
This mailer is automatically enabled when you initialize the Config class.

### Basic Usage

```php
use PHPAuth\Config;

$config = new Config($dbh);
// NativeMailer is enabled by default
```

### Configuring the Default Mailer

You can configure the default mailer by passing configuration options:

```php
use PHPAuth\Mailer\NativeMailer;

$mailer = new NativeMailer([
    'from' => 'noreply@yourdomain.com',
    'from_name' => 'Your Application Name'
]);

$config->setMailer($mailer);
```

## Custom Mailer Implementation

To use your own email delivery solution (such as PHPMailer, SwiftMailer, or third-party services like SendGrid, Mailgun, etc.),
create a class that implements the `PHPAuth\Mailer\MailerInterface`.

### MailerInterface Requirements

Your custom mailer must implement the following interface:

```php
namespace PHPAuth\Mailer;

interface MailerInterface
{
    /**
     * Send an email
     *
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $body Email body (HTML)
     * @param string $altbody Plain text alternative body
     * @param string|null $from Sender email (optional)
     * @param string|null $fromName Sender name (optional)
     * @return bool Success status
     */
    public function send(
        string $to,
        string $subject = '',
        string $body = '',
        string $altbody = '',
        ?string $from = null,
        ?string $fromName = null
    ): bool;

    /**
     * Send password reset email (can be removed in future releases)
     *
     * @param string $email Recipient email
     * @param string $resetKey Password reset key
     * @return bool Success status
     */
    public function sendPasswordReset(string $email, string $resetKey): bool;

    /**
     * Send activation email (can be removed in future releases)
     *
     * @param string $email Recipient email
     * @param string $activationKey Activation key
     * @return bool Success status
     */
    public function sendActivation(string $email, string $activationKey): bool;
}
```

### Example: PHPMailer Implementation

PHPAuth includes a ready-to-use PHPMailer driver:

```php
use PHPAuth\Mailer\PHPMailerDriver;

$mailerConfig = [
    'host'      => 'smtp.example.com',
    'port'      => 587,
    'auth'      => true,
    'username'  => 'your-email@example.com',
    'password'  => 'your-password',
    'secure'    => 'tls', // or 'ssl'
    'site_email'    => 'noreply@example.com',
    'site_name'     => 'Your Site Name',
    'debug'     => 0 // Set to 2 for verbose debugging
];

$mailer = new PHPMailerDriver($mailerConfig);
$config->setMailer($mailer);
```

### Example: Custom Mailer for Third-Party Service

Here's an example of implementing a custom mailer for a third-party service:

```php
namespace YourApp\Mailer;

use PHPAuth\Mailer\MailerInterface;

class SendGridMailer implements MailerInterface
{
    private string $apiKey;
    private string $defaultFrom;
    private string $defaultFromName;

    public function __construct(array $config)
    {
        $this->apiKey = $config['api_key'];
        $this->defaultFrom = $config['from'] ?? 'noreply@example.com';
        $this->defaultFromName = $config['from_name'] ?? 'My App';
    }

    public function send(
        string $to,
        string $subject = '',
        string $body = '',
        string $altbody = '',
        ?string $from = null,
        ?string $fromName = null
    ): bool {
        $from = $from ?? $this->defaultFrom;
        $fromName = $fromName ?? $this->defaultFromName;

        // Implement SendGrid API call here
        // Return true on success, false on failure

        return true;
    }

    public function sendPasswordReset(string $email, string $resetKey): bool
    {
        // Implement password reset email logic
        return true;
    }

    public function sendActivation(string $email, string $activationKey): bool
    {
        // Implement activation email logic
        return true;
    }
}
```

Then use it in your application:

```php
use YourApp\Mailer\SendGridMailer;

$mailer = new SendGridMailer([
    'api_key' => 'your-sendgrid-api-key',
    'from' => 'noreply@yourdomain.com',
    'from_name' => 'Your Application'
]);

$config->setMailer($mailer);
```

## Disabling the Mailer

If you need to disable email functionality entirely (e.g., for testing):

```php
$config->setMailer(false);
```

## setMailer() Method

The `setMailer()` method accepts three types of input:

- **`null`** - Uses the default NativeMailer
- **`MailerInterface` instance** - Uses your custom mailer implementation
- **`false`** - Disables the mailer entirely

```php
// Use default mailer
$config->setMailer(null);

// Use custom mailer
$config->setMailer(new YourCustomMailer($config));

// Disable mailer
$config->setMailer(false);
```

## Best Practices

1. **Always handle exceptions** - Email sending can fail, so wrap your mailer calls in try-catch blocks
2. **Test thoroughly** - Test your custom mailer implementation with various scenarios
3. **Log failures** - Keep track of email delivery failures for debugging
4. **Use environment variables** - Store sensitive credentials (API keys, passwords) in environment variables, not in code
5. **Implement proper error handling** - Return meaningful error messages from your mailer implementation

## Requirements

- For `NativeMailer`: PHP's `mail()` function must be properly configured on your server
- For `PHPMailerDriver`: PHPMailer library must be installed (`composer require phpmailer/phpmailer`)
- For custom mailers: Any dependencies required by your chosen email service
