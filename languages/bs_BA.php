<?php
$lang = array();

$lang['user_blocked'] = "Vaš račun je zaključan.";
$lang['user_verify_failed'] = "Captcha kod nije ispravan.";

$lang['email_password_invalid'] = "Email adresa ili šifra su neispravni.";
$lang['email_password_incorrect'] = "Email adresa ili šifra su netačni.";
$lang['remember_me_invalid'] = "Polje zapamti me je neispravno.";

$lang['password_short'] = "Šifra je prekratka.";
$lang['password_weak'] = "Šifra je preslaba.";
$lang['password_nomatch'] = "Šifre se ne podudaraju.";
$lang['password_changed'] = "Šifra uspješno promijenjena.";
$lang['password_incorrect'] = "Trenutna šifra nije tačna.";
$lang['password_notvalid'] = "Šifra nije validna.";

$lang['newpassword_short'] = "Nova šifra je prekratka.";
$lang['newpassword_long'] = "Nova šifra je preduga.";
$lang['newpassword_invalid'] = "Nova šifra mora sadržati najmanje jedno veliko i malo slovo, i barem jedan broj.";
$lang['newpassword_nomatch'] = "Nove šifre se ne podudaraju.";
$lang['newpassword_match'] = "Nova šifra je ista kao prethodna.";

$lang['email_short'] = "Email adresa je prekratka.";
$lang['email_long'] = "Email adresa je preduga.";
$lang['email_invalid'] = "Email adresa nije ispravna.";
$lang['email_incorrect'] = "Email adresa je netačna.";
$lang['email_banned'] = "Ova email adresa je zabranjena.";
$lang['email_changed'] = "Email adresa uspješno promijenjena.";

$lang['newemail_match'] = "Nova email adresa je ista kao prethodna.";

$lang['account_inactive'] = "Račun još uvijek nije aktiviran.";
$lang['account_activated'] = "Račun aktiviran.";

$lang['logged_in'] = "Sada ste prijavljeni.";
$lang['logged_out'] = "Sada ste odjavljeni.";

$lang['system_error'] = "Dogodila se sistemska greška. Molimo pokušajte ponovo.";

$lang['register_success'] = "Račun izrađen. Provjerite Vaš email za aktivaciju.";
$lang['register_success_emailmessage_suppressed'] = "Račun izrađen.";
$lang['email_taken'] = "Email adresa je već iskorištena.";

$lang['resetkey_invalid'] = "Ključ za resetovanje nije ispravan.";
$lang['resetkey_incorrect'] = "Ključ za resetovanje nije tačan.";
$lang['resetkey_expired'] = "Ključ za resetovanje je istekao.";
$lang['password_reset'] = "Šifra uspješno promijenjena.";

$lang['activationkey_invalid'] = "Aktivacijski ključ nije ispravan.";
$lang['activationkey_incorrect'] = "Aktivacijski ključ nije tačan.";
$lang['activationkey_expired'] = "Aktivacijski ključ je istekao.";

$lang['reset_requested'] = "Zahtjev za promjenom šifre je poslan na email.";
$lang['reset_requested_emailmessage_suppressed'] = "Zahtjev za promjenom šifre je kreiran.";
$lang['reset_exists'] = "Zahtjev za promjenom šifre već postoji. Ponovo ćete moći promijeniti šifru na %s";             //@todo: updated 2018-06-28

$lang['already_activated'] = "Račun je već aktiviran.";
$lang['activation_sent'] = "Aktivacijski email je poslan.";
$lang['activation_exists'] = "Aktivacijski email je već poslan. Slijedeće re-aktiviranje će biti moguće na %s";       //@todo: updated 2018-06-28

$lang['email_activation_subject'] = '%s - Aktiviranje računa';
$lang['email_activation_body'] = 'Zdravo,<br/><br/> Da biste se mogli prijaviti na Vaš račun prvo morate aktivirati Vaš račun klikom na slijedeći link : <strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/> Zatim je potrebno da iskoristite slijedeći ključ za aktivaciju: <strong>%3$s</strong><br/><br/> Ako se nedavno niste registrovali na %1$s onda je ova poruka do Vas dospjela greškom, molimo ignorišite je.';
$lang['email_activation_altbody'] = 'Zdravo, ' . "\n\n" . 'Da biste se mogli prijaviti na Vaš račun prvo morate aktivirati Vaš račun klikom na slijedeći link :' . "\n" . '%1$s/%2$s' . "\n\n" . 'Zatim je potrebno da iskoristite slijedeći ključ za aktivaciju: %3$s' . "\n\n" . 'Ako se nedavno niste registrovali na %1$s onda je ova poruka do Vas dospjela greškom, molimo ignorišite je.';

$lang['email_reset_subject'] = '%s - Zahtjev za promjenom šifre';
$lang['email_reset_body'] = 'Zdravo,<br/><br/>Da promijenite Vašu šifru kliknite na slijedeći link :<br/><br/><strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/>Zatim je potrebno da iskoristite slijedeći ključ za promjenu šifre: <strong>%3$s</strong><br/><br/>Ako niste zatražili promjenu šifre na %1$s nedavno onda ste dobili ovu poruku greškom, pa je ignorišite.';
$lang['email_reset_altbody'] = 'Zdravo, ' . "\n\n" . 'Da promijenite Vašu šifru posjetite slijedeći link :' . "\n" . '%1$s/%2$s' . "\n\n" . 'Zatim je potrebno da iskoristite slijedeći ključ za promjenu šifre: %3$s' . "\n\n" . 'Ako niste zatražili promjenu šifre na %1$s nedavno onda ste dobili ovu poruku greškom, pa je ignorišite.';

$lang['account_deleted'] = "Račun uspješno izbrisan.";
$lang['function_disabled'] = "Ova funkcija je onemogućena.";
$lang['account_not_found'] = "Nije pronađen niti jedan račun sa ovom email adresom";

return $lang;
