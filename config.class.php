<?php
namespace PHPAuth;

/**
 *
 */
class Config
{
    private $dbh;
    private $config;
    private $config_table = 'config';

    /**
     * Config::__construct()
     *
     * @param \PDO $dbh
     * @param string $config_table
     */
    public function __construct(\PDO $dbh, $config_table = 'config')
    {
        $this->dbh = $dbh;

        if (func_num_args() > 1)
            $this->phpauth_config_table = $config_table;

        $this->config = array();

        $query = $this->dbh->query("SELECT * FROM {$this->phpauth_config_table}");

        while ($row = $query->fetch()) {
            $this->config[$row['setting']] = $row['value'];
        }
    }
 

    /**
     * Config::__get()
     *
     * @param mixed $setting
     * @return
     */
    public function __get($setting)
    {
        return $this->config[$setting];
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
        $query = $this->dbh->prepare("UPDATE {$this->phpauth_config_table} SET value = ? WHERE setting = ?");

        if ($query->execute(array($value, $setting))) {
            $this->config[$setting] = $value;
            return true;
        }
        return false;
    }
}