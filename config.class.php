<?php
namespace PHPAuth;

/**
 * PHPAuth config class
 */
class Config
{
    private $dbh;
    private $config;
    private $phpauth_config_table = 'config';

    /**
     * Construct Config class, that contains all configuration values.
     * 
     * It is a little trick: we can call '$config = new Config($dbh, "config_table");' always or
     * we can patch private $phpauth_config_table manually and calls '$config = new Config($dbh);'
     * without any additional values!
     * @param \PDO $dbh             -- PDO database connect handler
     * @param string $config_table  -- config table name
     */
    public function __construct(\PDO $dbh, $config_table = 'config')
    {
        $this->dbh = $dbh;
        
        if (func_num_args() > 1)
            $this->phpauth_config_table = $config_table;

        $this->config = array();

        $query = $this->dbh->prepare("SELECT * FROM {$this->phpauth_config_table}");
        $query->execute();

        while($row = $query->fetch()) {
            $this->config[$row['setting']] = $row['value'];
        }
    }

    /**
     * Return config value
     * @param $setting  -- key
     * @return mixed    -- config value
     */
    public function __get($setting)
    {
        return $this->config[$setting];
    }

    /**
     * Set config value
     * @param $setting  -- key
     * @param $value    -- config value
     * @return bool     -- true if no any errors occured
     */
    public function __set($setting, $value)
    {
        $query = $this->dbh->prepare("UPDATE {$this->phpauth_config_table} SET value = ? WHERE setting = ?");

        if($query->execute(array($value, $setting))) {
            $this->config[$setting] = $value;
            return true;
        } else {
            return false;
        }
    }
}
