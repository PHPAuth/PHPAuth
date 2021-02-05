<?php
$lang = array();

$lang['user_blocked'] = "您当前被锁定在系统之外。";
$lang['user_verify_failed'] = "验证码无效。";

$lang['email_password_invalid'] = "电子邮件地址/密码无效。";
$lang['email_password_incorrect'] = "电子邮件地址/密码不正确。";
$lang['remember_me_invalid'] = "“记住我”字段无效。";

$lang['password_short'] = "密码太短。";
$lang['password_weak'] = "密码太弱。";
$lang['password_nomatch'] = "密码不匹配。";
$lang['password_changed'] = "密码更换成功。";
$lang['password_incorrect'] = "当前密码不正确。";
$lang['password_notvalid'] = "密码无效。";

$lang['newpassword_short'] = "新密码太短。";
$lang['newpassword_long'] = "新密码太长。";
$lang['newpassword_invalid'] = "新密码必须包含至少一个大写和小写字符以及至少一位数字。";
$lang['newpassword_nomatch'] = "新密码不匹配。";
$lang['newpassword_match'] = "新密码与旧密码相同。";

$lang['email_short'] = "电子邮件地址太短。";
$lang['email_long'] = "电子邮件地址太长。";
$lang['email_invalid'] = "电子邮箱地址不可用。";
$lang['email_incorrect'] = "电子邮件地址不正确。";
$lang['email_banned'] = "不允许使用此电子邮件地址。";
$lang['email_changed'] = "电子邮件地址更改成功。";

$lang['newemail_match'] = "新电子邮件与先前电子邮件匹配。";

$lang['account_inactive'] = "帐户尚未激活。";
$lang['account_activated'] = "帐户已激活。";

$lang['logged_in'] = "您现在已经登录。";
$lang['logged_out'] = "您现在已经注销。";

$lang['system_error'] = "遇到系统错误。请重试。";

$lang['register_success'] = "已创建帐户。激活电子邮件已发送至电子邮件。";
$lang['register_success_emailmessage_suppressed'] = "已创建帐户。";
$lang['email_taken'] = "该电子邮件地址已被使用。";

$lang['resetkey_invalid'] = "重置密钥无效。";
$lang['resetkey_incorrect'] = "重置密钥不正确。";
$lang['resetkey_expired'] = "重置密钥已过期。";
$lang['password_reset'] = "密码重置成功。";
$lang['password_expired'] = "您的密碼已過期。";

$lang['activationkey_invalid'] = "激活密钥无效。";
$lang['activationkey_incorrect'] = "激活密钥不正确。";
$lang['activationkey_expired'] = "激活密钥已过期。";

$lang['reset_requested'] = "密码重设请求已发送至电子邮件地址。";
$lang['reset_requested_emailmessage_suppressed'] = "密码重置请求已创建。";
$lang['reset_exists'] = "一个重置请求已经存在。下一个重置密码请求将在 %s 可用"; //@todo: updated 2018-06-28

$lang['already_activated'] = "帐户已被激活。";
$lang['activation_sent'] = "激活电子邮件已发送。";
$lang['activation_exists'] = "已经发送了一封激活电子邮件。下一次激活将在 %s 进行"; //@todo: updated 2018-06-28

$lang['email_activation_subject'] = '%s - 激活帐户';
$lang['email_activation_body'] = '你好，<br/><br/>要登录您的帐户，您首先需要通过单击以下链接来激活您的帐户：<strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/>然后，您需要使用以下激活密钥：<strong>%3$s</strong><br/><br/>如果您最近没有注册 %1$s，则此消息发送错误，请忽略它。';
$lang['email_activation_altbody'] = '你好，' . "\n\n" . '要登录到您的帐户，您首先需要通过访问以下链接激活您的帐户：' . "\n" . '%1$s/%2$s' . "\n\n" . '然后您需要使用以下激活密钥： %3$s' . "\n\n" . '如果您最近没有注册 %1$s，则此消息发送有误，请忽略它。';

$lang['email_reset_subject'] = '%s - 密码重置请求';
$lang['email_reset_body'] = '你好，<br/><br/>要重置密码，请单击以下链接：<br/><br/><strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/>然后您需要使用以下密码重置密钥：<strong>%3$s</strong><br/><br/>如果您最近没有在 %1$s 上请求密码重置密钥，则此消息发送错误，请忽略它。';
$lang['email_reset_altbody'] = '你好，' . "\n\n" . '要重置密码，请访问以下链接：' . "\n" . '%1$s/%2$s' . "\n\n" . '然后您需要使用以下密码重置密钥：%3$s' . "\n\n" . '如果您最近没有在 %1$s 上请求密码重置密钥，则此消息发送错误，请忽略它。';

$lang['account_deleted'] = "帐户已成功删除。";
$lang['function_disabled'] = "此功能已被禁用。";
$lang['account_not_found'] = "找不到使用该电子邮件地址的帐户";

$lang['php_version_required'] = "PHPAuth引擎需要PHP版本 ％s+！";

return $lang;
