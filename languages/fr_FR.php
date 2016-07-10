<?php
$lang = array();

$lang['user_blocked'] = "Vous &ecirc;tes actuellement bloqu&eacute;s du syst&egrave;me.";
$lang['user_verify_failed'] = "Code captcha invalide.";

$lang['email_password_invalid'] = "Adresse email / mot de passe invalide.";
$lang['email_password_incorrect'] = "Adresse email / mot de passe incorrect.";
$lang['remember_me_invalid'] = "Le champ se souvenir de moi est invalide.";

$lang['password_short'] = "Le mot de passe est trop court.";
$lang['password_weak'] = "Le mot de passe est trop faible.";
$lang['password_nomatch'] = "Les mots de passe ne sont pas identiques.";
$lang['password_changed'] = "Le mot de passe a bien &eacute;t&eacute; chang&eacute;.";
$lang['password_incorrect'] = "Le mot de passe actuel est incorrect.";
$lang['password_notvalid'] = "Le mot de passe est invalide.";

$lang['newpassword_short'] = "Le nouveau mot de passe est trop court.";
$lang['newpassword_long'] = "Le nouveau mot de passe est trop long.";
$lang['newpassword_invalid'] = "Le nouveau mot de passe doit contenir au moins un caractère en miniscule et en majuscule, et au moins un chiffre.";
$lang['newpassword_nomatch'] = "Les nouveaux mots de passe ne sont pas identiques.";
$lang['newpassword_match'] = "Le nouveau mot de passe est le m&ecirc;me que l'ancien.";

$lang['email_short'] = "L'adresse email est trop courte.";
$lang['email_long'] = "L'adresse email est trop longue.";
$lang['email_invalid'] = "L'adresse email est invalide.";
$lang['email_incorrect'] = "L'adresse email est incorrecte.";
$lang['email_banned'] = "Cette adresse email est interdite.";
$lang['email_changed'] = "L'adresse email a bien &eacute;t&eacute; chang&eacute;e.";

$lang['newemail_match'] = "La nouvelle adresse email est identique à l'adresse email actuelle.";

$lang['account_inactive'] = "Le compte n'a pas encore &eacute;t&eacute; activ&eacute;.";
$lang['account_activated'] = "Le compte est desormais activ&eacute;.";

$lang['logged_in'] = "Vous &ecirc;tes maintenant connect&eacute;s.";
$lang['logged_out'] = "Vous avez &eacute;t&eacute; deconnect&eacute;s.";

$lang['system_error'] = "Une erreur syst&egrave;me a &eacute;t&eacute; rencontr&eacute;e. Veuillez r&eacute;essayer.";

$lang['register_success'] = "Le compte a bien &eacute;t&eacute; cr&eacute;e. L'email d'activation vous a &eacute;t&eacute; envoy&eacute;.";
$lang['register_success_emailmessage_suppressed'] = "Le compte a bien &eacute;t&eacute; cr&eacute;e.";
$lang['email_taken'] = "L'adresse email est d&eacute;j&agrave; utilis&eacute;e.";

$lang['resetkey_invalid'] = "La cl&eacute; de r&eacute;initialisation est invalide.";
$lang['resetkey_incorrect'] = "La cl&eacute; de r&eacute;initialisation est incorrecte.";
$lang['resetkey_expired'] = "La cl&eacute; de r&eacute;initialisation est expir&eacute;e.";
$lang['password_reset'] = "Le mot de passe a bien &eacute;t&eacute; r&eacute;initialis&eacute;.";

$lang['activationkey_invalid'] = "La cl&eacute; d'activation est invalide.";
$lang['activationkey_incorrect'] = "La cl&eacute; d'activation est incorrecte.";
$lang['activationkey_expired'] = "La cl&eacute; d'activation est expir&eacute;e.";

$lang['reset_requested'] = "Une demande de r&eacute;initialisation de votre mot de passe a &eacute;t&eacute; envoy&eacute;.";
$lang['reset_requested_emailmessage_suppressed'] = "Une demande de r&eacute;initialisation de votre mot de passe a &eacutet&eacute cr&eacute&eacute.";
$lang['reset_exists'] = "Une demande de r&eacute;initialisation de votre mot de passe existe d&eacute;j&agrave;.";

$lang['already_activated'] = "Le compte est d&eacute;j&agrave; activ&eacute;.";
$lang['activation_sent'] = "L'email d'activation a bien &eacute;t&eacute; envoy&eacute;.";
$lang['activation_exists'] = "L'email d'activation a d&eacute;j&agrave; &eacute;t&eacute; envoy&eacute;.";

$lang['email_activation_subject'] = '%s - Activation de compte';
$lang['email_activation_body'] = 'Bonjour,<br/><br/> Pour pouvoir vous connecter vous devez d\'abord activer votre compte en cliquant sur le lien suivant :<strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/>Vous devrez utiliser cette clé d\'activation : <strong>%3$s</strong><br/><br/>\n Si vous ne souhaitez pas vous enregistrer sur %1$s vous pouvez ignorer ce message.';
$lang['email_activation_altbody'] = 'Bonjour,' . "\n\n" . 'Pour pouvoir vous connecter vous devez d\'abord activer votre compte en cliquant sur le lien suivant :' . "\n" . '%1$s/%2$s' . "\n\n" . 'Vous devrez utiliser cette clé d\'activation : %3$s' . "\n\n" . 'Si vous ne souhaitez pas vous enregistrer sur %1$s vous pouvez ignorer ce message.';

$lang['email_reset_subject'] = '%s - Reinitialisation du mot de passe';
$lang['email_reset_body'] = 'Bonjour,<br/><br/>Pour reinitialiser votre mot de passe cliquez sur le lien suivant :<br/><br/><strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/>Vous devrez utiliser cette clé pour reinitialiser votre mot de passe: <strong>%3$s</strong><br/><br/>Si vous n\'avez pas demandé une réinitialisation du mot de passe, vous pouvez ignorer ce message.';
$lang['email_reset_altbody'] = 'Bonjour,' . "\n\n" . 'Pour reinitialiser votre mot de passe cliquez sur le lien suivant :' . "\n" . '%1$s/%2$s' . "\n\n" . 'Vous devrez utiliser cette clé pour reinitialiser votre mot de passe depuis cette adresse :  %1$s' . "\n\n" . 'Si vous n\'avez pas demandé une réinitialisation du mot de passe, vous pouvez ignorer ce message.';

$lang['account_deleted'] = "Compte supprimé.";
$lang['function_disabled'] = "Cette fonction a &eacute;t&eacute; desactiv&eacute;.";