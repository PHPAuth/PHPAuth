<?php

class Config
{
    private $dbh;
    private $config;
    private $phpauth_config_table = 'config';

    public function __construct(\PDO $dbh)
    {
        $this->dbh = $dbh;

        $this->config = array();

        $query = $this->dbh->prepare("SELECT * FROM {$this->phpauth_config_table}");
        $query->execute();

        while($row = $query->fetch()) {
            $this->config[$row['setting']] = $row['value'];
        }
    }

    public function __get($setting)
    {
        return $this->config[$setting];
    }

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

?>
