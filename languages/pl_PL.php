<?php
$lang = array();

$lang['user_blocked'] = "Konto zablokowane.";
$lang['user_verify_failed'] = "Zły kod captcha.";

$lang['email_password_invalid'] = "Niepoprawny format emaila lub hasła.";
$lang['email_password_incorrect'] = "Niepoprawne hasło lub email.";
$lang['remember_me_invalid'] = "Pole 'zapamiętaj mnie' wypełnione niepoprawnie.";

$lang['password_short'] = "Za krótkie hasło.";
$lang['password_weak'] = "Zbyt proste hasło.";
$lang['password_nomatch'] = "Hasła nie są identyczne.";
$lang['password_changed'] = "Pomyślnie zmieniono hasło.";
$lang['password_incorrect'] = "Niepoprawne hasło";
$lang['password_notvalid'] = "Zły format hasła.";

$lang['newpassword_short'] = "Nowe hasło jest za krótkie";
$lang['newpassword_long'] = "Nowe hasło jest za długie.";
$lang['newpassword_invalid'] = "Nowe hasło musi zawierać co najmniej jedną dużą literę, małą literę oraz jedną cyfrę.";
$lang['newpassword_nomatch'] = "Nowe hasła nie są identyczne";
$lang['newpassword_match'] = "Nowe hasło jest takie samo jak stare hasło.";

$lang['email_short'] = "Za krótki adres email.";
$lang['email_long'] = "Za długi adres email";
$lang['email_invalid'] = "Niepoprawny adres email";
$lang['email_incorrect'] = "Zły adres email.";
$lang['email_banned'] = "Ten adres email nie jest dozwolony.";
$lang['email_changed'] = "Email zmieniony pomyślnie.";

$lang['newemail_match'] = "Stary adres email jest taki sam jak poprzedni.";

$lang['account_inactive'] = "Konto nie było jeszcze aktywowane.";
$lang['account_activated'] = "Aktywowano konto.";

$lang['logged_in'] = "Zalogowano pomyślnie.";
$lang['logged_out'] = "Wylogowano pomyślnie.";

$lang['system_error'] = "Błąd systemu. Spróbuj ponownie.";

$lang['register_success'] = "Stworzono konto. Link aktywacyny wysłano na podany adres email.";
$lang['register_success_emailmessage_suppressed'] = "Stworzono konto.";
$lang['email_taken'] = "Podany email jest już w naszej bazie.";

$lang['resetkey_invalid'] = "Zły klucz resetu.";
$lang['resetkey_incorrect'] = "Niepoprawny klucz resetu.";
$lang['resetkey_expired'] = "Klucz resetu stracił ważność.";
$lang['password_reset'] = "Pomyślnie zresetowano hasło.";

$lang['activationkey_invalid'] = "Zły klucz resetu.";
$lang['activationkey_incorrect'] = "Niepoprawny klucz resetu.";
$lang['activationkey_expired'] = "Klucz aktywacyjny stracił ważność.";

$lang['reset_requested'] = "Na podany adres email wysłano link resetujący hasło.";
$lang['reset_requested_emailmessage_suppressed'] = "Stworzono żądanie resetu hasła.";
$lang['reset_exists'] = "Żądanie resetu hasła już istnieje";

$lang['already_activated'] = "Konto już jest aktywowane.";
$lang['activation_sent'] = "Wysłano email aktywacyjny.";
$lang['activation_exists'] = "Email aktywacyjny już był wysłany.";

$lang['email_activation_subject'] = '%s - Aktywuj konto';
$lang['email_activation_body'] = 'Cześć,<br/><br/> Aby aktywować konto kliknij w link: <strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/> Wprowadź tam klucz aktywacji: <strong>%3$s</strong><br/><br/> Jeżeli rejestracja w serwisie %1$s nie była dokonana przez Ciebie i email ten został wysłany omyłkowo, proszę zignoruj go.';
$lang['email_activation_altbody'] = 'Cześć,' . "\n\n" . 'Aby aktywować konto kliknij w link:' . "\n" . '%1$s/%2$s' . "\n\n" . 'Wprowadź tam klucz aktywacji: %3$s' . "\n\n" . 'Jeżeli rejestracja w serwisie %1$s nie była dokonana przez Ciebie i email ten został wysłany omyłkowo, proszę zignoruj go.';

$lang['email_reset_subject'] = '%s - Procedura resetu hasła';
$lang['email_reset_body'] = 'Cześć,<br/><br/>Aby zresetować hasło kliknij w link: <br/><br/><strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/>Wprowadź tam klucz resetu: <strong>%3$s</strong><br/><br/>Jeżeli reset hasła w serwisie %1$s nie był dokonany przez Ciebie, proszę zignoruj tą wiadomość.';
$lang['email_reset_altbody'] = 'Czesć, ' . "\n\n" . 'Aby zresetować hasło kliknij w link: ' . "\n" . '%1$s/%2$s' . "\n\n" . 'Wprowadź tam klucz resetu: %3$s' . "\n\n" . 'Jeżeli reset hasła w serwisie %1$s nie był dokonany przez Ciebie, proszę zignoruj tą wiadomość.';

$lang['account_deleted'] = "Pomyślnie usunięto konto.";
$lang['function_disabled'] = "Ta funkcja jest wyłączona.";
$lang['account_not_found'] = "Nie znaleziono konta z tym adresem e-mail.";

return $lang;
