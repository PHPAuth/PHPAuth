<?php
$lang = array();

$lang['user_blocked'] = "Você está bloqueado.";
$lang['user_verify_failed'] = "Codigo de Captcha incorreto.";

$lang['email_password_invalid'] = "Endereço de email ou senha são inválidos.";
$lang['email_password_incorrect'] = "Endereço de email ou senha estão incorretas.";
$lang['remember_me_invalid'] = "O campo lembrar-me é invalido.";

$lang['password_short'] = "Senha muito curta.";
$lang['password_weak'] = "Senha muito fraca.";
$lang['password_nomatch'] = "As senhas não combinam.";
$lang['password_changed'] = "Senha alterada com sucesso.";
$lang['password_incorrect'] = "A senha está incorreta.";
$lang['password_notvalid'] = "Senha inválida.";

$lang['newpassword_short'] = "A nova senha é curta.";
$lang['newpassword_long'] = "A nova senha é longa.";
$lang['newpassword_invalid'] = "A nova senha deve conter pelo menos uma letra maiúscula, minúscula e um número.";
$lang['newpassword_nomatch'] = "Suas novas senhas não combinam.";
$lang['newpassword_match'] = "Sua nova senha é igual a anterior.";

$lang['email_short'] = "Endereço de email é muito curto.";
$lang['email_long'] = "Endereço de email é muito longo.";
$lang['email_invalid'] = "Este endereço de email não existe.";
$lang['email_incorrect'] = "Endereço de email incorreto.";
$lang['email_banned'] = "Este endereço de email não é permitido.";
$lang['email_changed'] = "Endereço de email alterado com sucesso.";

$lang['newemail_match'] = "O novo endereço de email é igual ao anterior.";

$lang['account_inactive'] = "Está conta ainda não está ativada.";
$lang['account_activated'] = "Conta ativada com sucesso.";

$lang['logged_in'] = "Você está logado.";
$lang['logged_out'] = "Você está deslogado.";

$lang['system_error'] = "Erro. Tente mais tarde.";

$lang['register_success'] = "Conta criada. O email para ativação foi enviado.";
$lang['register_success_emailmessage_suppressed'] = "Conta criada.";
$lang['email_taken'] = "Este endereço de email já está em uso.";

$lang['resetkey_invalid'] = "A chave de redefinição é inválida.";
$lang['resetkey_incorrect'] = "A chave de redefinição está incorreta.";
$lang['resetkey_expired'] = "Chave de redefinição expirada.";
$lang['password_reset'] = "Senha redefinida com sucesso.";

$lang['activationkey_invalid'] = "A chave de ativação é inválida.";
$lang['activationkey_incorrect'] = "A chave de ativação está incorreta.";
$lang['activationkey_expired'] = "A chave de ativação está expirada.";

$lang['reset_requested'] = "A requisição de redefinição da senha foi enviada.";
$lang['reset_requested_emailmessage_suppressed'] = "Requisição de redefinição da senha foi criada.";
$lang['reset_exists'] = "Essa requisição já existe.";

$lang['already_activated'] = "Essa conta já está ativada.";
$lang['activation_sent'] = "Email de ativação enviado com sucesso.";
$lang['activation_exists'] = "Um email de ativação já foi enviado.";

$lang['email_activation_subject'] = '%s - Ativação da conta';
$lang['email_activation_body'] = 'Olá,<br/><br/> Para logar-se em sua conta é necessaria ativa-la primeiro no seguinte link: <strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/> Em seguida você deve utilizar a seguinte chave de ativação: <strong>%3$s</strong><br/><br/> Se você não se cadastrou em %1$s recentemente, por favor ignore este email.';
$lang['email_activation_altbody'] = 'Olá, ' . "\n\n" . 'Para logar-se em sua conta é necessaria ativa-la primeiro no seguinte link:' . "\n" . '%1$s/%2$s' . "\n\n" . 'Em seguida você deve ultilizar a seguinte chave de ativação: %3$s' . "\n\n" . 'Se você não se cadastrou em %1$s recentemente, por favor ignore este email.';

$lang['email_reset_subject'] = '%s - Requisição de redefinição da senha';
$lang['email_reset_body'] = 'Olá,<br/><br/>Para você redefinir sua senha você primeiro precisa entrar no seguinte link:<br/><br/><strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/>Logo depois você precisa utilizar a seguinte chave de redefinição: <strong>%3$s</strong><br/><br/> Caso você não solicitou a redefinição da senha em %1$s recentemente, por favor ignore este email.';
$lang['email_reset_altbody'] = 'Olá, ' . "\n\n" . 'Para você redefinir sua senha você primeiro precisa entrar no seguinte link:' . "\n" . '%1$s/%2$s' . "\n\n" . 'Logo depois você precisa utilizar a seguinte chave de redefinição: %3$s' . "\n\n" . 'Caso você não solicitou a redefinição da senha em %1$s recentemente, por favor ignore este email.';

$lang['account_deleted'] = "Conta deletada com sucesso.";
$lang['function_disabled'] = "Esta função foi desabilitada.";
$lang['account_not_found'] = "Nenhuma conta encontrada com esse endereço de e-mail.";

return $lang;
