<?php
$lang = array();

$lang['user_blocked'] = "Ihr Benutzer ist im System gesperrt.";
$lang['user_verify_failed'] = "Der Captcha-Code ist ungültig.";

$lang['email_password_invalid'] = "E-Mail-Adresse und/oder Passwort sind ungültig.";
$lang['email_password_incorrect'] = "E-Mail-Adresse und/oder Passwort sind falsch.";
$lang['remember_me_invalid'] = "Das Angemeldet bleiben-Feld ist ungültig.";

$lang['password_short'] = "Das Passwort ist zu kurz.";
$lang['password_weak'] = "Das Passwort ist zu einfach.";
$lang['password_nomatch'] = "Die Passwörter stimmen nicht überein.";
$lang['password_changed'] = "Das Passwort wurde erfolgreich geändert.";
$lang['password_incorrect'] = "Das aktuelle Passwort ist falsch.";
$lang['password_notvalid'] = "Das Passwort ist ungültig.";

$lang['newpassword_short'] = "Das neue Passwort ist zu kurz.";
$lang['newpassword_long'] = "Das neue Passwort ist zu lang.";
$lang['newpassword_invalid'] = "Das neue Passwort muss mindestens einen Großbuchstaben, einen Kleinbuchstaben sowie eine Ziffer enthalten.";
$lang['newpassword_nomatch'] = "Die neuen Passwörter stimmen nicht überein.";
$lang['newpassword_match'] = "Das neue Passwort ist dasselbe wie das alte Passwort.";

$lang['email_short'] = "Die E-Mail-Adresse ist zu kurz.";
$lang['email_long'] = "Die E-Mail-Adresse ist zu lang.";
$lang['email_invalid'] = "Die E-Mail-Adresse ist ungültig.";
$lang['email_incorrect'] = "Die E-Mail-Adresse ist nicht korrekt.";
$lang['email_banned'] = "Diese E-Mail-Adresse ist nicht erlaubt.";
$lang['email_changed'] = "Ihre E-Mail-Adresse wurde erfolgreich geändert.";

$lang['newemail_match'] = "Die neue E-Mail-Adresse ist die gleiche wie die alte.";

$lang['account_inactive'] = "Ihr Benutzerkonto wurde noch nicht aktiviert.";
$lang['account_activated'] = "Ihr Benutzerkonto wurde aktiviert.";

$lang['logged_in'] = "Sie sind jetzt angemeldet.";
$lang['logged_out'] = "Sie sind jetzt abgemeldet.";

$lang['system_error'] = "Ein Systemfehler ist aufgetreten. Bitte versuchen Sie es erneut.";

$lang['register_success'] = "Ihr Benutzerkonto wurde erstellt. Wir haben Ihnen eine E-Mail mit einem Aktivierungslink geschickt.";
$lang['register_success_emailmessage_suppressed'] = "Ihr Benutzerkonto wurde erstellt.";
$lang['email_taken'] = "Mit dieser E-Mail-Adresse ist bereits ein anderer Benutzer registriert.";

$lang['resetkey_invalid'] = "Der Sicherheitsschlüssel ist ungültig.";
$lang['resetkey_incorrect'] = "Der Sicherheitsschlüssel ist nicht korrekt.";
$lang['resetkey_expired'] = "Der Sicherheitsschlüssel ist abgelaufen.";
$lang['password_reset'] = "Ihr Passwort wurde erfolgreich zurückgesetzt.";

$lang['activationkey_invalid'] = "Der Aktivierungsschlüssel ist ungültig.";
$lang['activationkey_incorrect'] = "Der Aktivierungsschlüssel ist nicht korrekt.";
$lang['activationkey_expired'] = "Der Aktivierungsschlüssel ist abgelaufen.";

$lang['reset_requested'] = "Wir haben Ihnen eine E-Mail zum Zurücksetzen Ihres Passworts geschickt.";
$lang['reset_requested_emailmessage_suppressed'] = "Eine Anforderung zum Zurücksetzen Ihres Passworts wurde erstellt.";
$lang['reset_exists'] = "Es liegt bereits eine Anfrage zum Zurücksetzen Ihres Passworts vor. Die nächste Anfrage wird am %s möglich sein.";

$lang['already_activated'] = "Ihr Benutzerkonto ist bereits aktiviert.";
$lang['activation_sent'] = "Eine Aktivierungsmail wurde verschickt.";
$lang['activation_exists'] = "Eine Aktivierungsmail wurde bereits verschickt. Der nächste Aktivierungsversuch wird am %s möglich sein.";

$lang['email_activation_subject'] = '%s - Bitte aktivieren Sie Ihr Benutzerkonto';
$lang['email_activation_body'] = 'Hallo,<br/><br/>um sich mit Ihrem Benutzerkonto anzumelden, müssen Sie zuerst Ihr Benutzerkonto aktivieren, indem Sie auf folgenden Link klicken oder ihn manuell in die Adresszeile Ihres Browsers kopieren: <strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/>Sie benötigen folgenden Aktivierungsschlüssel: <strong>%3$s</strong><br/><br/>Wenn Sie sich gar nicht auf %1$s angemeldet haben, können Sie diese E-Mail einfach ignorieren.';
$lang['email_activation_altbody'] = 'Hallo,' . "\n\n" . 'um sich mit Ihrem Benutzerkonto anzumelden, mü+ssen Sie zuerst Ihr Benutzerkonto aktivieren, indem Sie auf folgenden Link klicken oder ihn manuell in die Adresszeile Ihres Browsers kopieren:' . "\n" . '%1$s/%2$s' . "\n\n" . 'Sie benötigen folgenden Aktivierungsschlüssel: %3$s' . "\n\n" . 'Wenn Sie sich gar nicht auf %1$s angemeldet haben, können Sie diese E-Mail einfach ignorieren.';

$lang['email_reset_subject'] = '%s - Passwort zurücksetzen';
$lang['email_reset_body'] = 'Hallo,<br/><br/>um Ihr Passwort zurückzusetzen, klicken Sie bitte auf folgenden Link oder kopieren Sie ihn manuell in die Adresszeile Ihres Browsers: <br/><br/><strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/>Sie benötigen folgenden Sicherheitsschlüssel: <strong>%3$s</strong><br/><br/>Wenn Sie Ihr Passwort auf %1$s gar nicht zurücksetzen wollen, können Sie diese E-Mail einfach ignorieren.';
$lang['email_reset_altbody'] = 'Hallo,' . "\n\n" . 'um Ihr Passwort zurückzusetzen, klicken Sie bitte auf folgenden Link oder kopieren Sie ihn manuell in die Adresszeile Ihres Browsers:' . "\n" . '%1$s/%2$s' . "\n\n" . 'Sie benötigen folgenden Sicherheitsschlüssel: %3$s' . "\n\n" . 'Wenn Sie Ihr Passwort auf %1$s gar nicht zurücksetzen wollen, können Sie diese E-Mail einfach ignorieren.';

$lang['account_deleted'] = "Ihr Benutzerkonto wurde erfolgreich gelöscht.";
$lang['function_disabled'] = "Diese Funktion wurde deaktiviert.";
$lang['account_not_found'] = "Zu der angegebenen E-Mail-Adresse wurde kein Benutzer gefunden.";

return $lang;
