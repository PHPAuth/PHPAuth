<?php

namespace PHPAuth;

use PDO;
use PDOException;
use RuntimeException;

/**
 * PHPAuth Config class
 */
class Config implements ConfigInterface
{
    use Helpers;

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

        // set default 'en_GB' dictionary
        $this->config['dictionary'] = self::getForgottenDictionary();

        /**
         * VERSION '<2.0'
         */
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
        /*
         * ENDIF
         */
        /**
         * SO, AT VERSION 2.0 REMOVE/COMMENT BLOCK '<2.0'
         */

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
        $dictionary_default = self::getForgottenDictionary();

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
