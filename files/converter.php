<?php

$config = array(
    'host' => 'localhost',          // Database name
    'name' => 'test',               // Database host
    'user' => 'test',               // Database user
    'pass' => '123456789',          // Database user password
    'file' => 'domains.json'        // File to read
);

$dbh = new PDO('mysql:dbname=' . $config['name'] . ';host=' . $config['host'] . ';charset=utf8', $config['user'], $config['pass']);

$emails = json_decode(file_get_contents($config['file']));

if(count($emails) < 1)
{
    echo "\n" . "FAIL: Error occurred \n";
    exit();
}

foreach($emails as $email)
{
    if (filter_var("test@" . $email, FILTER_VALIDATE_EMAIL)) {
        $query = $dbh->prepare("INSERT INTO emailBanlist (`domain`) VALUES (?)");
        
        if($query->execute(array($email)))
        {
            echo "SUCCESS: " . $email . "\n";
        } else {
            echo "FAIL: " . $email . "\n";
        }
    } else {
        echo "FAIL: " . $email . "\n";
    }
}

?>
