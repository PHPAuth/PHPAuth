<?php
$lang = array();

$lang['user_blocked'] = "Du er i øjeblikket låst ude af systemet.";
$lang['user_verify_failed'] = "Captcha Code var ugyldig.";

$lang['email_password_invalid'] = "E-mail-adresse / password er ugyldige.";
$lang['email_password_incorrect'] = "E-mail-adresse / password er forkert.";
$lang['remember_me_invalid'] = "The remember me felt er ugyldigt.";

$lang['password_short'] = "Password er for kort.";
$lang['password_weak'] = "Password er for svagt.";
$lang['password_nomatch'] = "Passwords er ikke ens.";
$lang['password_changed'] = "Password ændret med succes.";
$lang['password_incorrect'] = "Nuværende adgangskode er forkert.";
$lang['password_notvalid'] = "Password er forkert.";

$lang['newpassword_short'] = "Ny password er for kort.";
$lang['newpassword_long'] = "Ny password er for langt.";
$lang['newpassword_invalid'] = "Ny adgangskode skal indeholde mindst ét stort og småt bogstav, og mindst et ciffer.";
$lang['newpassword_nomatch'] = "Nye adgangskoder er ikke ens.";
$lang['newpassword_match'] = "Ny adgangskode er den samme som den gamle adgangskode.";

$lang['email_short'] = "Email address er for kort.";
$lang['email_long'] = "Email address er for langt.";
$lang['email_invalid'] = "Email address er forkert.";
$lang['email_incorrect'] = "Email address er ugyldigt.";
$lang['email_banned'] = "This email address is not allowed.";
$lang['email_changed'] = "Email address ændret med succes.";

$lang['newemail_match'] = "Ny e-mail matcher tidligere e-mail.";

$lang['account_inactive'] = "Konto er endnu ikke blevet aktiveret.";
$lang['account_activated'] = "Konto aktiveret.";

$lang['logged_in'] = "Du er nu logget ind.";
$lang['logged_out'] = "Du er nu logget ud.";

$lang['system_error'] = "Der er fundet en systemfejl. Venligst prøv igen.";

$lang['register_success'] = "Konto oprettet. Aktivering e-mail sendt til e-mail.";
$lang['register_success_emailmessage_suppressed'] = "Konto oprettet.";
$lang['email_taken'] = "Den e-mail-adresse er allerede i brug.";

$lang['resetkey_invalid'] = "Reset key er ugyldig.";
$lang['resetkey_incorrect'] = "Reset key er forkert.";
$lang['resetkey_expired'] = "Reset key er udløbet.";
$lang['password_reset'] = "Password reset succes.";

$lang['activationkey_invalid'] = "Aktiveringsnøgle er ugyldig.";
$lang['activationkey_incorrect'] = "Aktiveringsnøgle er forkert.";
$lang['activationkey_expired'] = "Aktiveringsnøgle er udløbet.";

$lang['reset_requested'] = "Password reset anmodning sendt til e-mail-adresse.";
$lang['reset_requested_emailmessage_suppressed'] = "anmodning Password reset er oprettet.";
$lang['reset_exists'] = "En nulstilling anmodning findes allerede.";

$lang['already_activated'] = "Konto er allerede aktiveret.";
$lang['activation_sent'] = "Aktivering e-mail er blevet sendt.";
$lang['activation_exists'] = "En aktiveringsemail er allerede blevet sendt.";

$lang['email_activation_subject'] = '%s - Aktiver konto';
$lang['email_activation_body'] = 'Hello,<br/><br/> For at være i stand til at logge ind på din konto skal du først aktivere din konto ved at klikke på følgende link : <strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/> Derefter skal du bruge følgende aktiveringsnøgle: <strong>%3$s</strong><br/><br/> Hvis du ikke har registeret på %1$s for nylig så er denne besked blev sendt ved en fejl, venlisgt ignorere det.';
$lang['email_activation_altbody'] = 'Hello, ' . "\n\n" . 'For at være i stand til at logge ind på din konto skal du først aktivere din konto ved at besøge følgende link :' . "\n" . '%1$s/%2$s' . "\n\n" . 'Derefter skal du bruge følgende aktiveringsnøgle: %3$s' . "\n\n" . 'Hvis du ikke har registeret på %1$s for nylig så er denne besked blev sendt ved en fejl, venlisgt ignorere det.';

$lang['email_reset_subject'] = '%s - Password reset request';
$lang['email_reset_body'] = 'Hello,<br/><br/>Nulstill din adgangskode ved at klikke på følgende link :<br/><br/><strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/>Derefter skal du bruge følgende nøgle : <strong>%3$s</strong><br/><br/>Hvis du ikke har anmodet en password reset key på %1$s for nylig så er denne besked blev sendt ved en fejl, venlisgt ignorere det.';
$lang['email_reset_altbody'] = 'Hello, ' . "\n\n" . 'Nulstill din adgangskode ved at klikke på følgende link :' . "\n" . '%1$s/%2$s' . "\n\n" . 'Derefter skal du bruge følgende nøgle: %3$s' . "\n\n" . 'Hvis du ikke har anmodet en password reset key på %1$s for nylig så er denne besked blev sendt ved en fejl, venlisgt ignorere det.';

$lang['account_deleted'] = "Konto slettet.";
$lang['function_disabled'] = "Denne funktion er blevet deaktiveret.";
$lang['account_not_found'] = "Ingen konto fundet med den emailadresse.";

return $lang;
