<?php
$lang = array();

$lang['user_blocked'] = "شما از طرف سیستم بلاک شدید";
$lang['user_verify_failed'] = "تصویر امنیتی وارد شده صحیح نمیباشد";

$lang['email_password_invalid'] = "آدرس ایمیل / پسورد نا معتبر است";
$lang['email_password_incorrect'] = "آدرس ایمیل / پسورد صحیح نمیباشد";
$lang['remember_me_invalid'] = "فیلد مرا به خاطر بسپار صحیح نمیباشد";

$lang['password_short'] = "رمز عبور بسیار کوتاه است.";
$lang['password_weak'] = "Password is too weak.";
$lang['password_nomatch'] = "رمز های عبور مثل هم نیستند";
$lang['password_changed'] = "رمز عبور با موفقیت تغییر کرد";
$lang['password_incorrect'] = "رمز عبور فعلی صحیح نمیباشد";
$lang['password_notvalid'] = "رمز عبور نامعتبر است";

$lang['newpassword_short'] = "رمز عبور جدید بسیار کوتاه است";
$lang['newpassword_long'] = "رمز عبور جدید بسیار بلند است";
$lang['newpassword_invalid'] = "رمز عبور جدید باید حداقل شامل یک حرف بزرگ ، یک حرف کوچک و حداقل یک عدد باشد";
$lang['newpassword_nomatch'] = "رمز های عبور جدید مثل هم نیستند";
$lang['newpassword_match'] = "رمز عبور جدید مشابه رمز عبور قدیمی است";

$lang['email_short'] = "آدرس ایمیل بسیار کوتاه است";
$lang['email_long'] = "آدرس ایمیل بسیار بلند است";
$lang['email_invalid'] = "آدرس ایمیل نامعتبر است";
$lang['email_incorrect'] = "آدرس ایمیل اشتباه است";
$lang['email_banned'] = "این آدرس ایمیل اجازه دسترسی ندارد";
$lang['email_changed'] = "آدرس ایمیل با موفقیت تغییر کرد";

$lang['newemail_match'] = "ایمیل جدید مشابه ایمیل قبلی است";

$lang['account_inactive'] = "این حساب کاربری هنوز فعال نشده است";
$lang['account_activated'] = "حساب کاربری فعال شد";

$lang['logged_in'] = "شما وارد شدید";
$lang['logged_out'] = "شما خارج شدید";

$lang['system_error'] = "یک خطای سیستمی رخ داده است. لطفا مجدد تلاش کنید";

$lang['register_success'] = "حساب کاربری ایجاد شد. ایمیل فعال سازی برای شما ارسال شد";
$lang['register_success_emailmessage_suppressed'] = "حساب کاربری ایجاد شد";
$lang['email_taken'] = "این آدرس ایمیل قبلا استفاده شده است";

$lang['resetkey_invalid'] = "کلید تنظیم مجدد نامعتبر است";
$lang['resetkey_incorrect'] = "کلید تنظیم مجدد صحیح نیست";
$lang['resetkey_expired'] = "کلید تنظیم مجدد منقضی شده است";
$lang['password_reset'] = "رمز عبور با موفقیت بازنشانی شد";

$lang['activationkey_invalid'] = "کد فعال سازی نا معتبر است";
$lang['activationkey_incorrect'] = "کد فعال سازی صحیح نیست";
$lang['activationkey_expired'] = "این کد فعال سازی منقضی شده است";

$lang['reset_requested'] = "درخواست تغییر رمز عبور به ایمیل شما ارسال شد";
$lang['reset_requested_emailmessage_suppressed'] = "درخواست تغییر رمز عبور ایجاد شد";
$lang['reset_exists'] = "درخوسات بازنشانی قبلا ایجاد شده است";

$lang['already_activated'] = " این حساب کاربری قبلا فعال شده است";
$lang['activation_sent'] = "ایمیل فعال سازی ارسال شد";
$lang['activation_exists'] = "یک ایمیل فعال سازی قبلا ارسال شده است";

$lang['email_activation_subject'] = ' حساب کاربری فعال - %s';
$lang['email_activation_body'] = ' <p dir="rtl"> سلام,<br/><br/> برای ورود به سایت ابتدا باید با کلیک بر روی لینک زیر حساب کاربری خود را فعال کنید : <strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/>سپس به کلید فعال سازی زیر نیاز خواهید داشت :  <strong>%3$s</strong><br/><br/>  اگر شما در %1$s ثبت نام نکرده اید پس این ایمیل اشتباها برای شما ارسال شده است ، لطفا این ایمیل را نادیده بگیرید.</p>';
$lang['email_activation_altbody'] = 'سلام,' . "\n\n" . ' برای ورود به سایت ابتدا باید با کلیک بر روی لینک زیر حساب کاربری خود را فعال کنید : ' . "\n" . '%1$s/%2$s' . "\n\n" . 'سپس به کلید فعال سازی زیر نیاز خواهید داشت : %3$s' . "\n\n" . '  اگر شما در %1$s ثبت نام نکرده اید پس این ایمیل اشتباها برای شما ارسال شده است ، لطفا این ایمیل را نادیده بگیرید.';

$lang['email_reset_subject'] = 'درخوسات تغییر رمز عبور - %s';
$lang['email_reset_body'] = ' <p dir="rtl"> سلام,<br/><br/>برای تغییر رمز عبور خود روی لینک زیر کلیک کنید : <br/><br/><strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/>شما همچنین به کد فعال سازی زیر نیاز خواهید داشت : <strong>%3$s</strong><br/><br/>اگر شما درخواست تغییر رمز عبور در %1$s نداشتید پس این ایمیل به اشتباه برای شما ارسال شده است ، لطفا آنرا نادیده بگیرید.</p>';
$lang['email_reset_altbody'] = 'سلام,' . "\n\n" . 'برای تغییر رمز عبور خود روی لینک زیر کلیک کنید : ' . "\n" . '%1$s/%2$s' . "\n\n" . 'شما همچنین به کد فعال سازی زیر نیاز خواهید داشت : ' . "\n\n" . 'اگر شما درخواست تغییر رمز عبور در %1$s نداشتید پس این ایمیل به اشتباه برای شما ارسال شده است ، لطفا آنرا نادیده بگیرید.';

$lang['account_deleted'] = "حساب کاربری شما با موفقیت حذف شد";
$lang['function_disabled'] = "این تابع غیرفعال شده است.";
$lang['account_not_found'] = "لا وجود لحساب بهذا البريد الالكتروني.";

return $lang;
