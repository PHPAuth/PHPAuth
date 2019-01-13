<?php
$lang = array();

$lang['user_blocked'] = "Jelenleg ki vagy zárva a rendszerből";
$lang['user_verify_failed'] = "Hibás Captcha kód.";

$lang['email_password_invalid'] = "Helytelen Email cím / jelszó.";
$lang['email_password_incorrect'] = "Hibás Email cím / jelszó.";
$lang['remember_me_invalid'] = "Az Emlékezz Rám mező helytelen.";

$lang['password_short'] = "A megadott jelszó túl rövid.";
$lang['password_weak'] = "A megadott jelszó túl gyenge.";
$lang['password_nomatch'] = "A jelszavak nem egyeznek.";
$lang['password_changed'] = "Sikeres jelszóváltozatás.";
$lang['password_incorrect'] = "A megadott jelszó hibás.";
$lang['password_notvalid'] = "A megadott jelszó helytelen";

$lang['newpassword_short'] = "Az új jelszó túl rövid.";
$lang['newpassword_long'] = "Az új jelszó túl hosszú.";
$lang['newpassword_invalid'] = "A jelszónak tartalmaznia kell, legalább egy nagybetűs karaktert és egy számot.";
$lang['newpassword_nomatch'] = "A Jelszavak nem egyeznek.";
$lang['newpassword_match'] = "Az új jelszó ugyanaz mint a régi.";

$lang['email_short'] = "Email cím túl rövid.";
$lang['email_long'] = "Email cím túl hosszú.";
$lang['email_invalid'] = "Helytelen email cím.";
$lang['email_incorrect'] = "Hibás email cím.";
$lang['email_banned'] = "Ez az email cím nem engedélyezet.";
$lang['email_changed'] = "Az email cím megváltoztatva.";

$lang['newemail_match'] = "Az új email cím megegyezik a régivel.";

$lang['account_inactive'] = "A fiók még nincs aktiválva.";
$lang['account_activated'] = "Fiók aktiválva.";

$lang['logged_in'] = "Sikeresen bejelentkeztél.";
$lang['logged_out'] = "Sikeresen kijelentkeztél.";

$lang['system_error'] = "Rendszerhiba. Próbáld újra.";

$lang['register_success'] = "Fiók létrehozva. Az aktivációs emailt kiküldtük a megadott emial címre.";
$lang['register_success_emailmessage_suppressed'] = "Fiók létrehozva.";
$lang['email_taken'] = "Az email cím már használatban van.";

$lang['resetkey_invalid'] = "Hibás visszaálító kulcs.";
$lang['resetkey_incorrect'] = "Helytelen visszaálító kulcs.";
$lang['resetkey_expired'] = "A visszaálító kulcs elévült.";
$lang['password_reset'] = "Sikeres jelszó helyreállítás.";

$lang['activationkey_invalid'] = "Hibás aktivációs kulcs.";
$lang['activationkey_incorrect'] = "Helytelen aktivációs kulcs.";
$lang['activationkey_expired'] = "Az aktivációs kulcs elévült.";

$lang['reset_requested'] = "Jelszó helyreállításhoz szükséges email elküldve.";
$lang['reset_requested_emailmessage_suppressed'] = "Jelszó helyreállítási kérelem fogadva.";
$lang['reset_exists'] = "Jelszó helyreállítás már folyamatban van.";

$lang['already_activated'] = "A fiók már aktiválva van.";
$lang['activation_sent'] = "Az aktivációs email elküldve.";
$lang['activation_exists'] = "Az aktivációs emailt már kiküldtük.";

$lang['email_activation_subject'] = '%s - Fiók aktivákás';
$lang['email_activation_body'] = 'Üdv,<br/><br/> Ahhoz hogy be tudj jelentkezni, először aktiválnod kell a fiókod. Az aktiváláshoz kattints az alábbi linkre: <strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/> Ezt követően add meg az aktiváláshoz szükséges kulcsot: <strong>%3$s</strong><br/><br/> Ha nem te nem regisztráltál fiókot a %1$s -on,akkor kérlek hagyd figyelmen kívűl ezt a levelet.';
$lang['email_activation_altbody'] = 'Üdv, ' . "\n\n" . 'Ahhoz hogy be tudj jelentkezni, először aktiválnod kell a fiókod. Az aktiváláshoz kattints az alábbi linkre:' . "\n" . '%1$s/%2$s' . "\n\n" . 'Ezt követően add meg az aktiváláshoz szükséges kulcsot: %3$s' . "\n\n" . 'Ha nem te nem regisztráltál fiókot a %1$s -on,akkor kérlek hagyd figyelmen kívűl ezt a levelet.';

$lang['email_reset_subject'] = '%s - Jelszó helyreállítás';
$lang['email_reset_body'] = 'Üdv,<br/><br/>Új jelszó létrehozásához kattints az alábbi linkre:<br/><br/><strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/>Jelszó helyreállításhoz szüksége kulcs: <strong>%3$s</strong><br/><br/>Ha nem te kezdeményezted a jelszó helyreállítást a %1$s -on,akkor kérlek hagyd figyelmen kívűl ezt a levelet.';
$lang['email_reset_altbody'] = 'Üdv, ' . "\n\n" . 'Új jelszó létrehozásához kattints az alábbi linkre:' . "\n" . '%1$s/%2$s' . "\n\n" . 'Jelszó helyreállításhoz szüksége kulcs: %3$s' . "\n\n" . 'Ha nem te kezdeményezted a jelszó helyreállítást a %1$s -on,akkor kérlek hagyd figyelmen kívűl ezt a levelet.';

$lang['account_deleted'] = "A fiók sikeresen törőlve.";
$lang['function_disabled'] = "Ez a funkció ki lett kapcsolva.";
$lang['account_not_found'] = "Az e-mail címmel nem található fiók.";

return $lang;
