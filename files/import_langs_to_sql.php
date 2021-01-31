<?php
/**
 * User: Karel Wintersky
 * Date: 12.04.2018, time: 13:12
 */

$dbh = new PDO("mysql:host=localhost;dbname=...", "...", "...."); // set your args
$dbh_table = 'phpauth_translation_dictionary'; // import table target

/**
 * $langs array format
 * Currently there are 31
 *
 * a b c d e f g
 * h i j k l m n
 * o p q
 * r s t
 * u v w
 * x y z
 *
 */
$langs = [
    'ar_TN', 'bs_BA', 'cs_CZ', 'da_DK', 'de_DE', 'en_GB', 'es_MX', 'fa_IR', 'fr_FR', 'gr_GR',
    'hu_HU', 'id_ID', 'it_IT', 'nl_BE', 'nl_NL',
    'no_NB', 'pl_PL', 'ps_AF', 'pt_BR',
    'ro_RO', 'ru_RU', 'se_SE', 'sk_SK', 'sl_SI', 'sr_RS', 'th_TH', 'tr_TR',
    'uk_UA', 'vi_VN',
    'zh_CN', 'zh_TW'
];


foreach ($langs as $l) {
    $lang = include __DIR__ . "../languages/{$l}.php";

    echo('language = ' . $l);

    $query = "
INSERT INTO `{$dbh_table}` (`translation_key`, `{$l}`) VALUES (:translation_key, :message)
ON DUPLICATE KEY
UPDATE `{$l}` = :message
";

    $sth = $dbh->prepare($query);

    echo '[ ';

    foreach ($lang as $message_id => $message_text) {

        $sth->execute([
            'translation_key' => "{$message_id}",
            'message'         => "{$message_text}"
        ]);

        echo "{$message_id} | ";
    }

    echo " ] ", PHP_EOL;

    unset($lang);
}

