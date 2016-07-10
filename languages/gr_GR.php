<?php
$lang = array();

$lang['user_blocked'] = "Αυτήν τη στιγμή είστε μπλοκαρισμένοι από το σύστημα.";
$lang['user_verify_failed'] = "Ο κωδικός Captcha ήταν λανθασμένος.";

$lang['email_password_invalid'] = "Το Email / password είναι μη έγκυρα.";
$lang['email_password_incorrect'] = "Το Email address / password είναι λανθασμένα.";
$lang['remember_me_invalid'] = "Το πεδίο remember me είναι μη έγκυρο.";

$lang['password_short'] = "Ο κωδικός είναι πολύ μικρός.";
$lang['password_weak'] = "Ο κωδικός είναι πολύ αδύναμος.";
$lang['password_nomatch'] = "Οι κωδικοί δεν ταιριάζουν.";
$lang['password_changed'] = "Ο κωδικός άλλαξε επιτυχώς.";
$lang['password_incorrect'] = "Ο τρέχων κωδικός είναι λανθασμένος.";
$lang['password_notvalid'] = "Ο κωδικός είναι μη έγκυρος.";

$lang['newpassword_short'] = "Ο νέος κωδικός είναι πολύ μικρός.";
$lang['newpassword_long'] = "Ο νέος κωδικός είναι πολύ μεγάλος.";
$lang['newpassword_invalid'] = "Ο νέος κωδικός πρέπει να περιέχει τουλάχιστον ένα κεφαλαίο χαρακτήρα, ένα μικρό χαρακτήρα και έναν αριθμό.";
$lang['newpassword_nomatch'] = "Οι νέοι κωδικοί δεν ταιριάζουν.";
$lang['newpassword_match'] = "Ο νέος κωδικός είναι ίδιος με τον παλιό.";

$lang['email_short'] = "Το Email είναι πολύ μικρό.";
$lang['email_long'] = "Το Email είναι πολύ μεγάλο.";
$lang['email_invalid'] = "Το Email δεν είναι έγκυρο.";
$lang['email_incorrect'] = "Το Email είναι λανθασμένο.";
$lang['email_banned'] = "Αυτή η διεύθυνση email δεν επιτρέπεται.";
$lang['email_changed'] = "Το Email άλλαξε επιτυχώς.";

$lang['newemail_match'] = "Το καινούριο email είναι ίδιο με το παλιό.";

$lang['account_inactive'] = "Ο λογαριασμός δεν έχει ενεργοποιηθεί ακόμα.";
$lang['account_activated'] = "Ο λογαριασμός ενεργοποιήθηκε.";

$lang['logged_in'] = "Είστε συνδεδεμένος.";
$lang['logged_out'] = "Αποσυνδεθήκατε.";

$lang['system_error'] = "Το σύστημα αντιμετώπισε ένα σφάλμα. Προσπαθήστε ξανά.";

$lang['register_success'] = "Ο λογαριασμός δημιουργήθηκε. Το email ενεργοποίησης στάλθηκε στη διεύθυνση email.";
$lang['register_success_emailmessage_suppressed'] = "Ο λογαριασμός δημιουργήθηκε.";
$lang['email_taken'] = "Το email χρησιμοποιείται ήδη.";

$lang['resetkey_invalid'] = "Το κλειδί επαναφοράς δεν είναι έγκυρο.";
$lang['resetkey_incorrect'] = "Το κλειδί επαναφοράς είναι λανθασμένο.";
$lang['resetkey_expired'] = "Το κλειδί επαναφοράς έχει λήξει.";
$lang['password_reset'] = "Επαναφορά κωδικού επιτυχής.";

$lang['activationkey_invalid'] = "Το κλειδί ενεργοποίησης δεν είναι έγκυρο.";
$lang['activationkey_incorrect'] = "Το κλειδί ενεργοποίησης είναι λανθασμένο.";
$lang['activationkey_expired'] = "Το κλειδί ενεργοποίησης έχει λήξει.";

$lang['reset_requested'] = "Η αίτηση επαναφοράς κωδικού στάλθηκε στη διεύθυνση email.";
$lang['reset_requested_emailmessage_suppressed'] = "Η αίτηση επαναφοράς κωδικού δημιουργήθηκε.";
$lang['reset_exists'] = "Μία αίτηση επαναφοράς κωδικού υπάρχει ήδη.";

$lang['already_activated'] = "Ο λογαριασμός έχει ήδη ενεργοποιηθεί";
$lang['activation_sent'] = "Το email ενεργοποίησης στάλθηκε";
$lang['activation_exists'] = "Το email ενεργοποίησης έχει ήδη σταλεί.";

$lang['email_activation_subject'] = '%s - Ενεργοποίηση λογαριασμού';
$lang['email_activation_body'] = 'Γεια σου,<br/><br/> Για να μπορέσεις να συνδεθείς στο λογαριασμό σου πρέπει πρώτα να τον ενεργοποιήσεις κάνοντας κλικ στο παρακάτω link : <strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/> Έπειτα, πρέπει να χρησιμοποιήσεις το παρακάτω κλειδί ενεργοποίησης: <strong>%3$s</strong><br/><br/> Αν δεν έκανες εγγραφή στο %1$s πρόσφατα, αυτό το email στάλθηκε κατά λάθος και μπορείς να το αγνοήσεις.';
$lang['email_activation_altbody'] = 'Γεια σου, ' . "\n\n" . 'Για να μπορέσεις να συνδεθείς στο λογαριασμό σου πρέπει πρώτα να τον ενεργοποιήσεις κάνοντας κλικ στο παρακάτω link :' . "\n" . '%1$s/%2$s' . "\n\n" . 'Έπειτα, πρέπει να χρησιμοποιήσεις το παρακάτω κλειδί ενεργοποίησης: %3$s' . "\n\n" . 'Αν δεν έκανες εγγραφή στο %1$s πρόσφατα, αυτό το email στάλθηκε κατά λάθος και μπορείς να το αγνοήσεις.';

$lang['email_reset_subject'] = '%s - Αίτηση επαναφοράς κωδικού';
$lang['email_reset_body'] = 'Γεια σου,<br/><br/>Για να επαναφέρεις τον κωδικό σου κάνε κλικ στο παρακάτω link :<br/><br/><strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/>Στη συνέχεια, χρησιμοποίησε το παρακάτω κλειδί επαναφοράς κωδικού: <strong>%3$s</strong><br/><br/>Αν δεν ζήτησες επαναφορά κωδικού στο %1$s πρόσφατα, μπορείς να αγνοήσεις αυτό το μήνυμα.';
$lang['email_reset_altbody'] = 'Γεια σου, ' . "\n\n" . 'Για να επαναφέρεις τον κωδικό σου κάνε κλικ στο παρακάτω link :' . "\n" . '%1$s/%2$s' . "\n\n" . 'Στη συνέχεια, χρησιμοποίησε το παρακάτω κλειδί επαναφοράς κωδικού: %3$s' . "\n\n" . 'Αν δεν ζήτησες επαναφορά κωδικού στο %1$s πρόσφατα, μπορείς να αγνοήσεις αυτό το μήνυμα.';

$lang['account_deleted'] = "Ο λογαριασμός διαγράφτηκε επιτυχώς..";
$lang['function_disabled'] = "Αυτή η λειτουργία έχει απενεργοποιηθεί.";