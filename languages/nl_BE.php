<?php
$lang = array();

$lang['user_blocked'] = "Je bent momenteel uitgesloten van het systeem.";
$lang['user_verify_failed'] = "De Captcha Code is niet juist.";

$lang['email_password_invalid'] = "Je e-mail adres en/of paswoord zijn niet juist.";
$lang['email_password_incorrect'] = "Je e-mail adres en/of paswoord is fout.";
$lang['remember_me_invalid'] = "Het \"remember me\" veld is niet juist.";

$lang['password_short'] = "Je gekozen paswoord is te kort.";
$lang['password_weak'] = "Password is too weak.";
$lang['password_nomatch'] = "De paswoorden zijn niet gelijk.";
$lang['password_changed'] = "Je paswoord is met success aangepast.";
$lang['password_incorrect'] = "Je huidig paswoord is fout.";
$lang['password_notvalid'] = "Je paswoord is niet juist.";

$lang['newpassword_short'] = "Het nieuwe paswoord is te kort.";
$lang['newpassword_long'] = "Het nieuwe paswoord is te lang.";
$lang['newpassword_invalid'] = "Het nieuwe paswoord moet ten minste één hoofdletter, één kleine letter en één cijfer bevatten.";
$lang['newpassword_nomatch'] = "De nieuwe paswoorden zijn niet gelijk.";
$lang['newpassword_match'] = "Het nieuwe paswoord is hetzelfde als het oude paswoord.";

$lang['email_short'] = "Je e-mail adres is te kort.";
$lang['email_long'] = "Je e-mail adres is te lang.";
$lang['email_invalid'] = "Je e-mail adres is niet juist.";
$lang['email_incorrect'] = "Je e-mail adres is fout.";
$lang['email_banned'] = "Dit e-mail adres is niet toegestaan.";
$lang['email_changed'] = "Je e-mail adres is met success aangepast.";

$lang['newemail_match'] = "Het nieuwe e-mail adres is hetzelfde als het vorige e-mail adres.";

$lang['account_inactive'] = "Je account is nog niet geactiveerd.";
$lang['account_activated'] = "Je account is geactiveerd.";

$lang['logged_in'] = "Je bent nu ingelogd.";
$lang['logged_out'] = "Je bent nu uitgelogd.";

$lang['system_error'] = "Er heeft zich een systeemfout voorgedaan. Gelieve opnieuw te proberen.";

$lang['register_success'] = "Je account is aangemaakt. Een activatie-mail is verstuurd naar je e-mail adres.";
$lang['register_success_emailmessage_suppressed'] = "Je account is aangemaakt.";
$lang['email_taken'] = "Het e-mail adres is al in gebruik.";

$lang['resetkey_invalid'] = "De herstelsleutel is niet juist.";
$lang['resetkey_incorrect'] = "De herstelsleutel is fout.";
$lang['resetkey_expired'] = "De geldigheid van de herstelsleutel is verlopen.";
$lang['password_reset'] = "Het paswoord is met succes aangepast.";

$lang['activationkey_invalid'] = "De activatiesleutel is niet juist.";
$lang['activationkey_incorrect'] = "De activatiesleutel is fout.";
$lang['activationkey_expired'] = "De geldigheid van de activatiesleutel is verlopen.";

$lang['reset_requested'] = "De aanvraag voor het herstellen van je paswoord is verzonden naar je e-mail adres.";
$lang['reset_requested_emailmessage_suppressed'] = "De aanvraag voor het herstellen van je paswoord is aangemaakt.";
$lang['reset_exists'] = "De aanvraag voor het herstellen van je paswoord is al gebeurd.";

$lang['already_activated'] = "Je account is reeds geactiveerd.";
$lang['activation_sent'] = "De activatie-mail is verstuurd.";
$lang['activation_exists'] = "De activatie-mail is reeds verstuurd.";

$lang['email_activation_subject'] = '%s - Activateer je account';
$lang['email_activation_body'] = 'Hallo,<br/><br/> Om in te loggen in je account, moet je eerst je account activeren door te klikken op deze link: <strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/> Vervolgens moet je deze activatiesleutel gebruiken: <strong>%3$s</strong><br/><br/> Als je recent geen account op %1$s hebt proberen aanmaken, dan is dit bericht foutief verstuurd - gelieve het te negeren.';
$lang['email_activation_altbody'] = 'Hallo, ' . "\n\n" . 'Om in te loggen in je account, moet je eerst je account activeren door te klikken op deze link:' . "\n" . '%1$s/%2$s' . "\n\n" . 'Vervolgens moet je deze activatiesleutel gebruiken: %3$s' . "\n\n" . 'Als je recent geen account op %1$s hebt proberen aanmaken, dan is dit bericht foutief verstuurd - gelieve het te negeren.';

$lang['email_reset_subject'] = '%s - Herstelaanvraag voor je paswoord';
$lang['email_reset_body'] = 'Hallo,<br/><br/>Om je paswoord te herstellen, klik deze link:<br/><br/><strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/> Vervolgens moet je deze herstelsleutel gebruiken: <strong>%3$s</strong><br/><br/>Als je recent geen herstelsleutel voor je paswoord hebt aangevraagd op %1$s, dan is dit bericht foutief verstuurd - gelieve het te negeren.';
$lang['email_reset_altbody'] = 'Hallo, ' . "\n\n" . 'Om je paswoord te herstellen, klik deze link:' . "\n" . '%1$s/%2$s' . "\n\n" . 'Vervolgens moet je deze herstelsleutel gebruiken: %3$s' . "\n\n" . 'Als je recent geen account op %1$s hebt proberen aanmaken, dan is dit bericht foutief verstuurd - gelieve het te negeren.';

$lang['account_deleted'] = "Je account is met success verwijderd.";
$lang['function_disabled'] = "Deze functie is uitgeschakeld.";
$lang['account_not_found'] = "Geen account gevonden met deze e-mail.";

return $lang;
