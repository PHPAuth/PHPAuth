<?php
$lang = array();

$lang['user_blocked'] = "現在システムからロックアウトされています。";
$lang['user_verify_failed'] = "検証コードが無効です。";

$lang['email_password_invalid'] = "メールアドレス/パスワードが無効です。";
$lang['email_password_incorrect'] = "メールアドレス/パスワードが正しくありません。";
$lang['remember_me_invalid'] = "「remember me」フィールドは無効です。。";

$lang['password_short'] = "パスワードが短すぎます。";
$lang['password_weak'] = "パスワードが弱すぎます。";
$lang['password_nomatch'] = "パスワードが一致しません。";
$lang['password_changed'] = "パスワードは正常に変更されました。";
$lang['password_incorrect'] = "現在のパスワードが正しくありません。";
$lang['password_notvalid'] = "パスワードが無効です。";

$lang['newpassword_short'] = "新しいパスワードが短すぎます。";
$lang['newpassword_long'] = "新しいパスワードが長すぎます。";
$lang['newpassword_invalid'] = "新しいパスワードには、少なくとも1つの大文字と小文字、および少なくとも1つの数字が含まれている必要があります。";
$lang['newpassword_nomatch'] = "新しいパスワードと古いパスワードが一致しません。";
$lang['newpassword_match'] = "新しいパスワードは古いパスワードと同じです。";

$lang ['email_short'] = "メールアドレスが短すぎます。";
$lang ['email_long'] = "メールアドレスが長すぎます。";
$lang ['email_invalid'] = "メールアドレスは利用できません。";
$lang ['email_incorrect'] = "メールアドレスが正しくありません。";
$lang ['email_banned'] = "このメールアドレスは許可されていません。";
$lang ['email_changed'] = "メールアドレスは正常に変更されました。";
$lang ['newemail_match'] = "新しいメールは前のメールと一致します。";


$lang ['account_inactive'] = "アカウントはまだアクティブ化されていません。";
$lang ['account_activate'] = "アカウントがアクティブ化されました。";

$lang ['logged_in'] = "ログインしました。";
$lang ['logged_out'] = "現在ログアウトしています。";

$lang ['system_error'] = "システムエラーが発生しました。再試行してください。";

$lang ['register_success'] = "アカウントが作成されました。アクティベーションメールがメールに送信されました。";
$lang ['register_success_emailmessage_suppressed'] = "アカウントが作成されました。";
$lang ['email_taken'] = "メールアドレスはすでに取得されています。";

$lang ['resetkey_invalid'] = "リセットキーが無効です。";
$lang ['resetkey_incorrect'] = "リセットキーが正しくありません。";
$lang ['resetkey_expired'] = "リセットキーの有効期限が切れました。";
$lang ['password_reset'] = "パスワードは正常にリセットされました。";
$lang ['password_expired'] = "パスワードの有効期限が切れています。";

$lang ['activationkey_invalid'] = "アクティベーションキーが無効です。";
$lang ['activationkey_incorrect'] = "アクティベーションキーが正しくありません。";
$lang ['activationkey_expired'] = "アクティベーションキーの有効期限が切れています。";

$lang ['reset_requested'] = "パスワードリセットリクエストがメールアドレスに送信されました。";
$lang ['reset_requested_emailmessage_suppressed'] = "パスワードリセット要求が作成されました。";
$lang ['reset_exists'] = "リセット要求は既に存在します。次のリセットパスワード要求は %s で利用可能になります"; 

$lang['already_activated'] = "帐户已被激活。";
$lang['activation_sent'] = "激活电子邮件已发送。";
$lang['activation_exists'] = "已经发送了一封激活电子邮件。下一次激活将在 %s 进行"; //@todo: updated 2018-06-28

$lang ['already_activate'] = "アカウントがアクティブ化されました。";
$lang ['activation_sent'] = "アクティベーションメールが送信されました。";
$lang ['activation_exists'] = "アクティベーションメールが送信されました。次のアクティベーションは %s で行われます"; 


$lang ['email_activation_subject'] = '%s - アカウントをアクティブ化';
$lang ['email_activation_body'] = 'こんにちは、<br/><br/>アカウントにログインするには、まず次のリンクをクリックしてアカウントをアクティブ化する必要があります：<strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/>最近 %1$s に登録していない場合、このメッセージは誤って送信されました。無視してください。 ';
$lang ['email_activation_altbody'] = 'こんにちは' . "\n\n" . 'アカウントにログインするには、まず次のリンクにアクセスしてアカウントをアクティブ化する必要があります：' . "\n" . '%1$s/%2$s' . "\n\n" . '次に、次のアクティベーションキーを使用する必要があります：%3$s' . "\n\n" .  '最近%1$sを登録していない場合、このメッセージは正しく送信されませんでした。無視してください。 ';

$lang ['email_reset_subject'] = '%s - パスワードリセット要求';
$lang ['email_reset_body'] = 'こんにちは、<br/><br/>パスワードをリセットするには、次のリンクをクリックしてください：<br/><br/><strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/>最近%1$sでパスワードリセットキーをリクエストしていない場合、このメッセージは正しく送信されませんでした。無視してください。 ';
$lang ['email_reset_altbody'] = 'こんにちは、' . "\n\n" . 'パスワードをリセットするには、次のリンクにアクセスしてください：' . "\n" . '%1$s/%2$s' . "\n\n" . '次に、次のパスワードリセットキーを使用する必要があります：%3$s' . "\n\n" . '最近 %1$s でパスワードリセットキーを要求していない場合は、このメッセージは誤って送信されました。無視してください。 ';

$lang ['account_deleted'] = "アカウントは正常に削除されました。";
$lang ['function_disabled'] = "この機能は無効になっています。";
$lang ['account_not_found'] = "このメールアドレスのアカウントが見つかりませんでした";

return $lang;
