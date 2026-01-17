<?php

namespace PHPAuth\Driver;

/**
 * Internal mailer driver for mail()
 */
class NativeMailDriver implements MailerDriverInterface
{
    private string $defaultFrom;
    private string $defaultFromName;

    public function __construct(array $config = [])
    {
        $this->defaultFrom = $config['from'] ?? 'noreply@example.com';
        $this->defaultFromName = $config['from_name'] ?? 'PHPAuth Mailer';
    }

    /**
     * @param string $to
     * @param string $subject
     * @param string $body
     * @param string $altbody
     * @param string|null $from
     * @param string|null $fromName
     * @return bool
     */
    public function send(string $to, string $subject = '', string $body = '', string $altbody = '', ?string $from = null, ?string $fromName = null): bool
    {
        $from = $from ?? $this->defaultFrom;
        $fromName = $fromName ?? $this->defaultFromName;

        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=utf-8',
            "From: {$fromName} <{$from}>",
            'X-Mailer: PHP/' . phpversion()
        ];

        return mail($to, $subject, $body, implode("\r\n", $headers));
    }

    /**
     * STUB for send Password Reset EMail
     *
     * @param string $email
     * @param string $resetKey
     * @return bool
     */
    public function sendPasswordReset(string $email, string $resetKey): bool
    {
        return true;
    }

    /**
     * STUB for send Activation EMail
     *
     * @param string $email
     * @param string $activationKey
     * @return bool
     */
    public function sendActivation(string $email, string $activationKey): bool
    {
        return true;
    }
}
