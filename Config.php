<?php

namespace PHPAuth;

/**
 * PHPAuth Config class
 */
class Config
{
    protected $dbh;
    protected $config;
    protected $config_table = 'phpauth_config';

    protected $dictionary = [];

    /**
     * Config::__construct()
     *
     * @param \PDO $dbh
     * @param string $config_table
     * @param string $config_site_language
     */
    public function __construct(\PDO $dbh, $config_table = '', $config_site_language = '')
    {
        if (version_compare(phpversion(), '5.6.0', '<')) {
            die('PHPAuth: PHP 5.6.0+ required for PHPAuth engine!');
        }

        $this->config = array();
        $this->dbh = $dbh;
        $this->config_table = (empty($config_table)) ? 'phpauth_config' : $config_table;

        // check config table exists
        if (! $this->dbh->query("SHOW TABLES LIKE '{$this->config_table}'")->fetchAll() ) {
            die("PHPAuth: Config table `{$this->config_table}` NOT PRESENT in given database" . PHP_EOL);
        };

        // load configuration
        $this->config = $this->dbh->query("SELECT * FROM {$this->config_table}")->fetchAll(\PDO::FETCH_KEY_PAIR);

        $this->setForgottenDefaults(); // Danger foreseen is half avoided.

        // Check required tables exists

        // check table_attempts
        if (! $this->dbh->query("SHOW TABLES LIKE '{$this->config['table_attempts']}'")->fetchAll() ) {
            die("PHPAuth: Table `{$this->config['table_attempts']}` NOT PRESENT in given database" . PHP_EOL);
        };

        // check table requests
        if (! $this->dbh->query("SHOW TABLES LIKE '{$this->config['table_requests']}'")->fetchAll() ) {
            die("PHPAuth: Table `{$this->config['table_requests']}` NOT PRESENT in given database" . PHP_EOL);
        };

        // check table sessions
        if (! $this->dbh->query("SHOW TABLES LIKE '{$this->config['table_sessions']}'")->fetchAll() ) {
            die("PHPAuth: Table `{$this->config['table_sessions']}` NOT PRESENT in given database" . PHP_EOL);
        };

        // check table users
        if (! $this->dbh->query("SHOW TABLES LIKE '{$this->config['table_users']}'")->fetchAll() ) {
            die("PHPAuth: Table `{$this->config['table_users']}` NOT PRESENT in given database" . PHP_EOL);
        };

        // Determine site language
        /*if ($config_site_language !== '') {
            $site_language = $config_site_language;
        } else {
            $site_language = isset($this->config['site_language']) ? $this->config['site_language'] : 'en_GB';
        }*/

        $site_language = (empty($config_site_language))
            ? isset($this->config['site_language']) ? $this->config['site_language'] : 'en_GB'
            : $config_site_language;

        $dictionary = [];

        if (isset($this->config['core_translation_source'])) {

            switch ($this->config['core_translation_source']) {
                case 'php': {

                    // check file exist
                    if (is_file("languages/{$site_language}.php")) {
                        $dictionary = include dirname(__FILE__) . DIRECTORY_SEPARATOR . "languages" . DIRECTORY_SEPARATOR . "{$site_language}.php";
                    } else {
                        $dictionary = $this->setForgottenDictionary();
                    }

                    break;
                }
                case 'ini': {

                    // check file exist
                    if (is_file("languages/{$site_language}.ini")) {
                        $dictionary = parse_ini_file("languages/{$site_language}.ini");
                    } else {
                        $dictionary = $this->setForgottenDictionary();
                    }
                    break;
                }
                case 'database': {

                    // check field `table_translations` present
                    if (empty($this->config['table_translations'])) {
                        $dictionary = $this->setForgottenDictionary();
                        break;
                    }

                    // check table exists in database
                    if (! $this->dbh->query("SHOW TABLES LIKE '{$this->config['table_translations']}'")->fetchAll() ) {
                        $dictionary = $this->setForgottenDictionary();
                        break;
                    };

                    $query = "SELECT `key`, `{$site_language}` as `lang` FROM {$this->config['table_translations']} ";
                    $dictionary = $this->dbh->query($query)->fetchAll(\PDO::FETCH_KEY_PAIR);

                    break;
                }
                case 'xml': {
                    break;
                }
                case 'json': {
                    break;
                }
                default: {
                    $dictionary = $this->setForgottenDictionary();
                }
            } // end switch

        } else {
            $dictionary = $this->setForgottenDictionary();
        }

        $this->config['dictionary'] = $dictionary;

    }

    /**
     * Config::__get()
     *
     * @param mixed $setting
     * @return string
     */
    public function __get($setting)
    {
        return $this->config[$setting];
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->config;
    }

    /**
     * Config::__set()
     *
     * @param mixed $setting
     * @param mixed $value
     * @return bool
     */
    public function __set($setting, $value)
    {
        $query = $this->dbh->prepare("UPDATE {$this->config_table} SET value = ? WHERE setting = ?");

        if ($query->execute(array($value, $setting))) {
            $this->config[$setting] = $value;

            return true;
        }

        return false;
    }

    /**
     * Config::override()
     *
     * @param mixed $setting
     * @param mixed $value
     * @return bool
     */
    public function override($setting, $value)
    {
        $this->config[$setting] = $value;

        return true;
    }

    /**
     * Danger foreseen is half avoided.
     *
     * Set default values.
     * REQUIRED FOR USERS THAT DOES NOT UPDATE THEIR `config` TABLES.
     */
    protected function setForgottenDefaults()
    {
        // verify* values.

        if (!isset($this->config['verify_password_min_length'])) {
            $this->config['verify_password_min_length'] = 3;
        }

        if (!isset($this->config['verify_password_max_length'])) {
            $this->config['verify_password_max_length'] = 150;
        }

        if (!isset($this->config['verify_password_strong_requirements'])) {
            $this->config['verify_password_strong_requirements'] = 1;
        }

        if (!isset($this->config['verify_email_min_length'])) {
            $this->config['verify_email_min_length'] = 5;
        }

        if (!isset($this->config['verify_email_max_length'])) {
            $this->config['verify_email_max_length'] = 100;
        }

        if (!isset($this->config['verify_email_use_banlist'])) {
            $this->config['verify_email_use_banlist'] = 1;
        }

        // emailmessage* values

        if (!isset($this->config['emailmessage_suppress_activation'])) {
            $this->config['emailmessage_suppress_activation'] = 0;
        }

        if (!isset($this->config['emailmessage_suppress_reset'])) {
            $this->config['emailmessage_suppress_reset'] = 0;
        }
		
		if (!isset($this->config['mail_charset'])) {
            $this->config['mail_charset'] = "UTF-8";
        }
	}

    /**
     * Returns forgotten translation dictionary
     *
     * @return array
     */
    private function setForgottenDictionary()
    {
        $lang = array();

        $lang['user_blocked'] = "You are currently locked out of the system.";
        $lang['user_verify_failed'] = "Captcha Code was invalid.";

        $lang['email_password_invalid'] = "Email address / password are invalid.";
        $lang['email_password_incorrect'] = "Email address / password are incorrect.";
        $lang['remember_me_invalid'] = "The remember me field is invalid.";

        $lang['password_short'] = "Password is too short.";
        $lang['password_weak'] = "Password is too weak.";
        $lang['password_nomatch'] = "Passwords do not match.";
        $lang['password_changed'] = "Password changed successfully.";
        $lang['password_incorrect'] = "Current password is incorrect.";
        $lang['password_notvalid'] = "Password is invalid.";

        $lang['newpassword_short'] = "New password is too short.";
        $lang['newpassword_long'] = "New password is too long.";
        $lang['newpassword_invalid'] = "New password must contain at least one uppercase and lowercase character, and at least one digit.";
        $lang['newpassword_nomatch'] = "New passwords do not match.";
        $lang['newpassword_match'] = "New password is the same as the old password.";

        $lang['email_short'] = "Email address is too short.";
        $lang['email_long'] = "Email address is too long.";
        $lang['email_invalid'] = "Email address is invalid.";
        $lang['email_incorrect'] = "Email address is incorrect.";
        $lang['email_banned'] = "This email address is not allowed.";
        $lang['email_changed'] = "Email address changed successfully.";

        $lang['newemail_match'] = "New email matches previous email.";

        $lang['account_inactive'] = "Account has not yet been activated.";
        $lang['account_activated'] = "Account activated.";

        $lang['logged_in'] = "You are now logged in.";
        $lang['logged_out'] = "You are now logged out.";

        $lang['system_error'] = "A system error has been encountered. Please try again.";

        $lang['register_success'] = "Account created. Activation email sent to email.";
        $lang['register_success_emailmessage_suppressed'] = "Account created.";
        $lang['email_taken'] = "The email address is already in use.";

        $lang['resetkey_invalid'] = "Reset key is invalid.";
        $lang['resetkey_incorrect'] = "Reset key is incorrect.";
        $lang['resetkey_expired'] = "Reset key has expired.";
        $lang['password_reset'] = "Password reset successfully.";

        $lang['activationkey_invalid'] = "Activation key is invalid.";
        $lang['activationkey_incorrect'] = "Activation key is incorrect.";
        $lang['activationkey_expired'] = "Activation key has expired.";

        $lang['reset_requested'] = "Password reset request sent to email address.";
        $lang['reset_requested_emailmessage_suppressed'] = "Password reset request is created.";
        $lang['reset_exists'] = "A reset request already exists.";

        $lang['already_activated'] = "Account is already activated.";
        $lang['activation_sent'] = "Activation email has been sent.";
        $lang['activation_exists'] = "An activation email has already been sent.";

        $lang['email_activation_subject'] = '%s - Activate account';
        $lang['email_activation_body'] = 'Hello,<br/><br/> To be able to log in to your account you first need to activate your account by clicking on the following link : <strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/> You then need to use the following activation key: <strong>%3$s</strong><br/><br/> If you did not sign up on %1$s recently then this message was sent in error, please ignore it.';
        $lang['email_activation_altbody'] = 'Hello, ' . "\n\n" . 'To be able to log in to your account you first need to activate your account by visiting the following link :' . "\n" . '%1$s/%2$s' . "\n\n" . 'You then need to use the following activation key: %3$s' . "\n\n" . 'If you did not sign up on %1$s recently then this message was sent in error, please ignore it.';

        $lang['email_reset_subject'] = '%s - Password reset request';
        $lang['email_reset_body'] = 'Hello,<br/><br/>To reset your password click the following link :<br/><br/><strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/>You then need to use the following password reset key: <strong>%3$s</strong><br/><br/>If you did not request a password reset key on %1$s recently then this message was sent in error, please ignore it.';
        $lang['email_reset_altbody'] = 'Hello, ' . "\n\n" . 'To reset your password please visiting the following link :' . "\n" . '%1$s/%2$s' . "\n\n" . 'You then need to use the following password reset key: %3$s' . "\n\n" . 'If you did not request a password reset key on %1$s recently then this message was sent in error, please ignore it.';

        $lang['account_deleted'] = "Account deleted successfully.";
        $lang['function_disabled'] = "This function has been disabled.";

        return $lang;
    }
	

}
