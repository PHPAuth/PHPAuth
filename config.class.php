<?php

class Config
{
    private $dbh;
    private $config;
	// replace by your database connection informations
	private $db = array(
		'host' => 'localhost',
		'name' => 'PHPAuth',
		'user' => 'root',
		'pass' => ''
		);

    public function __construct(&$dbh)
    {
        $dbh = new PDO("mysql:host={$this->db['host']};dbname={$this->db['name']}", $this->db['user'], $this->db['pass']);	
        $this->dbh = $dbh;
		unset($db);
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
