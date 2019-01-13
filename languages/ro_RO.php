<?php
$lang = array();

$lang['user_blocked'] = "În prezent, sunteți blocat din sistem.";
$lang['user_verify_failed'] = "Codul Captcha nu a fost valid.";

$lang['email_password_invalid'] = "Adresa e-mail / parola nu este validă.";
$lang['email_password_incorrect'] = "Adresa email / parola incorecte.";
$lang['remember_me_invalid'] = "Câmpul de amintire este nevalid.";

$lang['password_short'] = "Parola este prea scurtă.";
$lang['password_weak'] = "Parola este prea slabă.";
$lang['password_nomatch'] = "Parolele nu se potrivesc.";
$lang['password_changed'] = "Parola a fost schimbată cu succes.";
$lang['password_incorrect'] = "Parola curentă este incorectă.";
$lang['password_notvalid'] = "Parola curentă este invalidă.";

$lang['newpassword_short'] = "Parola nouă este prea scurtă.";
$lang['newpassword_long'] = "Parola nouă este prea lungă.";
$lang['newpassword_invalid'] = "Parola nouă trebuie să conțină cel puțin un caractere majuscule și majuscule și cel puțin o cifră.";
$lang['newpassword_nomatch'] = "Parolele noi nu se potrivesc.";
$lang['newpassword_match'] = "Parola nouă este identică cu parola veche.";

$lang['email_short'] = "Adresa de e-mail este prea scurtă.";
$lang['email_long'] = "Adresa de e-mail este prea lungă.";
$lang['email_invalid'] = "Adresa de e-mail este prea invalidă.";
$lang['email_incorrect'] = "Adresa de e-mail este prea incorectă.";
$lang['email_banned'] = "Această adresă de e-mail nu este permisă.";
$lang['email_changed'] = "Adresa de e-mail a fost modificată cu succes.";

$lang['newemail_match'] = "E-mailul nou corespunde e-mailului anterior.";

$lang['account_inactive'] = "Contul nu a fost încă activat.";
$lang['account_activated'] = "Contul a fost activat.";

$lang['logged_in'] = "Acum sunteți conectat (ă).";
$lang['logged_out'] = "Acum sunteți deconectat (ă).";

$lang['system_error'] = "A apărut o eroare de sistem. Vă rugăm să încercați din nou.";

$lang['register_success'] = "Cont creat. E-mailul de activare a fost trimis la e-mail.";
$lang['register_success_emailmessage_suppressed'] = "Cont creat.";
$lang['email_taken'] = "Adresa de email este deja folosită.";

$lang['resetkey_invalid'] = "Cheia de resetare este invalidă.";
$lang['resetkey_incorrect'] = "Cheia de resetare este incorectă.";
$lang['resetkey_expired'] = "Cheia de resetare a expirat.";
$lang['password_reset'] = "Resetarea parolei reușită.";

$lang['activationkey_invalid'] = "Cheia de activare este invalidă.";
$lang['activationkey_incorrect'] = "Cheia de activare este incorectă.";
$lang['activationkey_expired'] = "Cheia de activare a expirat.";

$lang['reset_requested'] = "Solicitarea de resetare a parolei a fost trimisă la adresa de e-mail.";
$lang['reset_requested_emailmessage_suppressed'] = "Solicitarea de resetare a parolei a fost creată.";
$lang['reset_exists'] = "O solicitare de resetare există deja.";

$lang['already_activated'] = "Contul este deja activat.";
$lang['activation_sent'] = "E-mailul de activare a fost trimis.";
$lang['activation_exists'] = "Un e-mail de activare a fost deja trimis.";

$lang['email_activation_subject'] = '%s - Activează contul';
$lang['email_activation_body'] = 'Buna ziua ,<br/><br/> Pentru a vă putea conecta la contul dvs., trebuie mai întâi să vă activați contul făcând clic pe următorul link : <strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/> Apoi trebuie să utilizați următoarea cheie de activare: <strong>%3$s</strong><br/><br/> Dacă nu ați solicitat o cheie de resetare a parolei pe siteul %1$s recent, atunci acest mesaj a fost trimis în mod eronat, vă rugăm să îl ignorați.';
$lang['email_activation_altbody'] = 'Buna ziua, ' . "\n\n" . 'Pentru a vă putea conecta la contul dvs., trebuie mai întâi să vă activați contul făcând clic pe următorul link :' . "\n" . '%1$s/%2$s' . "\n\n" . 'Apoi trebuie să utilizați următoarea cheie de activare: %3$s' . "\n\n" . 'Dacă nu ați solicitat o cheie de resetare a parolei pe siteul %1$s recent, atunci acest mesaj a fost trimis în mod eronat, vă rugăm să îl ignorați.';

$lang['email_reset_subject'] = '%s - Solicitare pentru resetarea parolei';
$lang['email_reset_body'] = 'Buna ziua,<br/><br/>Pentru a vă reseta parola, faceți clic pe următorul link :<br/><br/><strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/>Apoi trebuie să utilizați următoarea cheie de resetare a parolei: <strong>%3$s</strong><br/><br/>Dacă nu ați solicitat o cheie de resetare a parolei pe siteul %1$s recent, atunci acest mesaj a fost trimis în mod eronat, vă rugăm să îl ignorați.';
$lang['email_reset_altbody'] = 'Buna ziua, ' . "\n\n" . 'Pentru a vă reseta parola, faceți clic pe următorul link :' . "\n" . '%1$s/%2$s' . "\n\n" . 'Apoi trebuie să utilizați următoarea cheie de resetare a parolei: %3$s' . "\n\n" . 'Dacă nu ați solicitat o cheie de resetare a parolei pe siteul %1$s recent, atunci acest mesaj a fost trimis în mod eronat, vă rugăm să îl ignorați.';

$lang['account_deleted'] = "Contul a fost șters cu succes.";
$lang['function_disabled'] = "Această funcție a fost dezactivată.";
$lang['account_not_found'] = "Nu s-a găsit un cont cu adresa de e-mail respectivă.";

return $lang;
