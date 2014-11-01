<?php

class Config
{
    private $dbh;
    private $config;

    public function __construct(\PDO $dbh)
    {
        $this->dbh = $dbh;

        $this->config = array();

        $query = $this->dbh->prepare("SELECT * FROM config");
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
        $query = $this->dbh->prepare("UPDATE config SET value = ? WHERE setting = ?");

        if($query->execute(array($value, $setting))) {
            $this->config[$setting] = $value;
            return true;
        } else {
            return false;
        }
    }
}

?>
