<?php

namespace PHPAuth;

use PDO;
use PDOException;
use PDOStatement;
use RuntimeException;

/**
 * PHPAuth Config class
 */
class Config implements ConfigInterface
{
    /**
     * @var PDO
     */
    protected $dbh;

    /**
     * @var array
     */
    public $config;

    /**
     * @var string
     */
    public $config_table = 'phpauth_config';

    /**
     * Custom E-Mail validator callback
     *
     * @var callable
     */
    public $emailValidator;

    /**
     * Custom Password validator callback
     *
     * @var callable
     */
    public $passwordValidator;

    /**
     * Custom Mailer callback
     *
     * @var callable
     */
    public $customMailer;

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
    public function __construct(PDO $dbh, $config_source = null, string $config_type = self::CONFIG_TYPE_SQL, string $config_site_language = '')
    {
        $config_type = strtolower($config_type);

        if (PHP_VERSION_ID < 70200) {
            die('PHPAuth: PHP 7.2.0+ required for PHPAuth engine!');
        }

        $this->config = [];
        $this->dbh = $dbh;
        $this->dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        switch ($config_type) {
            case self::CONFIG_TYPE_INI : {
                if (empty($config_source)) {
                    throw new RuntimeException('PHPAuth: config type is FILE, but no source file declared!');
                }

                // replace beginner '$' in filepath to application root directory
                $source = preg_replace('/^\$/', getcwd(), $config_source);

                if (!is_readable($source)) {
                    throw new RuntimeException("PHPAuth: config type is FILE, declared as {$source}, but file not readable or not exist");
                }

                // load configuration
                $this->config = parse_ini_file($source);

                break;
            }
            case self::CONFIG_TYPE_ARRAY: {
                if (empty($config_source)) {
                    throw new RuntimeException('PHPAuth: config type is ARRAY, but source config is EMPTY');
                }

                // get configuration from given array
                $this->config = $config_source;

                break;
            }
            case self::CONFIG_TYPE_JSON: {
                throw new RuntimeException('PHPAuth: Config type JSON not supported now');
            }
            case self::CONFIG_TYPE_YML: {
                throw new RuntimeException('PHPAuth: Config type YAML not supported now');
            }
            case self::CONFIG_TYPE_XML: {
                throw new RuntimeException('PHPAuth: Config type XML not supported now');
            }
            case self::CONFIG_TYPE_SQL:
            default:
            {
                // is 'SQL' or EMPTY value
                $this->config_table = (empty($config_source)) ? 'phpauth_config' : $config_source;

                if ($this->checkTableExists($this->config_table) === false) {
                    throw new RuntimeException("PHPAuth: Config table `{$this->config_table}` NOT PRESENT in given database");
                }

                $this->config = $this
                    ->dbh
                    ->query("SELECT `setting`, `value` FROM {$this->config_table} ORDER BY `setting`")
                    ->fetchAll(PDO::FETCH_KEY_PAIR);

                break;
            }
        } // end switch

        $this->setForgottenDefaults(); // Danger foreseen is half avoided.

        // Check required tables exists

        // check table_attempts
        if ($this->checkTableExists($this->config['table_attempts']) === false) {
            throw new RuntimeException("PHPAuth: Config table `{$this->config['table_attempts']}` NOT PRESENT in given database");
        }

        if ($this->checkTableExists($this->config['table_requests']) === false) {
            throw new RuntimeException("PHPAuth: Config table `{$this->config['table_requests']}` NOT PRESENT in given database");
        }

        if ($this->checkTableExists($this->config['table_sessions']) === false) {
            throw new RuntimeException("PHPAuth: Config table `{$this->config['table_sessions']}` NOT PRESENT in given database");
        }

        if ($this->checkTableExists($this->config['table_users']) === false) {
            throw new RuntimeException("PHPAuth: Config table `{$this->config['table_users']}` NOT PRESENT in given database");
        }

        // Determine site language
        $site_language = (empty($config_site_language))
            ? $this->config['site_language'] ?? 'en_GB'
            : $config_site_language;

        switch ($this->config['translation_source']) {
            case 'php': {
                $lang_file = __DIR__ . DIRECTORY_SEPARATOR . '../languages' . DIRECTORY_SEPARATOR . "{$site_language}.php";
                if (is_readable($lang_file)) {
                    $dictionary_new = include $lang_file;
                }
                break;
            }
            case 'ini': {
                $lang_file = __DIR__ . DIRECTORY_SEPARATOR . '../languages' . DIRECTORY_SEPARATOR . "{$site_language}.ini";

                if (is_readable($lang_file)) {
                    $dictionary_new = parse_ini_file($lang_file);
                }
                break;
            }
            case 'sql': {
                if ($this->config['table_translations'] && $this->checkTableExists($this->config['table_translations'])) {
                    $dictionary_new =
                        $this
                            ->dbh
                            ->query("SELECT `translation_key`, `{$site_language}` as `lang` FROM {$this->config['table_translations']} ")
                            ->fetchAll(PDO::FETCH_KEY_PAIR);
                }
            }
        }

        $this->setLocalization($dictionary_new);

        // set reCaptcha config
        $config_recaptcha = [];

        if (array_key_exists('recaptcha_enabled', $this->config)) {
            $config_recaptcha['recaptcha_enabled'] = $this->config['recaptcha_enabled'];
            $config_recaptcha['recaptcha_site_key'] = $this->config['recaptcha_site_key'];
            $config_recaptcha['recaptcha_secret_key'] = $this->config['recaptcha_secret_key'];
        }

        $this->config['recaptcha'] = $config_recaptcha;
    }

    public function setEMailValidator(callable $callable = null):Config
    {
        if (!is_null($callable) && is_callable($callable)) {
            $this->emailValidator = $callable;
        }

        return $this;
    }

    public function setPasswordValidator(callable $callable = null):Config
    {
        if (!is_null($callable) && is_callable($callable)) {
            $this->passwordValidator = $callable;
        }

        return $this;
    }

    public function setLocalization(array $dictionary):Config
    {
        $dictionary_default = $this->getForgottenDictionary();

        foreach ($dictionary_default as $key => $value) {
            if (array_key_exists($key, $dictionary) && !empty($dictionary[$key])) {
                $dictionary_default[$key] = $dictionary[$key];
            }
        }
        $this->config['dictionary'] = $dictionary;

        return $this;
    }

    /**
     *
     *
     * @param callable|null $callable
     * @return $this
     */
    public function setCustomMailer(callable $callable = null):Config
    {
        if (!is_null($callable) && is_callable($callable)) {
            $this->customMailer = $callable;
        }

        return $this;
    }

    /**
     * Config::__get()
     *
     * @param string $setting
     * @return string|int
     */
    public function __get(string $setting)
    {
        return array_key_exists($setting, $this->config) ? $this->config[$setting] : null;
    }

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
     * Set default values.
     *
     * REQUIRED FOR USERS THAT DOES NOT UPDATE THEIR `config` TABLES.
     */
    protected function setForgottenDefaults()
    {
        // ==== unchecked values ====
        $this->repairConfigValue('bcrypt_cost', 10);

        $this->repairConfigValue('cookie_name', 'phpauth_session_cookie');

        $this->repairConfigValue('verify_password_min_length', 3);

        $this->repairConfigValue('verify_email_min_length', 5);

        $this->repairConfigValue('verify_email_max_length', 100);

        $this->repairConfigValue('verify_email_use_banlist', 1);

        $this->repairConfigValue('emailmessage_suppress_activation', 0);

        $this->repairConfigValue('emailmessage_suppress_reset', 0);

        $this->repairConfigValue('mail_charset', 'UTF-8');

        $this->repairConfigValue('allow_concurrent_sessions', false);
    }

    /**
     * Set configuration value if it is not present.
     *
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
    protected function getForgottenDictionary(): array
    {
        $lang = array();

        $lang['account_activated'] = 'Account activated.';
        $lang['account_deleted'] = 'Account deleted successfully.';
        $lang['account_inactive'] = 'Account has not yet been activated.';
        $lang['email_taken'] = 'The email address is already in use.';
        $lang['activation_exists'] = 'An activation email has already been sent.';
        $lang['activation_sent'] = 'Activation email has been sent.';
        $lang['already_activated'] = 'Account is already activated.';
        $lang['activationkey_expired'] = 'Activation key has expired.';
        $lang['activationkey_incorrect'] = 'Activation key is incorrect.';
        $lang['activationkey_invalid'] = 'Activation key is invalid.';
        $lang['captcha_verify_failed'] = 'Captcha Code was invalid.';
        $lang['user_verify_failed'] = 'Captcha Code was invalid.';
        $lang['email_banned'] = 'This email address is not allowed.';
        $lang['email_changed'] = 'Email address changed successfully.';
        $lang['email_incorrect'] = 'Email address is incorrect.';
        $lang['email_invalid'] = 'Email address is invalid.';
        $lang['email_long'] = 'Email address is too long.';
        $lang['email_short'] = 'Email address is too short.';
        $lang['newemail_match'] = 'New email matches previous email.';
        $lang['email_activation_altbody'] = 'Hello,

To be able to log in to your account you first need to activate your account by visiting the following link :
%1$s/%2$s

You then need to use the following activation key: %3$s

If you did not sign up on %1$s recently then this message was sent in error, please ignore it.';
        $lang['email_activation_body'] = 'Hello,&lt;br/&gt;&lt;br/&gt; To be able to log in to your account you first need to activate your account by clicking on the following link : &lt;strong&gt;&lt;a href="%1$s/%2$s"&gt;%1$s/%2$s&lt;/a&gt;&lt;/strong&gt;&lt;br/&gt;&lt;br/&gt; You then need to use the following activation key: &lt;strong&gt;%3$s&lt;/strong&gt;&lt;br/&gt;&lt;br/&gt; If you did not sign up on %1$s recently then this message was sent in error, please ignore it.';
        $lang['email_activation_subject'] = '%s - Activate account';
        $lang['email_reset_altbody'] = 'Hello,

To reset your password please visiting the following link :
%1$s/%2$s

You then need to use the following password reset key: %3$s

If you did not request a password reset key on %1$s recently then this message was sent in error, please ignore it.';
        $lang['email_reset_body'] = 'Hello,&lt;br/&gt;&lt;br/&gt;To reset your password click the following link :&lt;br/&gt;&lt;br/&gt;&lt;strong&gt;&lt;a href="%1$s/%2$s"&gt;%1$s/%2$s&lt;/a&gt;&lt;/strong&gt;&lt;br/&gt;&lt;br/&gt;You then need to use the following password reset key: &lt;strong&gt;%3$s&lt;/strong&gt;&lt;br/&gt;&lt;br/&gt;If you did not request a password reset key on %1$s recently then this message was sent in error, please ignore it.';
        $lang['email_reset_subject'] = '%s - Password reset request';
        $lang['logged_in'] = 'You are now logged in.';
        $lang['logged_out'] = 'You are now logged out.';
        $lang['newpassword_invalid'] = 'New password must contain at least one uppercase and lowercase character, and at least one digit.';
        $lang['newpassword_long'] = 'New password is too long.';
        $lang['newpassword_match'] = 'New password is the same as the old password.';
        $lang['newpassword_nomatch'] = 'New passwords do not match.';
        $lang['newpassword_short'] = 'New password is too short.';
        $lang['password_changed'] = 'Password changed successfully.';
        $lang['password_incorrect'] = 'Current password is incorrect.';
        $lang['password_nomatch'] = 'Passwords do not match.';
        $lang['password_notvalid'] = 'Password is invalid.';
        $lang['password_short'] = 'Password is too short.';
        $lang['password_weak'] = 'Password is too weak.';
        $lang['register_success'] = 'Account created. Activation email sent to email.';
        $lang['register_success_emailmessage_suppressed'] = 'Account created.';
        $lang['password_reset'] = 'Password reset successfully.';
        $lang['resetkey_expired'] = 'Reset key has expired.';
        $lang['resetkey_incorrect'] = 'Reset key is incorrect.';
        $lang['resetkey_invalid'] = 'Reset key is invalid.';
        $lang['reset_exists'] = 'A reset request already exists.';
        $lang['reset_requested'] = 'Password reset request sent to email address.';
        $lang['reset_requested_emailmessage_suppressed'] = 'Password reset request is created.';
        $lang['function_disabled'] = 'This function has been disabled.';
        $lang['system_error'] = 'A system error has been encountered. Please try again.';
        $lang['user_blocked'] = 'You are currently locked out of the system.';
        $lang['user_login_account_inactive'] = 'The account isn\'t activated yet. ';
        $lang['user_login_incorrect_password'] = 'Incorrect Password.';
        $lang['user_register_email_taken'] = 'E-mail already in use.';
        $lang['user_register_success'] = 'The account has been created. Activation instructions sent to the provided e-mail.';
        $lang['user_validate_email_incorrect'] = 'Incorrect email format.';
        $lang['user_validate_password_incorrect'] = 'Password too short, too long or otherwise doesn\'t match the requirements.';
        $lang['user_validate_remember_me_invalid'] = 'Unacceptable &amp;ldquo;remember user&amp;rdquo; field value.';
        $lang['user_validate_user_not_found'] = 'This e-mail is not registered.';
        $lang['account_not_found'] = 'Email address / password are incorrect.';
        $lang['email_password_incorrect'] = 'Email address / password are incorrect.';
        $lang['email_password_invalid'] = 'Email address / password are invalid.';
        $lang['remember_me_invalid'] = 'The remember me field is invalid.';

        $lang['php_version_required'] = 'PHPAuth engine requires PHP version %s+!';

        return $lang;
    }

    /**
     * Check is given table exists, depends on database driver
     *
     * @param string|null $table
     * @return bool
     */
    protected function checkTableExists(string $table = null):bool
    {
        if (empty($table)) {
            return false;
        }

        switch ($this->dbh->getAttribute(PDO::ATTR_DRIVER_NAME)) {
            case 'pgsql': {
                $sth = $this->dbh->query("SELECT FROM pg_tables WHERE tablename = '{$table}' ;");
                return (bool)$sth->rowCount();
                break;
            }
            case 'mysql': {
                $sth = $this->dbh->query("SELECT EXISTS(SELECT 1 FROM information_schema.TABLES WHERE TABLE_NAME = '{$table}' AND TABLE_SCHEMA in (SELECT DATABASE()));");
                return (bool)$sth->fetchColumn();
                break;
            }
            case 'sqlite': {
                $sth = $this->dbh->query("PRAGMA table_info({$table})  ");
                $schema = $sth->fetchAll();
                return (!empty($schema));
            }
            default: {
                // Legacy databases (MS SQL, Informix and so on)
                try {
                    $this->dbh->query("SELECT * FROM {$table} LIMIT 1;");
                } catch (PDOException $e) {
                    return false;
                }
                return true;
            }
        } // switch

        return false;
    }
}
