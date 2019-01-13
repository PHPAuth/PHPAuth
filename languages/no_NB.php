<?php
$lang = array();

$lang['user_blocked'] = "Du er for øyeblikket låst ute av systemet.";
$lang['user_verify_failed'] = "Captcha-koden var ugyldig.";

$lang['email_password_invalid'] = "Epost eller passord er ugyldig.";
$lang['email_password_incorrect'] = "Epost eller passord er feil.";
$lang['remember_me_invalid'] = "Husk meg-feltet er ugyldig.";

$lang['password_short'] = "Passordet er for kort.";
$lang['password_weak'] = "Passordet er for svakt.";
$lang['password_nomatch'] = "Passordene samsvarer ikke.";
$lang['password_changed'] = "Endring av passord var vellykket.";
$lang['password_incorrect'] = "Nåværende passord er feil.";
$lang['password_notvalid'] = "Passord er ugyldig.";

$lang['newpassword_short'] = "Nytt passord er for kort.";
$lang['newpassword_long'] = "Nytt passord er for langt.";
$lang['newpassword_invalid'] = "Nytt passord må inneholde minst én stor og liten bokstav, samt minst ett tall";
$lang['newpassword_nomatch'] = "De nye passordene samsvarer ikke";
$lang['newpassword_match'] = "Det nye passordet er det samme som det gamle passordet.";

$lang['email_short'] = "Epostadressen er for kort.";
$lang['email_long'] = "Epostaddressen er for lang";
$lang['email_invalid'] = "Epostaddressen er ugyldig.";
$lang['email_incorrect'] = "Epostadressen er feil.";
$lang['email_banned'] = "Denne epostadressen er ikke tillatt.";
$lang['email_changed'] = "Endring av epostadresse var vellykket.";

$lang['newemail_match'] = "Den nye epostadressen er lik den gamle.";

$lang['account_inactive'] = "Kontoen har ikke blitt aktivert enda.";
$lang['account_activated'] = "Kontoen ble aktivert.";

$lang['logged_in'] = "Du er nå logget inn.";
$lang['logged_out'] = "Du er nå logget ut.";

$lang['system_error'] = "En systemfeil har oppstått. Vær vennlig og prøv igjen.";

$lang['register_success'] = "Kontoen ble opprettet. En aktiveringsepost har blitt sendt på epost.";
$lang['register_success_emailmessage_suppressed'] = "Kontoen ble opprettet.";
$lang['email_taken'] = "Denne epostadressen er allerede i bruk.";

$lang['resetkey_invalid'] = "Tilbakestillingsnøkkelen er ugyldig.";
$lang['resetkey_incorrect'] = "Tilbakestillingsnøkkelen er feil.";
$lang['resetkey_expired'] = "Tilbakestillingsnøkkelen er utgått.";
$lang['password_reset'] = "Tilbakestilling av passordet var vellykket.";

$lang['activationkey_invalid'] = "Aktiveringsnøkkelen er ugyldig.";
$lang['activationkey_incorrect'] = "Aktiveringsnøkkelen er feil.";
$lang['activationkey_expired'] = "Aktiveringsnøkkelen er utgått.";

$lang['reset_requested'] = "Epost for tilbakestilling av passord har blitt sendt.";
$lang['reset_requested_emailmessage_suppressed'] = "En forespørsel om tilbakestilling av passord har blitt opprettet.";
$lang['reset_exists'] = "Det finnes allerede en forespørsel om tilbakestilling.";

$lang['already_activated'] = "Kontoen er allerede aktivert.";
$lang['activation_sent'] = "Aktiveringsepost har blitt sendt.";
$lang['activation_exists'] = "En aktiveringsepost har allerede blitt sendt.";

$lang['email_activation_subject'] = '%s - Aktiver konto';
$lang['email_activation_body'] = 'Hei,<br/><br/> For å kunne logge inn på kontoen din må du først aktivere den ved å trykke på den følgende lenken: <strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/> Deretter må du bruke følgende aktiveringsnøkkel: <strong>%3$s</strong><br/><br/> Hvis du ikke meldte deg opp hos %1$s nylig, ble denne eposten sendt ved en feil. Vær snill og se bort fra den.';
$lang['email_activation_altbody'] = 'Hei, ' . "\n\n" . 'For å kunne logge inn på kontoen din må du først aktivere kontoen din ved å besøke følgende lenke:' . "\n" . '%1$s/%2$s' . "\n\n" . 'Deretter må du bruke følgende aktiveringsnøkkel: %3$s' . "\n\n" . 'Hvis du ikke meldte deg opp hos %1$s nylig, ble denne eposten sendt ved en feil. Vær snill og se bort fra den.';

$lang['email_reset_subject'] = '%s - Forespørsel om tilbakestilling av passord';
$lang['email_reset_body'] = 'Hei,<br/><br/>For å tilbakestille passordet ditt, trykk på følgende lenke:<br/><br/><strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/>Deretter må du bruke følgende tilbakestillingsnøkkel: <strong>%3$s</strong><br/><br/>Hvis du ikke ba om en tilbakestilling av passord hos %1$s nylig, ble denne eposten sendt ved en feil. Vær vennlig og se bort fra den.';
$lang['email_reset_altbody'] = 'Hei, ' . "\n\n" . 'For å tilbakestille passordet ditt, trykk på følgende lenke:' . "\n" . '%1$s/%2$s' . "\n\n" . 'Deretter må du bruke følgende tilbakestillingsnøkkel: %3$s' . "\n\n" . 'Hvis du ikke ba om en tilbakestilling av passord hos %1$s nylig, ble denne eposten sendt ved en feil. Vær vennlig og se bort fra den.';

$lang['account_deleted'] = "Sletting av konto var vellyket.";
$lang['function_disabled'] = "Denne funksjonen har blitt deaktivert.";
$lang['account_not_found'] = "Ingen konto funnet med den e-mail adressen.";

return $lang;
