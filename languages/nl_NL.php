<?php
$lang = array();

$lang['user_blocked'] = "Je bent momenteel uitgesloten van het systeem.";
$lang['user_verify_failed'] = "De captcha code is niet juist.";

$lang['email_password_invalid'] = "Je e-mailadres en/of wachtwoord is niet juist.";
$lang['email_password_incorrect'] = "Je e-mailadres en/of wachtwoord is fout.";
$lang['remember_me_invalid'] = "Het \"onthoud mij\" veld is niet juist.";

$lang['password_short'] = "Je gekozen wachtwoord is te kort.";
$lang['password_weak'] = "Je gekozen wachtwoord is niet complex genoeg.";
$lang['password_nomatch'] = "De wachtwoorden zijn niet gelijk.";
$lang['password_changed'] = "Je wachtwoord is met succes aangepast.";
$lang['password_incorrect'] = "Je huidig wachtwoord is fout.";
$lang['password_notvalid'] = "Je wachtwoord is niet juist.";

$lang['newpassword_short'] = "Het nieuwe wachtwoord is te kort.";
$lang['newpassword_long'] = "Het nieuwe wachtwoord is te lang.";
$lang['newpassword_invalid'] = "Het nieuwe wachtwoord moet ten minste één hoofdletter, één kleine letter en één cijfer bevatten.";
$lang['newpassword_nomatch'] = "De nieuwe wachtwoorden zijn niet gelijk.";
$lang['newpassword_match'] = "Het nieuwe wachtwoord is hetzelfde als het oude wachtwoord.";

$lang['email_short'] = "Je e-mailadres is te kort.";
$lang['email_long'] = "Je e-mailadres is te lang.";
$lang['email_invalid'] = "Je e-mailadres is niet juist.";
$lang['email_incorrect'] = "Je e-mailadres is fout.";
$lang['email_banned'] = "Dit e-mailadres is niet toegestaan.";
$lang['email_changed'] = "Je e-mailadres is met succes aangepast.";

$lang['newemail_match'] = "Het nieuwe e-mailadres is hetzelfde als het oude e-mailadres.";

$lang['account_inactive'] = "Je account is nog niet geactiveerd.";
$lang['account_activated'] = "Je account is geactiveerd.";

$lang['logged_in'] = "Je bent nu ingelogd.";
$lang['logged_out'] = "Je bent nu uitgelogd.";

$lang['system_error'] = "Er is iets fout gegaan. Probeer het opnieuw.";

$lang['register_success'] = "Je account is aangemaakt. Een activatiemail is verstuurd naar je e-mailadres.";
$lang['register_success_emailmessage_suppressed'] = "Je account is aangemaakt.";
$lang['email_taken'] = "Het e-mailadres is al in gebruik.";

$lang['resetkey_invalid'] = "De herstelsleutel is niet juist.";
$lang['resetkey_incorrect'] = "De herstelsleutel is fout.";
$lang['resetkey_expired'] = "De geldigheid van de herstelsleutel is verlopen.";
$lang['password_reset'] = "Het wachtwoord is met succes aangepast.";

$lang['activationkey_invalid'] = "De activatiesleutel is niet juist.";
$lang['activationkey_incorrect'] = "De activatiesleutel is fout.";
$lang['activationkey_expired'] = "De geldigheid van de activatiesleutel is verlopen.";

$lang['reset_requested'] = "De aanvraag voor het herstellen van je wachtwoord is verzonden naar je e-mailadres.";
$lang['reset_requested_emailmessage_suppressed'] = "De aanvraag voor het herstellen van je wachtwoord is aangemaakt.";
$lang['reset_exists'] = "De aanvraag voor het herstellen van je wachtwoord is reeds gedaan.";

$lang['already_activated'] = "Je account is reeds geactiveerd.";
$lang['activation_sent'] = "De activatiemail is verstuurd.";
$lang['activation_exists'] = "De activatiemail is reeds verstuurd.";

$lang['email_activation_subject'] = '%s - Activateer je account';
$lang['email_activation_body'] = 'Hallo,<br/><br/> Om in te loggen in je account, moet je eerst je account activeren door te klikken op deze link: <strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/> Vervolgens moet je deze activatiesleutel gebruiken: <strong>%3$s</strong><br/><br/> Als je recentelijk geen account op %1$s hebt geprobeerd aan te maken, dan is dit bericht foutief verstuurd - gelieve het te negeren.';
$lang['email_activation_altbody'] = 'Hallo, ' . "\n\n" . 'Om in te loggen in je account, moet je eerst je account activeren door te klikken op deze link:' . "\n" . '%1$s/%2$s' . "\n\n" . 'Vervolgens moet je deze activatiesleutel gebruiken: %3$s' . "\n\n" . 'Als je recentelijk geen account op %1$s hebt geprobeerd aan te maken, dan is dit bericht foutief verstuurd - gelieve het te negeren.';

$lang['email_reset_subject'] = '%s - Herstelaanvraag voor je wachtwoord';
$lang['email_reset_body'] = 'Hallo,<br/><br/>Om je wachtwoord te herstellen, klik op deze link:<br/><br/><strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/> Vervolgens moet je deze herstelsleutel gebruiken: <strong>%3$s</strong><br/><br/>Als je recentelijk geen herstelsleutel voor je wachtwoord hebt aangevraagd op %1$s, dan is dit bericht foutief verstuurd - gelieve het te negeren.';
$lang['email_reset_altbody'] = 'Hallo, ' . "\n\n" . 'Om je wachtwoord te herstellen, klik op deze link:' . "\n" . '%1$s/%2$s' . "\n\n" . 'Vervolgens moet je deze herstelsleutel gebruiken: %3$s' . "\n\n" . 'Als je recentelijk geen account op %1$s hebt geprobeerd aan te maken, dan is dit bericht foutief verstuurd - gelieve het te negeren.';

$lang['account_deleted'] = "Je account is met succes verwijderd.";
$lang['function_disabled'] = "Deze functie is uitgeschakeld.";
$lang['account_not_found'] = "Geen account gevonden met deze e-mailadres.";

return $lang;
