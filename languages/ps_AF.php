<?php
$lang = array();

$lang['user_blocked'] = "تاسو د سيستم لخوا بلاک سوي ياست.";
$lang['user_verify_failed'] = "امنيتي کود ناسم وو.";

$lang['email_password_invalid'] = "بريښناليک يا پټ توري ناسم وه.";
$lang['email_password_incorrect'] = "بريښناليک يا پټ توري ناسم وه.";
$lang['remember_me_invalid'] = "'ما په ياد ولره' خانه ناسمه وه.";

$lang['password_short'] = "پټ توري دير لږ دي.";
$lang['password_weak'] = "پټ توري ډير کمزوره دي.";
$lang['password_nomatch'] = "پټ توري برابر نه دي.";
$lang['password_changed'] = "پټ توري په برياليتوب سره نوي سول.";
$lang['password_incorrect'] = "اوسني پټ توري ناسم دي.";
$lang['password_notvalid'] = "پټ توري ناسم دي.";

$lang['newpassword_short'] = "نوي پټ توري ډير لږ دي.";
$lang['newpassword_long'] = "نوي پټ توري ډير زيات دي.";
$lang['newpassword_invalid'] = "نوي پټ توري بايډ لږ تر لږه يو لوی حرف او يو کوچنۍ حرف ولري او لږ تر لږه يو عدد هم بايد ولري.";
$lang['newpassword_nomatch'] = "نوي پټ توري برابر نه دي.";
$lang['newpassword_match'] = "نوي پټ توري د تير په څير دي.";

$lang['email_short'] = "بريښناليک ډير لنډ دي.";
$lang['email_long'] = "بريښناليک ډير اوږد دي.";
$lang['email_invalid'] = "بريښناليک ناسم دي.";
$lang['email_incorrect'] = "بريښناليک ناسم دي.";
$lang['email_banned'] = "دا بريښناليک اجازه نه لري.";
$lang['email_changed'] = "بريښناليک په برياليتوب سره نوی سو.";

$lang['newemail_match'] = "نوی بريښناليک د تير په څير دی.";

$lang['account_inactive'] = "ستاسو حساب تر اوسه نه دی فعال سوی.";
$lang['account_activated'] = "حساب فعال سو.";

$lang['logged_in'] = "تاسو اوس داخل سواست.";
$lang['logged_out'] = "تاسو اوس ووتلاست.";

$lang['system_error'] = "په سيستم کي ستونزه رامنځ ته سوه بيا هڅه وکړئ.";

$lang['register_success'] = "حساب پرانيستل سو او ستاسو بريښناليک ته يو پيغام در وليږل سو.";
$lang['register_success_emailmessage_suppressed'] = "حساب جوړ سو.";
$lang['email_taken'] = "دا بريښناليک داخل سوی دی.";

$lang['resetkey_invalid'] = "د نوي کولو کود ناسم دی.";
$lang['resetkey_incorrect'] = "د نوي کولو کود ناسم دی.";
$lang['resetkey_expired'] = "د نوي کولو کود زوړ دی.";
$lang['password_reset'] = "پټ توري په برياليتوب سره نوي سول.";

$lang['activationkey_invalid'] = "د فعالولو کود ناسم دی.";
$lang['activationkey_incorrect'] = "د فعالولو کود ناسم دی.";
$lang['activationkey_expired'] = "د فعالولو کود زوړ دی.";

$lang['reset_requested'] = "د پټو تورو د نوي کولو غوښتنه ستاسو بريښناليک ته در وليږل سوه.";
$lang['reset_requested_emailmessage_suppressed'] = "د پټو تورو د نوي کولو غوښتنه وسوه.";
$lang['reset_exists'] = "د نوي کولو غوښتنه سوې ده.";

$lang['already_activated'] = "حساب فعال سوی دی.";
$lang['activation_sent'] = "د فعالولو بريښناليک وليږل سو.";
$lang['activation_exists'] = "د فعالولو بريښناليک ليږل سوی دی.";

$lang['email_activation_subject'] = '%s - حساب فعال کړی.';
$lang['email_activation_body'] = 'سلام,<br/><br/> ددې لپاره چي تاسو خپل حساب ته داخل سئ نو لومړی بايد تاسو د لاندي پيوند په کليک کولو سره خپل حساب فعال کړئ: <strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/> تر هغه وروسته تاسو بايد د فعالولو له دغه کود څخه استفاده وکړئ: <strong>%3$s</strong><br/><br/> که تاسو په دې وروستيو کي په %1$s کي حساب نه وي پرانستی نو لطفاً دا بريښناليک له پامه وغورځوئ.';
$lang['email_activation_altbody'] = 'سلام, ' . "\n\n" . 'ددې لپاره چي تاسو خپل حساب ته داخل سئ نو لومړی بايد خپل حساب د لاندي پيوند په راخلاصولو سره فعال کړئ:' . "\n" . '%1$s/%2$s' . "\n\n" . 'تر هغه وروسته تاسو د فعالولو له دې کود څخه استفاده وکړئ: %3$s' . "\n\n" . 'که تاسو په دې وروستيو کي په %1$s کي حساب نه وي پرانستی نو لطفاً دا بريښناليک له پامه وغورځوئ.';

$lang['email_reset_subject'] = '%s - پټ توري نوي کول';
$lang['email_reset_body'] = 'سلام,<br/><br/>ددې لپاره چي تاسو خپل پټ توري نوي کړئ نو په لاندي پيوند باندي کليک وکړئ:<br/><br/><strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/>تر دې وروسته تاسو بايد د پټو تورو د نوي لپاره له دغه کود څخه استفاده وکړئ: <strong>%3$s</strong><br/><br/>که تاسو په دې وروستيو کي د %1$s څخه د پټو تورو د نوي کولو غوښتنه نه وي کړې نو لطفاً دا بريښناليک له پامه وغورځوئ.';
$lang['email_reset_altbody'] = 'سلام, ' . "\n\n" . 'ددې لپاره چي تاسو خپل پټ توري نوي کړئ نو په لاندي پيوند باندي کليک وکړئ:' . "\n" . '%1$s/%2$s' . "\n\n" . 'تر دې وروسته تاسو بايد د پټو تورو د نوي لپاره له دغه کود څخه استفاده وکړئ: %3$s' . "\n\n" . 'که تاسو په دې وروستيو کي د %1$s څخه د پټو تورو د نوي کولو غوښتنه نه وي کړې نو لطفاً دا بريښناليک له پامه وغورځوئ.';

$lang['account_deleted'] = "حساب په برياليتوب سره له منځه ولاړ.";
$lang['function_disabled'] = "دا کړنه اجازه نه لري.";
$lang['account_not_found'] = "لا وجود لحساب بهذا البريد الالكتروني.";

return $lang;
