<?php
$lang = array();

$lang['user_blocked'] = "Bạn đã bị khóa khỏi hệ thống.";
$lang['user_verify_failed'] = "Mã Captcha không hợp lệ.";

$lang['email_password_invalid'] = "Email / mật khẩu không hợp lệ.";
$lang['email_password_incorrect'] = "Email / mật khẩu không chính xác.";
$lang['remember_me_invalid'] = "Trường remember không hợp lệ.";

$lang['password_short'] = "Mật khẩu quá ngắn.";
$lang['password_weak'] = "Mật khẩu yếu.";
$lang['password_nomatch'] = "Mật khẩu không khớp.";
$lang['password_changed'] = "Thay đổi mật khẩu thành công.";
$lang['password_incorrect'] = "Mật khẩu hiện tại không đúng.";
$lang['password_notvalid'] = "Mật khẩu không hợp lệ.";

$lang['newpassword_short'] = "Mật khẩu mới quá ngắn.";
$lang['newpassword_long'] = "Mật khẩu mới quá dài.";
$lang['newpassword_invalid'] = "Mật khẩu mới phải chứa ít nhất một ký tự viết hoa và một ký tự viết thường, và một chữ số.";
$lang['newpassword_nomatch'] = "Mật khẩu không khớp.";
$lang['newpassword_match'] = "Mật khẩu mới giống với mật khẩu cũ.";

$lang['email_short'] = "Email quá ngắn.";
$lang['email_long'] = "Email quá dài.";
$lang['email_invalid'] = "Email không hợp lệ.";
$lang['email_incorrect'] = "Email không chính xác.";
$lang['email_banned'] = "Email này không được chấp nhận.";
$lang['email_changed'] = "Thay đổi email thành công.";

$lang['newemail_match'] = "Email mới giống với email cũ.";

$lang['account_inactive'] = "Tài khoản chưa được kích hoạt.";
$lang['account_activated'] = "Tài khoản đã được kích hoạt.";

$lang['logged_in'] = "Bạn đã đăng nhập.";
$lang['logged_out'] = "Bạn đã đăng xuất.";

$lang['system_error'] = "Lỗi hệ thống! Vui lòng thử lại lần nữa.";

$lang['register_success'] = "Tài khoản đã được tạo. Email kích hoạt đã được gửi.";
$lang['register_success_emailmessage_suppressed'] = "Tài khoản đã được tạo.";
$lang['email_taken'] = "Email này đã được sử dụng.";

$lang['resetkey_invalid'] = "Mã reset không hợp lệ.";
$lang['resetkey_incorrect'] = "Mã reset không chính xác.";
$lang['resetkey_expired'] = "Mã reset đã hết hạn.";
$lang['password_reset'] = "Thay đổi mật khẩu thành công.";

$lang['activationkey_invalid'] = "Mã kích hoạt không hợp lệ.";
$lang['activationkey_incorrect'] = "Mã kích hoạt không chính xác.";
$lang['activationkey_expired'] = "Mã kích hoạt đã hết hạn.";

$lang['reset_requested'] = "Yêu cầu reset mật khẩu đã được gửi tới email.";
$lang['reset_requested_emailmessage_suppressed'] = "Yêu cầu reset mật khẩu đã được tạo.";
$lang['reset_exists'] = "Yêu cầu reset mật khẩu đã tồn tại.";

$lang['already_activated'] = "Tài khoản đã được kích hoạt rồi.";
$lang['activation_sent'] = "Email kích hoạt đã được gửi.";
$lang['activation_exists'] = "Email kích hoạt đã được gửi rồi.";

$lang['email_activation_subject'] = '%s - Kích hoạt tài khoản';
$lang['email_activation_body'] = 'Xin chào,<br/><br/> Để đăng nhập bạn cần phải kích hoạt tài khoản bằng cách click vào đường dẫn sau: <strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/> Sau đó dùng mã kích hoạt sau: <strong>%3$s</strong><br/><br/> Nếu bạn chưa từng đăng ký tài khoản tại %1$s vui lòng bỏ qua email này.';
$lang['email_activation_altbody'] = 'Xin chào, ' . "\n\n" . 'Để đăng nhập bạn cần phải kích hoạt tài khoản bằng cách click vào đường dẫn sau:' . "\n" . '%1$s/%2$s' . "\n\n" . 'Sau đó dùng mã kích hoạt sau: %3$s' . "\n\n" . 'Nếu bạn chưa từng đăng ký tài khoản tại %1$s vui lòng bỏ qua email này.';

$lang['email_reset_subject'] = '%s - Yêu cầu reset mật khẩu';
$lang['email_reset_body'] = 'Xin chào,<br/><br/>Click vào đường dẫn sau để reset mật khẩu:<br/><br/><strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/>Sau đó dùng mã reset password sau: <strong>%3$s</strong><br/><br/>Nếu bạn không gửi yêu cầu reset mật khẩu tại %1$s vui lòng bỏ qua email này.';
$lang['email_reset_altbody'] = 'Xin chào, ' . "\n\n" . 'Đi đến đường dẫn sau để reset mật khẩu:' . "\n" . '%1$s/%2$s' . "\n\n" . 'Sau đó dùng mã reset password sau: %3$s' . "\n\n" . 'Nếu bạn không gửi yêu cầu reset mật khẩu tại %1$s vui lòng bỏ qua email này.';

$lang['account_deleted'] = "Tài khoản đã được xóa thành công.";
$lang['function_disabled'] = "Chức năng này đã bị vô hiệu hóa.";
$lang['account_not_found'] = "Không tìm thấy tài khoản nào có địa chỉ email đó.";

return $lang;
