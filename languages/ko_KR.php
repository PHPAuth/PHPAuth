<?php
$lang = array();

$lang['user_blocked'] = "너무 잦은 접근으로 인해 차단되었습니다.";
$lang['user_verify_failed'] = "Captcha 코드가 유효하지 않습니다.";

$lang['email_password_invalid'] = "유효한 아이디/비밀번호가 아닙니다.";
$lang['email_password_incorrect'] = "아이디/비밀번호가 정확하지 않습니다.";
$lang['remember_me_invalid'] = "remember me 값이 0이나 1이 아닙니다..";

$lang['password_short'] = "비밀번호가 너무 짧습니다.";
$lang['password_weak'] = "비밀번호가 너무 쉽습니다.";
$lang['password_nomatch'] = "비밀번호가 일치하지 않습니다.";
$lang['password_changed'] = "비밀번호가 성공적으로 변경되었습니다.";
$lang['password_incorrect'] = "현재 비밀번호가 정확하지 않습니다.";
$lang['password_notvalid'] = "유효한 비밀번호가 아닙니다.";

$lang['newpassword_short'] = "새로운 비밀번호가 너무 짧습니다.";
$lang['newpassword_long'] = "새로운 비밀번호가 너무 깁니다.";
$lang['newpassword_invalid'] = "새로운 비밀번호는 하나 이상의 대문자와 소문자, 숫자로 이루어져 있어야 합니다.";
$lang['newpassword_nomatch'] = "새로운 비밀번호가 일치하지 않습니다.";
$lang['newpassword_match'] = "새로운 비밀번호가 이전 비밀번호와 일치합니다.";

$lang['email_short'] = "이메일 주소가 너무 짧습니다.";
$lang['email_long'] = "이메일 주소가 너무 깁니다.";
$lang['email_invalid'] = "유효한 이메일 주소가 아닙니다.";
$lang['email_incorrect'] = "이메일 주소가 정확하지 않습니다.";
$lang['email_banned'] = "허용되지 않은 이메일 주소입니다.";
$lang['email_changed'] = "이메일 주소가 성공적으로 변경되었습니다.";

$lang['id_short'] = "아이디가 너무 짧습니다.";
$lang['id_long'] = "아이디가 너무 깁니다.";
$lang['id_invalid'] = "유효한 아이디가 아닙니다.";
$lang['id_incorrect'] = "아이디가 정확하지 않습니다.";
$lang['id_banned'] = "허용되지 않은 아아디입니다.";
$lang['id_changed'] = "아이디가 성공적으로 변경되었습니다.";

$lang['newemail_match'] = "새로운 이메일 주소가 이전 이메일 주소와 일치합니다.";

$lang['account_inactive'] = "활성화된 계정이 아닙니다.";
$lang['account_activated'] = "계정이 활성화되었습니다.";

$lang['logged_in'] = "로그인 되었습니다.";
$lang['logged_out'] = "로그아웃 되었습니다.";

$lang['system_error'] = "시스템 에러가 발생했습니다. 다시 시도해주세요.";

$lang['register_success'] = "계정이 생성되었습니다. 확인 이메일이 전송되었습니다.";
$lang['register_success_emailmessage_suppressed'] = "계정이 생성되었습니다.";
$lang['email_taken'] = "이미 사용된 이메일 주소입니다.";
$lang['id_taken'] = "이미 사용된 아이디입니다.";

$lang['resetkey_invalid'] = "유효하지 않은 재설정 키입니다.";
$lang['resetkey_incorrect'] = "재설정 키가 정확하지 않습니다.";
$lang['resetkey_expired'] = "재설정 키가 만료되었습니다.";
$lang['password_reset'] = "비밀번호가 성공적으로 재설정되었습니다.";

$lang['activationkey_invalid'] = "유효하지 않은 활성화 키입니다.";
$lang['activationkey_incorrect'] = "활성화 키가 정확하지 않습니다.";
$lang['activationkey_expired'] = "활성화 키가 만료되었습니다.";

$lang['reset_requested'] = "비밀번호 재설정 요청을 이메일로 전송하였습니다.";
$lang['reset_requested_emailmessage_suppressed'] = "비밀번호 재설정 요청이 생성되었습니다.";
$lang['reset_exists'] = "재설정 요청이 이미 존재합니다.";

$lang['already_activated'] = "계정이 이미 활성화되었습니다.";
$lang['activation_sent'] = "확인 이메일이 전송되었습니다.";
$lang['activation_exists'] = "확인 이메일이 이미 전송되었습니다.";

$lang['email_activation_subject'] = '%s - 계정 활성화';
$lang['email_activation_body'] = '안녕하십니까.<br/><br/> 계정에 로그인하기 위해서는 먼저 다음 링크를 클릭하여 계정을 활성화해야 합니다. <strong><a href="%1$s/%2$s.php">%1$s/%2$s.php</a></strong><br/><br/> 그리고 다음 활성화 키를 사용해야 합니다.<br/> <strong>%3$s</strong><br/><br/> 만약 최근에 GSA ONLINE JUDGE에 가입하지 않으셨다면 이 메세지는 잘못 전송되었습니다. 무시하십시오.';
$lang['email_activation_altbody'] = '안녕하십니까. ' . "\n\n" . '계정에 로그인하기 위해서는 먼저 다음 링크를 클릭하여 계정을 활성화해야 합니다.'. "\n" . '%1$s/%2$s.php' . "\n\n" . '그리고 다음 활성화 키를 사용해야 합니다. %3$s' . "\n\n" . '만약 최근에 GSA ONLINE JUDGE에 가입하지 않으셨다면 이 메세지는 잘못 전송되었습니다. 무시하십시오.';

$lang['email_reset_subject'] = '%s - 비밀번호 재설정 요청';
$lang['email_reset_body'] = '안녕하십니까.<br/><br/>비밀번호를 재설정하기 위해서는 먼저 다음 링크를 클릭하세요. <br/><br/><strong><a href="%1$s/%2$s.php">%1$s/%2$s.php</a></strong><br/><br/>그리고 다음 재설정 키를 사용하여야 합니다. <strong>%3$s</strong><br/><br/>만약 GSA ONLINE JUDGE에서 비밀번호 재설정 키를 요청하지 않았다면 이 메세지는 잘못 전송되었습니다. 무시하십시오.';
$lang['email_reset_altbody'] = '안녕하십니까. ' . "\n\n" . '비밀번호를 재설정하기 위해서는 먼저 다음 링크를 클릭하세요.' . "\n" . '%1$s/%2$s.php' . "\n\n" . '그리고 다음 재설정 키를 사용하여야 합니다. %3$s' . "\n\n" . '만약 GSA ONLINE JUDGE에서 비밀번호 재설정 키를 요청하지 않았다면 이 메세지는 잘못 전송되었습니다. 무시하십시오.';

$lang['account_deleted'] = "계정이 성공적으로 삭제되었습니다.";
$lang['function_disabled'] = "이 기능은 비활성화되었습니다.";


