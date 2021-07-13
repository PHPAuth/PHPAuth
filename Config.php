<?php

namespace PHPAuth;

use PDO;
use PDOException;
use PDOStatement;

/**
 * PHPAuth Config class
 */
class Config
{
    protected $dbh;
    public $config;
    public $config_table = 'phpauth_config';

    /**
     * Config::__construct()
     *
     * Create config class for PHPAuth\Auth.
     * Examples:
     *
     * new Config($dbh) -- Defaults will be used: load config from SQL table phpauth_config, language is 'en_GB'
     * new Config($dbh, 'config_table', '') -- 3rd argument is 'sql' or '' => 2rd argument determines config table in DB, default 'phpauth_config'
     * new Config($dbh, '$/config/phpauth.ini', 'ini') -- configuration will be loaded from INI file, '$' means Application basedir
     * new Config($dbh, $CONFIG_ARRAY, 'array') -- configuration must be defined in $CONFIG_ARRAY value
     *
     * in any case, 4th argument defines site language as locale code
     *
     * @param PDO $dbh
     * @param string|array $config_source -- declare source of config - table name, filepath or data-array
     * @param string $config_type -- default empty (means config in SQL table phpauth_config), possible values: 'sql', 'ini', 'array'
     * @param string $config_site_language -- declare site language, empty value means 'en_GB'
     */
    public function __construct(PDO $dbh, $config_source = null, string $config_type = '', string $config_site_language = '')
    {
        $config_type = strtolower($config_type);

        if (version_compare(phpversion(), '7.1.0', '<')) {
            die('PHPAuth: PHP 7.1.0+ required for PHPAuth engine!');
        }

        $this->config = [];
        $this->dbh = $dbh;

        switch ($config_type) {
            case 'ini':
            {

                // check valid keys
                if (empty($config_source)) {
                    die('PHPAuth: config type is FILE, but no source file declared!');
                } //@todo: \Exception

                // replace beginner '$' in filepath to application root directory
                $source = preg_replace('/^\$/', getcwd(), $config_source);

                // check ini-config is readable
                if (!is_readable($source)) {
                    die("PHPAuth: config type is FILE, declared as {$source}, but file not readable or not exist");
                } //@todo: \Exception

                // load configuration
                $this->config = parse_ini_file($source);

                break;
            }
            case 'array':
            {
                // check data is valid
                if (empty($config_source)) {
                    die('PHPAuth: config type is ARRAY, but source config is EMPTY');
                } //@todo: \Exception

                // get configuration from given array
                $this->config = $config_source;

                break;
            }
            case 'json':
            case 'yml':
            case 'xml':
            {
                break;
            }
            default:
            {
                // is 'SQL' or EMPTY value
                //
                // determine config table
                $this->config_table = (empty($config_source)) ? 'phpauth_config' : $config_source;

                // load configuration
                try {
                    $configQuery = $this->dbh->query("SELECT `setting`, `value` FROM {$this->config_table};");

                    if ($configQuery instanceof PDOStatement) {
                        $this->config = $configQuery->fetchAll(PDO::FETCH_KEY_PAIR);
                    } else {
                        throw new PDOException();
                    }
                } catch (PDOException $e) {
                    die("PHPAuth: Config table `{$this->config_table}` NOT PRESENT in given database" . PHP_EOL);
                }

                break;
            }
        } // end switch

        $this->setForgottenDefaults(); // Danger foreseen is half avoided.

        // Check required tables exists

        // check table_attempts
        try {
            $this->dbh->query("SELECT * FROM {$this->config['table_attempts']} LIMIT 1;");
        } catch (PDOException $e) {
            die("PHPAuth: Config table `{$this->config['table_attempts']}` NOT PRESENT in given database" . PHP_EOL);
        }

        // check table requests
        try {
            $this->dbh->query("SELECT * FROM {$this->config['table_requests']} LIMIT 1;");
        } catch (PDOException $e) {
            die("PHPAuth: Config table `{$this->config['table_requests']}` NOT PRESENT in given database" . PHP_EOL);
        }

        // check table sessions
        try {
            $this->dbh->query("SELECT * FROM {$this->config['table_sessions']} LIMIT 1;");
        } catch (PDOException $e) {
            die("PHPAuth: Config table `{$this->config['table_sessions']}` NOT PRESENT in given database" . PHP_EOL);
        }

        // check table users
        try {
            $this->dbh->query("SELECT * FROM {$this->config['table_users']} LIMIT 1;");
        } catch (PDOException $e) {
            die("PHPAuth: Config table `{$this->config['table_users']}` NOT PRESENT in given database" . PHP_EOL);
        }

        // Determine site language
        $site_language = (empty($config_site_language))
            ? isset($this->config['site_language']) ? $this->config['site_language'] : 'en_GB'
            : $config_site_language;

        $dictionary = [];

        if (isset($this->config['translation_source'])) {
            switch ($this->config['translation_source']) {
                case 'php':
                {

                    $lang_file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR . "{$site_language}.php";

                    if (is_readable($lang_file)) {
                        $dictionary = include $lang_file;
                    } else {
                        $dictionary = $this->setForgottenDictionary();
                    }

                    break;
                }
                case 'ini':
                {

                    $lang_file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR . "{$site_language}.ini";

                    if (is_readable($lang_file)) {
                        $dictionary = parse_ini_file($lang_file);
                    } else {
                        $dictionary = $this->setForgottenDictionary();
                    }
                    break;
                }
                case 'sql':
                {

                    // check field `table_translations` present
                    if (empty($this->config['table_translations'])) {
                        $dictionary = $this->setForgottenDictionary();
                        break;
                    }

                    // check table exists in database
                    try {
                        $this->dbh->query("SELECT * FROM {$this->config['table_translations']} LIMIT 1;");
                    } catch (PDOException $e) {
                        $dictionary = $this->setForgottenDictionary();
                        break;
                    }

                    $query = "SELECT `translation_key`, `{$site_language}` as `lang` FROM {$this->config['table_translations']} ";
                    $dictionary = $this->dbh->query($query)->fetchAll(PDO::FETCH_KEY_PAIR);

                    break;
                }
                case 'xml':
                case 'json':
                {
                    break;
                }
                default:
                {
                    $dictionary = $this->setForgottenDictionary();
                }
            } // end switch
        } else {
            $dictionary = $this->setForgottenDictionary();
        }

        // set dictionary
        $this->config['dictionary'] = $dictionary;

        // set reCaptcha config
        $config_recaptcha = [];

        if (array_key_exists('recaptcha_enabled', $this->config)) {
            $config_recaptcha['recaptcha_enabled'] = $this->config['recaptcha_enabled'];
            $config_recaptcha['recaptcha_site_key'] = $this->config['recaptcha_site_key'];
            $config_recaptcha['recaptcha_secret_key'] = $this->config['recaptcha_secret_key'];
        }

        $this->config['recaptcha'] = $config_recaptcha;
    }

    /**
     * Config::__get()
     *
     * @param string $setting
     *
     * @return string|int
     */
    public function __get(string $setting)
    {
        return array_key_exists($setting, $this->config) ? $this->config[$setting] : null;
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        return $this->config;
    }

    /**
     * Config::__set()
     *
     * @param string $setting
     * @param mixed $value
     *
     * @return bool
     */
    public function __set(string $setting, $value)
    {
        $query_prepared = $this->dbh->prepare("UPDATE {$this->config_table} SET value = :value WHERE setting = :setting");

        if ($query_prepared->execute(['value' => $value, 'setting' => $setting])) {
            $this->config[$setting] = $value;

            return true;
        }

        return false;
    }

    /**
     * Config::override()
     *
     * @param string $setting
     * @param mixed $value
     *
     * @return bool
     */
    public function override(string $setting, $value): bool
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
        // ==== unchecked values ====
        $this->repairConfigValue('bcrypt_cost', 10);

        // cookies* values
        $this->repairConfigValue('cookie_name', 'phpauth_session_cookie');

        // verify* values
        $this->repairConfigValue('verify_password_min_length', 3);

        $this->repairConfigValue('verify_email_min_length', 5);

        $this->repairConfigValue('verify_email_max_length', 100);

        $this->repairConfigValue('verify_email_use_banlist', 1);

        // emailmessage* values

        $this->repairConfigValue('emailmessage_suppress_activation', 0);

        $this->repairConfigValue('emailmessage_suppress_reset', 0);

        $this->repairConfigValue('mail_charset', 'UTF-8');

        // others
        $this->repairConfigValue('allow_concurrent_sessions', false);
    }

    /**
     * Set configuration value if it is not present.
     * @param string $setting
     * @param mixed $default_value
     */
    protected function repairConfigValue(string $setting, $default_value)
    {
        if (!isset($this->config[$setting])) {
            $this->config[ $setting ] = $default_value;
        }
    }

    /**
     * Returns forgotten translation dictionary
     *
     * @return array
     */
    protected function setForgottenDictionary(): array
    {
        $lang = array();

        $lang['user_blocked'] = 'You are currently locked out of the system.';
        $lang['user_verify_failed'] = 'Captcha Code was invalid.';

        $lang['account_email_invalid'] = 'Email address is incorrect or banned';
        $lang['account_password_invalid'] = 'Password is invalid';
        $lang['account_not_found'] = 'Account with given email not found.';

        $lang['login_remember_me_invalid'] = 'The remember me field is invalid.';

        $lang['email_password_invalid'] = 'Email address / password are invalid.';
        $lang['email_password_incorrect'] = 'Email address / password are incorrect.';
        $lang['remember_me_invalid'] = 'The remember me field is invalid.';

        $lang['password_short'] = 'Password is too short.';
        $lang['password_weak'] = 'Password is too weak.';
        $lang['password_nomatch'] = 'Passwords do not match.';
        $lang['password_changed'] = 'Password changed successfully.';
        $lang['password_incorrect'] = 'Current password is incorrect.';
        $lang['password_notvalid'] = 'Password is invalid.';

        $lang['newpassword_short'] = 'New password is too short.';
        $lang['newpassword_long'] = 'New password is too long.';
        $lang['newpassword_invalid'] = 'New password must contain at least one uppercase and lowercase character, and at least one digit.';
        $lang['newpassword_nomatch'] = 'New passwords do not match.';
        $lang['newpassword_match'] = 'New password is the same as the old password.';

        $lang['email_short'] = 'Email address is too short.';
        $lang['email_long'] = 'Email address is too long.';
        $lang['email_invalid'] = 'Email address is invalid.';
        $lang['email_incorrect'] = 'Email address is incorrect.';
        $lang['email_banned'] = 'This email address is not allowed.';
        $lang['email_changed'] = 'Email address changed successfully.';

        $lang['newemail_match'] = 'New email matches previous email.';

        $lang['account_inactive'] = 'Account has not yet been activated.';
        $lang['account_activated'] = 'Account activated.';

        $lang['logged_in'] = 'You are now logged in.';
        $lang['logged_out'] = 'You are now logged out.';

        $lang['system_error'] = 'A system error has been encountered. Please try again.';

        $lang['register_success'] = 'Account created. Activation email sent to email.';
        $lang['register_success_emailmessage_suppressed'] = 'Account created.';
        $lang['email_taken'] = 'The email address is already in use.';

        $lang['resetkey_invalid'] = 'Reset key is invalid.';
        $lang['resetkey_incorrect'] = 'Reset key is incorrect.';
        $lang['resetkey_expired'] = 'Reset key has expired.';
        $lang['password_reset'] = 'Password reset successfully.';

        $lang['activationkey_invalid'] = 'Activation key is invalid.';
        $lang['activationkey_incorrect'] = 'Activation key is incorrect.';
        $lang['activationkey_expired'] = 'Activation key has expired.';

        $lang['reset_requested'] = 'Password reset request sent to email address.';
        $lang['reset_requested_emailmessage_suppressed'] = 'Password reset request is created.';
        $lang['reset_exists'] = 'A reset request already exists. Next reset password request will available at %s';             //@todo: updated 2018-06-28

        $lang['already_activated'] = 'Account is already activated.';
        $lang['activation_sent'] = 'Activation email has been sent.';
        $lang['activation_exists'] = 'An activation email has already been sent. Next reactivation will available at %s';       //@todo: updated 2018-06-28

        $lang['email_activation_subject'] = '%s - Activate account';
        $lang['email_activation_body'] = 'Hello,<br/><br/> To be able to log in to your account you first need to activate your account by clicking on the following link : <strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/> You then need to use the following activation key: <strong>%3$s</strong><br/><br/> If you did not sign up on %1$s recently then this message was sent in error, please ignore it.';
        $lang['email_activation_altbody'] = 'Hello, ' . "\n\n" . 'To be able to log in to your account you first need to activate your account by visiting the following link :' . "\n" . '%1$s/%2$s' . "\n\n" . 'You then need to use the following activation key: %3$s' . "\n\n" . 'If you did not sign up on %1$s recently then this message was sent in error, please ignore it.';

        $lang['email_reset_subject'] = '%s - Password reset request';
        $lang['email_reset_body'] = 'Hello,<br/><br/>To reset your password click the following link :<br/><br/><strong><a href="%1$s/%2$s">%1$s/%2$s</a></strong><br/><br/>You then need to use the following password reset key: <strong>%3$s</strong><br/><br/>If you did not request a password reset key on %1$s recently then this message was sent in error, please ignore it.';
        $lang['email_reset_altbody'] = 'Hello, ' . "\n\n" . 'To reset your password please visiting the following link :' . "\n" . '%1$s/%2$s' . "\n\n" . 'You then need to use the following password reset key: %3$s' . "\n\n" . 'If you did not request a password reset key on %1$s recently then this message was sent in error, please ignore it.';

        $lang['account_deleted'] = 'Account deleted successfully.';
        $lang['function_disabled'] = 'This function has been disabled.';
        $lang['account_not_found'] = 'No account found with that email address';

        $lang['php_version_required'] = 'PHPAuth engine requires PHP version %s+!';

        return $lang;
    }
}
