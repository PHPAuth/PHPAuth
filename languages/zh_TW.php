<?php
$lang = array();

$lang['user_blocked'] = "您當前被鎖定在系統之外。";
$lang['user_verify_failed'] = "驗證碼無效。";

$lang['email_password_invalid'] = "電子郵件地址/密碼無效。";
$lang['email_password_incorrect'] = "電子郵件地址/密碼不正確。";
$lang['remember_me_invalid'] = "“記住我”字段無效。";

$lang['password_short'] = "密碼太短。";
$lang['password_weak'] = "密碼太弱。";
$lang['password_nomatch'] = "密碼不匹配。";
$lang['password_changed'] = "密碼更換成功。";
$lang['password_incorrect'] = "當前密碼不正確。";
$lang['password_notvalid'] = "密碼無效。";

$lang['newpassword_short'] = "新密碼太短。";
$lang['newpassword_long'] = "新密碼太長。";
$lang['newpassword_invalid'] = "新密碼必須包含至少一個大寫和小寫字符以及至少一位數字。";
$lang['newpassword_nomatch'] = "新密碼不匹配。";
$lang['newpassword_match'] = "新密碼與舊密碼相同。";

$lang['email_short'] = "電子郵件地址太短。";
$lang['email_long'] = "電子郵件地址太長。";
$lang['email_invalid'] = "電子郵箱地址不可用。";
$lang['email_incorrect'] = "電子郵件地址不正確。";
$lang['email_banned'] = "不允許使用此電子郵件地址。";
$lang['email_changed'] = "電子郵件地址更改成功。";

$lang['newemail_match'] = "新電子郵件與先前電子郵件匹配。";

$lang['account_inactive'] = "帳戶尚未激活。";
$lang['account_activated'] = "帳戶已激活。";

$lang['logged_in'] = "您現在已經登錄。";
$lang['logged_out'] = "您現在已經註銷。";

$lang['system_error'] = "遇到系統錯誤。請重試。";

$lang['register_success'] = "已創建帳戶。激活電子郵件已發送至電子郵件。";
$lang['register_success_emailmessage_suppressed'] = "已創建帳戶。";
$lang['email_taken'] = "該電子郵件地址已被使用。";

$lang['resetkey_invalid'] = "重置密鑰無效。";
$lang['resetkey_incorrect'] = "重置密鑰不正確。";
$lang['resetkey_expired'] = "重置密鑰已過期。";
$lang['password_reset'] = "密碼重置成功。";
$lang['password_expired'] = "您的密码已过期。";

$lang['activationkey_invalid'] = "激活密鑰無效。";
$lang['activationkey_incorrect'] = "激活密鑰不正確。";
$lang['activationkey_expired'] = "激活密鑰已過期。";

$lang['reset_requested'] = "密碼重設請求已發送至電子郵件地址。";
$lang['reset_requested_emailmessage_suppressed'] = "密碼重置請求已創建。";
$lang['reset_exists'] = "一個重置請求已經存在。下一個重置密碼請求將在 %s 可用"; //@todo: updated 2018-06-28

$lang['already_activated'] = "帳戶已被激活。";
$lang['activation_sent'] = "激活電子郵件已發送。";
$lang['activation_exists'] = "已經發送了一封激活電子郵件。下一次激活將在 %s 進行"; //@todo: updated 2018-06-28

$lang['email_activation_subject'] = '%s - 激活帳戶';
$lang['email_activation_body'] = '你好，<br/><br/>要登錄您的帳戶，您首先需要通過單擊以下鏈接來激活您的帳戶：<strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/>然後，您需要使用以下激活密鑰：<strong>%3$s</strong><br/><br/>如果您最近沒有註冊 %1$s，則此消息發送錯誤，請忽略它。';
$lang['email_activation_altbody'] = '你好，' . "\n\n" . '要登錄到您的帳戶，您首先需要通過訪問以下鏈接激活您的帳戶：' . "\n" . '%1$s/%2$s' . "\n\n" . '然後您需要使用以下激活密鑰：%3$s' . "\n\n" . '如果您最近沒有註冊 %1$s，則此消息發送有誤，請忽略它。';

$lang['email_reset_subject'] = '%s - 密碼重置請求';
$lang['email_reset_body'] = '你好，<br/><br/>要重置密碼，請單擊以下鏈接：<br/><br/><strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/>然後您需要使用以下密碼重置密鑰：<strong>%3$s</strong><br/><br/>如果您最近没有在 %1$s 上请求密码重置密钥，则此消息发送错误，请忽略它。';
$lang['email_reset_altbody'] = '你好，' . "\n\n" . '要重置密碼，請訪問以下鏈接：' . "\n" . '%1$s/%2$s' . "\n\n" . '然後您需要使用以下密碼重置密鑰：%3$s' . "\n\n" . '如果您最近沒有在 %1$s 上請求密碼重置密鑰，則此消息發送錯誤，請忽略它。';

$lang['account_deleted'] = "帳戶已成功刪除。";
$lang['function_disabled'] = "此功能已被禁用。";
$lang['account_not_found'] = "找不到使用該電子郵件地址的帳戶";

$lang['php_version_required'] = "PHPAuth引擎需要PHP版本 ％s+！";

return $lang;
