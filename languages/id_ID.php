<?php
$lang = array();

$lang['user_blocked'] = "Anda dalam keadaan terkunci oleh sistem.";
$lang['user_verify_failed'] = "Captcha tidak benar.";

$lang['email_password_invalid'] = "Alamat Email / password tidak tepat.";
$lang['email_password_incorrect'] = "Alamat Email / password tidak benar.";
$lang['remember_me_invalid'] = "Ingat login tidak benar.";

$lang['password_short'] = "Password terlalu pendek.";
$lang['password_weak'] = "Password lemah.";
$lang['password_nomatch'] = "Password tidak sama.";
$lang['password_changed'] = "Sukses merubah Password.";
$lang['password_incorrect'] = "Password saat ini tidak tepat.";
$lang['password_notvalid'] = "Password tidak tepat.";

$lang['newpassword_short'] = "Pasword baru terlalu pendek.";
$lang['newpassword_long'] = "Password baru terlalu pendek.";
$lang['newpassword_invalid'] = "Password baru harus memiliki minimal satu hurup besar dan hurup kecil serta, dan minimal angka minimal satu digit.";
$lang['newpassword_nomatch'] = "Password baru tidak sama.";
$lang['newpassword_match'] = "Password sama dengan password yang lama.";

$lang['email_short'] = "Alamat Email terlalu pendek.";
$lang['email_long'] = "Alamat Email terlalu panjang.";
$lang['email_invalid'] = "Alamat Email tidak tepat.";
$lang['email_incorrect'] = "Alamat Email tidak benar.";
$lang['email_banned'] = "Alamat Email ini tidak diperkenankan.";
$lang['email_changed'] = "Alamat Email sukses dirubah.";

$lang['newemail_match'] = "Alamat Email baru persis dengan email sebelumnya.";

$lang['account_inactive'] = "Akun belum di aktifkan.";
$lang['account_activated'] = "Akun sudah di aktifkan..";

$lang['logged_in'] = "Anda sekarang telah login.";
$lang['logged_out'] = "Anda sekarang telah logout";

$lang['system_error'] = "Kesalahan pada sistem. silahkan coba kembali.";

$lang['register_success'] = "Akun telah dibuat email aktivasi telah dikirim kepada alamat email.";
$lang['register_success_emailmessage_suppressed'] = "Akun dibuat.";
$lang['email_taken'] = "Alamat Email ini telah digunakan";

$lang['resetkey_invalid'] = "Kunci reset tidak tepat.";
$lang['resetkey_incorrect'] = "Kunci Reset tidak benar.";
$lang['resetkey_expired'] = "Kunci Reset telah habis masa waktu.";
$lang['password_reset'] = "Password sukses direset.";

$lang['activationkey_invalid'] = "Kunci Aktivasi tidak tepat.";
$lang['activationkey_incorrect'] = "Kunci Aktivasi tidak benar.";
$lang['activationkey_expired'] = "Kunci Aktivasi telah habis masa waktu.";

$lang['reset_requested'] = "Reset Password telah dikirim ke alamat email";
$lang['reset_requested_emailmessage_suppressed'] = "Permintaan reset Password telah dibuat.";
$lang['reset_exists'] = "Permintaan reset sudah ada.";

$lang['already_activated'] = "Akun sudah diaktifkan.";
$lang['activation_sent'] = "Email aktivasi telah dikirim.";
$lang['activation_exists'] = "Sebuah aktivasi email sudah dikirim.";

$lang['email_activation_subject'] = '%s - Aktivasi akun';
$lang['email_activation_body'] = 'Halo,<br/><br/> Agar bisa login dengan akun Anda, pastikan melakukan aktivasi dengan klik link berikut ini
: <strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/> Anda harus menggunakan kunci aktivasi : <strong>%3$s</strong><br/><br/> Jika kamu tidak pernah melakukan pendaftaran di %1$s berarti email ini kesalahan pengiriman, abaikan saja';
$lang['email_activation_altbody'] = 'Halo, ' . "\n\n" . 'Agar bisa login dengan akun Anda, pastikan melakukan aktivasi dengan klik link berikut ini :' . "\n" . '%1$s/%2$s' . "\n\n" . 'Anda harus menggunakan kunci aktivasi: %3$s' . "\n\n" . 'Jika kamu tidak pernah melakukan pendaftaran di %1$s berarti email ini kesalahan pengiriman, abaikan saja';

$lang['email_reset_subject'] = '%s - Permintaan reset password';
$lang['email_reset_body'] = 'Halo,<br/><br/> Untuk melakukan reset password silahkan klik link ini: <br/><br/><strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/>Anda harus menggunakan kunci aktivasi: <strong>%3$s</strong><br/><br/>
Jika Anda tidak melakukan permintaan reset password pada on %1$s berarti email ini kesalahan pengiriman, abaikan saja.';
$lang['email_reset_altbody'] = 'Halo, ' . "\n\n" . 'Untuk melakukan reset password silahkan klik link ini :' . "\n" . '%1$s/%2$s' . "\n\n" . 'Anda harus menggunakan kunci aktivasi: %3$s' . "\n\n" . 'Jika Anda tidak melakukan permintaan reset password pada on %1$s berarti email ini kesalahan pengiriman, abaikan saja..';

$lang['account_deleted'] = "Akun sukses dihapus.";
$lang['function_disabled'] = "Fungsi ini tidak diaktifkan.";
$lang['account_not_found'] = "Tak ada Akun dengan email tersebut.";

return $lang;
