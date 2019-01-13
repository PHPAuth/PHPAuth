<?php
$lang = array();

$lang['user_blocked'] = "Trenutno ste blokirani.";
$lang['user_verify_failed'] = "Varnostna koda je nepravilna.";

$lang['email_password_invalid'] = "E-naslov ali geslo je neveljaven.";
$lang['email_password_incorrect'] = "E-naslov ali geslo je nepravilen.";
$lang['remember_me_invalid'] = "Polje zapomni si me je neveljavno.";

$lang['password_short'] = "Geslo je prekratko.";
$lang['password_weak'] = "Geslo je prešibko.";
$lang['password_nomatch'] = "Gesli se ne ujemata.";
$lang['password_changed'] = "Geslo je uspešno spremenjeno.";
$lang['password_incorrect'] = "Trenutno geslo je napačno.";
$lang['password_notvalid'] = "Geslo je neveljavno.";

$lang['newpassword_short'] = "Novo geslo je prekratko.";
$lang['newpassword_long'] = "Novo geslo je predolgo.";
$lang['newpassword_invalid'] = "Novo geslo mora imeti vsaj eno veliko in vsaj eno malo črko ter številko.";
$lang['newpassword_nomatch'] = "Novi gesli se ne ujemata.";
$lang['newpassword_match'] = "Novo geslo je enako staremu.";

$lang['email_short'] = "E-naslov je prekratek.";
$lang['email_long'] = "E-naslov je predolg.";
$lang['email_invalid'] = "E-naslov je neveljaven.";
$lang['email_incorrect'] = "E-naslov je napačen.";
$lang['email_banned'] = "Ta e-naslov ni dovoljen.";
$lang['email_changed'] = "E-naslov je uspešno spremenjen.";

$lang['newemail_match'] = "Nov e-naslov je enak staremu..";

$lang['account_inactive'] = "Račun še ni bil aktiviran.";
$lang['account_activated'] = "Račun je aktiviran.";

$lang['logged_in'] = "Uspešno ste se prijavili.";
$lang['logged_out'] = "Uspešno ste se odjavili.";

$lang['system_error'] = "Pojavila se je sistemska napaka. Poskusite ponovno.";

$lang['register_success'] = "Račun je uspešno ustvarjen. Aktivacijska e-pošta je poslana na vaš e-naslov.";
$lang['register_success_emailmessage_suppressed'] = "Račun uspešno ustvarjen.";
$lang['email_taken'] = "Ta e-naslov je že uporabljen.";

$lang['resetkey_invalid'] = "Ključ za ponastavitev ni veljaven.";
$lang['resetkey_incorrect'] = "Ključ za ponastavitev ni pravilen.";
$lang['resetkey_expired'] = "Ključ za ponastavitev je potekel.";
$lang['password_reset'] = "Geslo je uspešno ponastavljeno.";

$lang['activationkey_invalid'] = "Aktivacijski ključ ni veljaven.";
$lang['activationkey_incorrect'] = "Aktivacijski ključ ni pravilen.";
$lang['activationkey_expired'] = "Aktivacijski ključ je potekel.";

$lang['reset_requested'] = "Ponastavitveno e-sporočilo je poslano na e-naslov.";
$lang['reset_requested_emailmessage_suppressed'] = "Zahteva za ponastavitev gesla je ustvarjena.";
$lang['reset_exists'] = "Zahteva na ponastavitev že obstaja. Nova bo na voljo %s";

$lang['already_activated'] = "Račun je že aktiviran.";
$lang['activation_sent'] = "Aktivacijsko e-sporočilo je poslano na e-naslov.";
$lang['activation_exists'] = "Aktivacijsko e-sporočilo je že bilo poslano. Nova reaktivacija bo na voljo %s";

$lang['email_activation_subject'] = '%s - Aktivacija računa';
$lang['email_activation_body'] = 'Pozdravljeni,<br/><br/>Za prijavo v račun ga morate najprej aktivirati s klikom na sledečo povezavo: <strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/>Nato morate uporabiti sledeči aktivacijslki ključ: <strong>%3$s</strong><br/><br/> Če se na %1$s niste registrirali, je to sporočilo napaka in ga ignorirajte.';
$lang['email_activation_altbody'] = 'Pozdravljeni, ' . "\n\n" . 'Za prijavo v račun ga morate najprej aktivirati s klikom na sledečo povezavo:' . "\n" . '%1$s/%2$s' . "\n\n" . 'Nato morate uporabiti sledeči aktivacijslki ključ: %3$s' . "\n\n" . 'Če se na %1$s niste registrirali, je to sporočilo napaka in ga ignorirajte.';

$lang['email_reset_subject'] = '%s - Zahteva na ponastavitev gesla';
$lang['email_reset_body'] = 'Pozdravljeni,<br/><br/>Za ponastavitev gesla obiščite sledečo povezavo:<br/><br/><strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/>Nato morate uporabiti sledeči aktivacijslki ključ <strong>%3$s</strong><br/><br/>Če na %1$s gesla niste želeli ponastaviti, je to sporočilo napaka in ga ignorirajte.';
$lang['email_reset_altbody'] = 'Pozdravljeni, ' . "\n\n" . 'Za ponastavitev gesla obiščite sledečo povezavo:' . "\n" . '%1$s/%2$s' . "\n\n" . 'Nato morate uporabiti sledeči aktivacijslki ključ: %3$s' . "\n\n" . 'Če na %1$s gesla niste želeli ponastaviti, je to sporočilo napaka in ga ignorirajte.';

$lang['account_deleted'] = "Račun je uspešno izbrisan.";
$lang['function_disabled'] = "Ta funkcija je onemogočena.";
$lang['account_not_found'] = "Nobenega računa s tem e-poštnim naslovom ni.";

return $lang;
