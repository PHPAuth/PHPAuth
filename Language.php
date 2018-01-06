<?php

namespace PHPAuth;

/**
 * PHPAuth Language class
 */

class Language
{
    protected $dbh;
    protected $config;
    protected $lang;

    /**
     *
     * Language::__construct()
     *
     */
    public function __construct(\PDO $dbh, $config)
    {
        $this->dbh = $dbh;
        $this->config = $config;
        
        $langPreferred = $this->getLangId($this->config->language_preferred);
        $langFallback = $this->getLangId($this->config->language_fallback);
        
        $fallback = array();
        $fallback["system_error"] = "A system error has been encountered. Translation text missing.";
        
        $lang = array();
        $lang = array_merge($fallback, $this->getLang($langFallback), $this->getLang($langPreferred));
        $this->lang = $lang;
    }

    /**
     * Language::__get()
     *
     * @param mixed $setting
     * @return string
     */
    public function __get($key)
    {
        if(!array_key_exists($key, $this->lang))
        {
            return $this->lang["system_error"] . " #16";
        }
        
        return $this->lang[$key];
    }
    
    /**
     * Language::getLangId()
     *
     * @param mixed $code
     * @return int
     */
    public function getLangId($code)
    {
        $query = $this->dbh->prepare("SELECT id FROM {$this->config->table_languages} WHERE code = ?");
        $query->execute(array($code));

        $data = $query->fetchColumn();
        
        if (!$data) {
            return false;
        }

        return $data;
    }
    
    /**
     * Language::getLang()
     *
     * @param mixed $id
     * @return array $data
     */
    public function getLang($id)
    {
        $query = $this->dbh->prepare("SELECT `key`, `text` FROM {$this->config->table_translations} WHERE language_id = ?");
        $query->execute(array($id));
        
        $data = $query->fetchAll(\PDO::FETCH_KEY_PAIR);
        
        if (!$data) {
            return false;
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->lang;
    }

}
