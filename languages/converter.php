<?php

$config = array();
$config['name'] = "test";           // Database name
$config['host'] = "localhost";      // Database host
$config['user'] = "test";           // Database user
$config['pass'] = "123456789";      // Database user password

$dbh = new PDO('mysql:dbname=' . $config['name'] . ';host=' . $config['host'] . ';charset=utf8', $config['user'], $config['pass']);

$files = scandir(__DIR__);

foreach($files as $file)
{
    if(is_file($file) && $file != "converter.php")
    {
        $lang = array();
        require $file;
        
        $code = explode("_", $file);
        $code = $code[0];
        
        $query = $dbh->prepare("INSERT INTO languages (`lang`) VALUES (?)");
        
        if($query->execute(array($code)))
        {
            echo "\n" . "SUCCESS: " . $code . "\n";
            
            $langid = $dbh->lastInsertId();
            
            foreach($lang as $key => $text)
            {
                $query = $dbh->prepare("INSERT INTO translations (`lang`, `key`, `text`) VALUES (?, ?, ?)");
                
                if($query->execute(array($langid, $key, $text)))
                {
                    echo "SUCCESS: " . $key . "\n";
                } else {
                    echo "FAIL: " . $key . "\n";
                }
            }
        } else {
            echo "\n" . "FAIL: " . $code . "\n";
        }
    }
}

?>
