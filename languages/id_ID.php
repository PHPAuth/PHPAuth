<?php
$lang = array();

$lang['user_blocked'] = "Anda dalam keadaan terkunci oleh sistem.";
$lang['user_verify_failed'] = "Captcha tidak benar.";

$lang['account_email_invalid'] = "Alamat surel salah atau dilarang";
$lang['account_password_invalid'] = "Kata sandi tidak sah";
$lang['account_not_found'] = "Akun dengan surel yang diberikan tidak ditemukan.";

$lang['login_remember_me_invalid'] = "Bidang mengingat saya tidak sah.";

$lang['email_password_invalid'] = "Alamat Email / password tidak tepat.";
$lang['email_password_incorrect'] = "Alamat Email / password tidak benar.";
$lang['remember_me_invalid'] = "Bidang mengingat saya tidak sah.";

$lang['password_short'] = "Kata sandi terlalu pendek.";
$lang['password_weak'] = "Kata sandi terlalu lemah.";
$lang['password_nomatch'] = "Kata sandi tidak sama.";
$lang['password_changed'] = "Sukses merubah kata sandi.";
$lang['password_incorrect'] = "Kata sandi saat ini tidak benar.";
$lang['password_notvalid'] = "Kata sandi tidak sah.";

$lang['newpassword_short'] = "Kata sandi baru terlalu pendek.";
$lang['newpassword_long'] = "Kata sandi baru terlalu panjang.";
$lang['newpassword_invalid'] = "Kata sandi baru harus mengandung setidaknya satu karakter huruf besar dan kecil, dan setidaknya satu digit.";
$lang['newpassword_nomatch'] = "Kata sandi baru tidak sama.";
$lang['newpassword_match'] = "Kata sandi baru sama dengan kata sandi lama.";

$lang['email_short'] = "Surel terlalu pendek.";
$lang['email_long'] = "Surel terlalu panjang.";
$lang['email_invalid'] = "Surel tidak sah.";
$lang['email_incorrect'] = "Surel tidak benar.";
$lang['email_banned'] = "Alamat surel tidak diijinkan.";
$lang['email_changed'] = "Alamat surel sukses diubah.";

$lang['newemail_match'] = "Alamat Surel baru cocok dengan yang sebelumnya.";

$lang['account_inactive'] = "Akun belum diaktifkan.";
$lang['account_activated'] = "Akun sudah diaktifkan.";

$lang['logged_in'] = "Anda sekarang telah masuk.";
$lang['logged_out'] = "Anda sekarang telah keluar.";

$lang['system_error'] = "Kesalahan pada sistem. silahkan coba kembali.";

$lang['register_success'] = "Akun dibuat. Surel aktivasi telah dikirim ke alamat surel.";
$lang['register_success_emailmessage_suppressed'] = "Akun dibuat.";
$lang['email_taken'] = "Surel ini telah digunakan.";

$lang['resetkey_invalid'] = "Kunci reset tidak sah.";
$lang['resetkey_incorrect'] = "Kunci reset tidak benar.";
$lang['resetkey_expired'] = "Kunci reset telah kedaluwarsa.";
$lang['password_reset'] = "Kata sandi sukses direset.";

$lang['activationkey_invalid'] = "Kunci aktivasi tidak sah.";
$lang['activationkey_incorrect'] = "Kunci aktivasi tidak benar.";
$lang['activationkey_expired'] = "Kunci aktivasi kedaluwarsa.";

$lang['reset_requested'] = "Permintaan reset kata sandi telah dikirim ke alamat surel.";
$lang['reset_requested_emailmessage_suppressed'] = "Permintaan reset kata sandi dibuat.";
$lang['reset_exists'] = "Permintaan reset sudah ada. Permintaan reset kata sandi selanjutnya akan tersedia pada %s";

$lang['already_activated'] = "Akun telah diaktifkan.";
$lang['activation_sent'] = "Aktifasi surel telah dikirimkan.";
$lang['activation_exists'] = "Aktifasi surel telah dikirimkan. Pengaktifan kembali selanjutnya akan tersedia pada %s";

$lang['email_activation_subject'] = '%s - Akun aktif';
$lang['email_activation_body'] = 'Halo,<br/><br/> Untuk dapat masuk ke akun Anda, pertama-tama Anda harus mengaktifkan akun Anda dengan mengklik tautan berikut : <strong><a href="%1$s/%2$s">%1$s/%2$ s</a></strong><br/><br/> Kemudian Anda perlu menggunakan kunci aktivasi berikut: <strong>%3$s</strong><br/><br/> Jika tidak daftar di %1$s baru-baru ini lalu pesan ini dikirim karena kesalahan, harap abaikan.';
$lang['email_activation_altbody'] = 'Halo, ' . "\n\n" . 'Untuk dapat masuk ke akun Anda, Anda harus terlebih dahulu mengaktifkan akun Anda dengan mengunjungi tautan berikut :' . "\N" . '%1$s/%2$s' . "\n\n" . 'Anda kemudian perlu menggunakan kunci aktivasi berikut: %3$s' . "\n\n" . 'Jika Anda tidak mendaftar di %1$s baru-baru ini, maka pesan ini dikirim karena kesalahan, abaikan saja.';

$lang['email_reset_subject'] = '%s - Permintaan reset kata sandi';
$lang['email_reset_body'] = 'Halo,<br/><br/>Untuk menyetel ulang sandi, klik tautan berikut:<br/><br/><strong><a href="%1$s/%2$s">%1$s /%2$s</a></strong><br/><br/>Anda kemudian perlu menggunakan kunci setel ulang sandi berikut: <strong>%3$s</strong><br/><br/ >Jika Anda tidak meminta kunci setel ulang kata sandi pada %1$s baru-baru ini, maka pesan ini dikirim karena kesalahan, abaikan saja.';
$lang['email_reset_altbody'] = 'Halo, ' . "\n\n" . 'Untuk mengatur ulang kata sandi Anda, silakan kunjungi tautan berikut :' . "\N" . '%1$s/%2$s' . "\n\n" . 'Anda kemudian perlu menggunakan kunci setel ulang sandi berikut: %3$s' . "\n\n" . 'Jika Anda tidak meminta kunci setel ulang sandi pada %1$s baru-baru ini, maka pesan ini dikirim karena kesalahan, abaikan saja.';

$lang['account_deleted'] = "Akun berhasil dihapus.";
$lang['function_disabled'] = "Fungsi ini telah dinonaktifkan.";
$lang['account_not_found'] = "Tidak ditemukan akun dengan alamat surel tersebut";

$lang['php_version_required'] = "PHPAuth membutuhkan versi PHP %s+!";

return $lang;
