<?php

$dsn = "mysql:host=127.0.0.1;dbname=phpauth_test_database";
$username = "phpauth_test_user";
$password = "";

$pdo = new PDO($dsn, $username, $password); // set your args
$table = 'phpauth_translation_dictionary'; // import table target

$lang = 'en_GB';

$sth = $pdo->query("SELECT translation_key, {$lang} AS lang FROM {$table} ORDER BY translation_group, translation_key");
$data = $sth->fetchAll(PDO::FETCH_ASSOC);

$echo = '<?php ' . PHP_EOL . PHP_EOL . '$lang = []; ' . PHP_EOL . PHP_EOL;

foreach ($data as $key => $value) {
    $echo .= '$' . "lang['{$value['translation_key']}'] = '" . htmlspecialchars($value['lang'], ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE) . "'; " . PHP_EOL;
}

$echo .= PHP_EOL . PHP_EOL. 'return $lang;' . PHP_EOL . PHP_EOL;

echo $echo;

