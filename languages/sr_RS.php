<?php

$lang = array();

$lang['user_blocked'] = "Vaš nalog je blokiran od strane sistema.";
$lang['user_verify_failed'] = "Kod za zaštitu od spama koji ste uneli nije ispravan.";

$lang['email_password_invalid'] = "Email adresa / lozinka su nevažeći.";
$lang['email_password_incorrect'] = "Email adresa / lozinka su netačni.";
$lang['remember_me_invalid'] = "The remember me field is invalid.";

$lang['password_short'] = "Lozinka je prektratka.";
$lang['password_weak'] = "Lozinka mora biti jača (slova, brojevi, karakteri...).";
$lang['password_nomatch'] = "Lozinke se ne poklapaju.";
$lang['password_changed'] = "Lozinka je uspešno izmenjena.";
$lang['password_incorrect'] = "Trenutna lozinka je netačna.";
$lang['password_notvalid'] = "Lozinka je netačna.";

$lang['newpassword_short'] = "Nova lozinka je prekratka.";
$lang['newpassword_long'] = "Nova lozinka je predugačka.";
$lang['newpassword_invalid'] = "Nova lozinka mora sadržati najmanje jedno veliko i jedno malo slovo i najmanje jedan znak ili broj.";
$lang['newpassword_nomatch'] = "Nove lozinke se ne poklapaju.";
$lang['newpassword_match'] = "Nova lozinka je identična prethodnoj.";

$lang['email_short'] = "Email adresa je prekratka.";
$lang['email_long'] = "Email adresa je predugačka.";
$lang['email_invalid'] = "Email adresa je nevažeća.";
$lang['email_incorrect'] = "Email adresa je netačna.";
$lang['email_banned'] = "Uneta email adresa nije odobrena.";
$lang['email_changed'] = "Promena Email adrese je uspešna";

$lang['newemail_match'] = "Nova Email adresa poklapa se sa prethodnom.";

$lang['account_inactive'] = "Vaš nalog još uvek nije aktiviran.";
$lang['account_activated'] = "Nalog je aktiviran.";

$lang['logged_in'] = "Uspešno ste se prijavili.";
$lang['logged_out'] = "Uspešno ste se odjavili.";

$lang['system_error'] = "Došlo je do greške, molimo pokušajte ponovo.";

$lang['register_success'] = "Nalog je kreiran. Aktivacioni link je poslat na Vašu email adresu.";
$lang['register_success_emailmessage_suppressed'] = "Nalog je kreiran.";
$lang['email_taken'] = "Uneta Email adresa je već u upotrebi.";

$lang['resetkey_invalid'] = "Kod za reset je nevažeći.";
$lang['resetkey_incorrect'] = "Kod za reset je netačan.";
$lang['resetkey_expired'] = "Kod za reset je istekao.";
$lang['password_reset'] = "Lozinka je uspešno resetovana.";

$lang['activationkey_invalid'] = "Aktivacioni kod je nevažeći.";
$lang['activationkey_incorrect'] = "Aktivacioni kod je netačan.";
$lang['activationkey_expired'] = "Aktivacioni kod je istekao.";

$lang['reset_requested'] = "Zahtev za resetovanje lozinke je poslat na Vašu email adresu.";
$lang['reset_requested_emailmessage_suppressed'] = "Zahtev za resetovanje lozinke je kreiran.";
$lang['reset_exists'] = "Zahtev za resetovanje lozinke je već poslat.";

$lang['already_activated'] = "Vaš nalog je već aktiviran.";
$lang['activation_sent'] = "Aktivacioni Email je poslat.";
$lang['activation_exists'] = "Aktivacioni Email je već poslat.";

$lang['email_activation_subject'] = '%s - Aktivacija naloga';
$lang['email_activation_body'] = 'Poštovani,<br/><br/>  Da biste mogli da pristupite nalogu potrebno je da otvorite link: <strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/> i potom unesete aktivacioni kod: <strong>%3$s</strong><br/><br/> Ukoliko niste Vi naručili aktivacioni kod, niti pokušali da se registrujete na našem sajtu, molimo Vas da ignorišete ovu poruku. %1$s ';
$lang['email_activation_altbody'] = 'Poštovani, ' . "\n\n" . 'Da biste mogli da pristupite nalogu potrebno je da otvorite link:' . "\n" . '%1$s/%2$s' . "\n\n" . 'i potom unesete aktivacioni kod: %3$s' . "\n\n" . 'Ukoliko niste Vi naručili aktivacioni kod, niti pokušali da se registrujete na našem sajtu, molimo Vas da ignorišete ovu poruku. %1$s';

$lang['email_reset_subject'] = '%s - Zahtev za resetovanje lozinke';
$lang['email_reset_body'] = 'Poštovani,<br/><br/> Da biste resetovali lozinku potrebno je da kliknete na sledeći link:<br/><br/><strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/>i potom upotrebite sledeći kod za resetovanje lozinke: <strong>%3$s</strong><br/><br/>Ukoliko niste Vi naručili kod za resetovanje lozinke, molimo Vas da ignorišete ovu poruku. %1$s ';
$lang['email_reset_altbody'] = 'Poštovani, ' . "\n\n" . 'Da biste resetovali lozinku potrebno je da kliknete na sledeći link:' . "\n" . '%1$s/%2$s' . "\n\n" . 'i potom upotrebite sledeći kod za resetovanje lozinke: %3$s' . "\n\n" . 'Ukoliko niste Vi naručili kod za resetovanje lozinke, molimo Vas da ignorišete ovu poruku. %1$s';

$lang['account_deleted'] = "Nalog je uspešno obrisan.";
$lang['function_disabled'] = "Ova opcija nije dostupna.";
$lang['account_not_found'] = "Nije pronađen nalog sa tim imejlom.";

return $lang;
