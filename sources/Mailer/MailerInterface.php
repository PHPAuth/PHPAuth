<?php

namespace PHPAuth\Mailer;

interface MailerInterface
{
    /**
     * Отправка email
     *
     * @param string $to Email получателя
     * @param string $subject Тема письма
     * @param string $body Тело письма (HTML)
     * @param string|null $from Email отправителя (опционально)
     * @param string|null $fromName Имя отправителя (опционально)
     * @return bool Успешность отправки
     */
    public function send(string $to, string $subject = '', string $body = '', string $altbody = '', ?string $from = null, ?string $fromName = null): bool;

    public function sendPasswordReset(string $email, string $resetKey): bool;

    public function sendActivation(string $email, string $activationKey): bool;
}
