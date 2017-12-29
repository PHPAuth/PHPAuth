<?php

$config = array();
$config['name'] = "test";           // Database name
$config['host'] = "localhost";      // Database host
$config['user'] = "test";           // Database user
$config['pass'] = "123456789";      // Database user password

$file = "domains.json";

$dbh = new PDO('mysql:dbname=' . $config['name'] . ';host=' . $config['host'] . ';charset=utf8', $config['user'], $config['pass']);

$emails = json_decode(file_get_contents($file));

if(count($emails) < 1){
    echo "\n" . "FAIL: Error occurred #01 \n";
    exit();
}

foreach($emails as $email)
{
    if (filter_var("test@" . $email, FILTER_VALIDATE_EMAIL)) {
        $query = $dbh->prepare("INSERT INTO emailBanlist (`domain`) VALUES (?)");
        
        if($query->execute(array($email)))
        {
            echo "\n" . "SUCCESS: " . $email . "\n";
        } else {
            echo "\n" . "FAIL: " . $email . "\n";
        }
    } else {
        echo "\n" . "FAIL: " . $email . "\n";
    }
}

?>
