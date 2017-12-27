<?php

$lang = array();
require "en_GB.php";                // PHPAuth-compatible language-file to convert

$config = array();
$config['name'] = "test";           // Database name
$config['host'] = "localhost";      // Database host
$config['user'] = "test";           // Database user
$config['pass'] = "123456789";      // Database user password

/* ---------------------------------
    Do not alter below this line
----------------------------------*/

$dbh = new PDO('mysql:dbname=' . $config['name'] . ';host=' . $config['host'] . ';charset=utf8', $config['user'], $config['pass']);

$langid = 1;

foreach( $lang as $key => $text ){

    $query = $dbh->prepare("INSERT INTO translations (`lang`, `key`, `text`) VALUES (?, ?, ?)");
    if(!$query->execute(array($langid, $key, $text))){
        echo "FAIL: " . $key . "\n\n";
    }

}

?>