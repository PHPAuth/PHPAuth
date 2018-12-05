<?php
$lang = array();

$lang['user_blocked'] = "No has iniciado sesión en el sistema.";
$lang['user_verify_failed'] = "El código Captcha fue inválido.";

$lang['email_password_invalid'] = "El correo electrónico / La contraseña son inválidos.";
$lang['email_password_incorrect'] = "El correo electrónico / La contraseña son incorrectos.";
$lang['remember_me_invalid'] = "El campo de Recordame es inválido.";

$lang['password_short'] = "La contraseña es muy corta.";
$lang['password_weak'] = "La contraseña es muy débil.";
$lang['password_nomatch'] = "Las contraseñas no coinciden.";
$lang['password_changed'] = "La contraseña ha sido cambiada con éxito.";
$lang['password_incorrect'] = "La contraseña actual es incorrecta.";
$lang['password_notvalid'] = "La contraseña es inválida.";

$lang['newpassword_short'] = "La contraseña nueva es muy corta.";
$lang['newpassword_long'] = "La nueva contraseña es muy larga.";
$lang['newpassword_invalid'] = "La nueva contraseña debe contener al menos una letra mayúscula y una letra minúscula, y al menos un dígito.";
$lang['newpassword_nomatch'] = "Las contraseñas nuevas no coinciden.";
$lang['newpassword_match'] = "La nueva contraseña es la misma que la contraseña anterior.";

$lang['email_short'] = "El correo electrónico es muy corto.";
$lang['email_long'] = "El correo electrónico es muy largo.";
$lang['email_invalid'] = "El correo electrónico es inválido.";
$lang['email_incorrect'] = "El correo electrónico es incorrecto.";
$lang['email_banned'] = "El correo electrónico no está permitido.";
$lang['email_changed'] = "El correo electrónico ha sido cambiado con éxito.";

$lang['newemail_match'] = "El nuevo correo electrónico coincide con el anterior.";

$lang['account_inactive'] = "La cuenta no ha sido activada.";
$lang['account_activated'] = "Cuenta activada.";

$lang['logged_in'] = "Has iniciado sesión.";
$lang['logged_out'] = "Tu sesión ha terminado.";

$lang['system_error'] = "Ha ocurrido un error en el sistema. Por favor trate de nuevo.";

$lang['register_success'] = "La cuenta se ha creado y el correo electrónico de activación ha sido enviado.";
$lang['register_success_emailmessage_suppressed'] = "La cuenta se ha creado.";
$lang['email_taken'] = "El correo electrónico está actualmente en uso.";

$lang['resetkey_invalid'] = "La clave de reinicio es inválida.";
$lang['resetkey_incorrect'] = "La clave de reinicio es incorrecta.";
$lang['resetkey_expired'] = "La clave de reinicio ha expirado.";
$lang['password_reset'] = "La contraseña ha sido reiniciada con éxito.";

$lang['activationkey_invalid'] = "La clave de activación es inválida.";
$lang['activationkey_incorrect'] = "La clave de activación es incorrecta.";
$lang['activationkey_expired'] = "La clave de activación ha expirado.";

$lang['reset_requested'] = "La petición para reiniciar la contraseña ha sido enviado al correo electrónico.";
$lang['reset_requested_emailmessage_suppressed'] = "La petición para reiniciar la contraseña ha sido creada.";
$lang['reset_exists'] = "Ya existe una petición para reiniciar la contraseña.";

$lang['already_activated'] = "La cuenta ya ha sido activada.";
$lang['activation_sent'] = "El correo electrónico de activación ha sido enviado.";
$lang['activation_exists'] = "El correo electrónico de activación ya ha sido enviado.";

$lang['email_activation_subject'] = '%s - Activar cuenta';
$lang['email_activation_body'] = 'Hola,<br/><br/>Para inciar sesión en tu cuenta de usuario primero tienes que activar tu cuenta dando click en el siguiente enlace: <strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/> Después, necesitas usar la siguiente clave de activación: <strong>%3$s</strong><br/><br/> Si no te registraste en %1$s recientemente entonces este mensaje fue enviado por error, por favor ignóralo.';
$lang['email_activation_altbody'] = 'Hola, ' . "\n\n" . 'Para inciar sesión en tu cuenta de usuario primero tienes que activar tu cuenta dando click en el siguiente enlace :' . "\n" . '%1$s/%2$s' . "\n\n" . 'Después, necesitas usar la siguiente clave de activación: %3$s' . "\n\n" . 'Si no te registraste en %1$s recientemente entonces este mensaje fue enviado por error, por favor ignóralo';

$lang['email_reset_subject'] = '%s - Petición para reiniciar contraseña';
$lang['email_reset_body'] = 'Hola,<br/><br/>Para reiniciar tu contraseña por favor da click en el siguiente enlace:<br/><br/><strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/>Después, necesitas usar la siguiente clave de reinicio: <strong>%3$s</strong><br/><br/>Si no solicitaste reiniciar tu contraseña en %1$s por favor ignora este mensaje.';
$lang['email_reset_altbody'] = 'Hola, ' . "\n\n" . 'Para reiniciar tu contraseña por favor da click en el siguiente enlace:' . "\n" . '%1$s/%2$s' . "\n\n" . 'Después, necesitas usar la siguiente clave de reinicio: %3$s' . "\n\n" . 'Si no solicitaste reiniciar tu contraseña en %1$s por favor ignora este mensaje.';

$lang['account_deleted'] = "La cuenta se ha borrado con éxito.";
$lang['function_disabled'] = "Esta función ha sido desactivada.";
$lang['account_not_found'] = "No se ha encontrado ninguna cuenta con esa dirección de correo electrónico.";

return $lang;
