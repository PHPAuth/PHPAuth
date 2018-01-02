<?php

/**
 * Returns an array of language files
 * @param $dir
 * @return array $files
 */
function getLanguageFiles($dir)
{
    $pathparts = explode("/", $_SERVER['PHP_SELF']);
    $filename = $pathparts[count($pathparts) -1];

    foreach(scandir($dir) as $file)
    {
        if(is_file($file) && $file != $filename)
        {
            $files[] = explode(".", $file)[0];
        }
    }

    if(count($files) > 0){
        return $files;
    }

    return false;
}

/**
 * Inserts a language identifier into the database
 * @param $dbh
 * @param $lang_code
 * @return array $return
 */
function insertLanguage($dbh, $lang_code)
{
    $return['error'] = true;

    $query = $dbh->prepare("INSERT INTO languages (`lang`) VALUES (?)");
        
    if(!$query->execute(array($lang_code)))
    {
        $return['message'] = "Failed to import language: " . $lang_code;
        return $return;
    }

    $return['error'] = false;
    $return['id'] = $dbh->lastInsertId();

    return $return;
}

/**
 * Inserts a translation text into the database
 * @param $dbh
 * @param $lang_id
 * @param $key
 * @param $text
 * @return array $return
 */
function insertTranslation($dbh, $lang_id, $key, $text)
{
    $return['error'] = true;

    $query = $dbh->prepare("INSERT INTO translations (`lang`, `key`, `text`) VALUES (?, ?, ?)");
                
    if(!$query->execute(array($lang_id, $key, $text)))
    {
        $return['message'] = "Failed to import translation key: " . $key;
        return $return;
    }

    $return['error'] = false;

    return $return;
}

/**
 * Run import if form has been submitted
 */
if(isset($_POST['db_host']))
{
    $dbh = new PDO('mysql:dbname=' . $_POST['db_name'] . ';host=' . $_POST['db_host'] . ';charset=utf8', $_POST['db_user'], $_POST['db_pass']);

    $res = "\n";

    foreach($_POST['languages'] as $lang_code)
    {
        $lang = array();
        require $lang_code . ".php";

        $res .= "Importing " . $lang_code . "\n";

        $insertLanguage = insertLanguage($dbh, $lang_code);

        if($insertLanguage['error'] == true){
            $res .= $insertLanguage['message'] . "\n";
            break;
        }

        foreach($lang as $key => $text)
        {
            $res .= "  " . $key . "\n";

            $insertTranslation = insertTranslation($dbh, $insertLanguage['id'], $key, $text);

            if($insertTranslation['error'] == true){
                $res .= $insertTranslation['message'] . "\n";
            }
        }

    }

    $res .= "\nImport finished \n";

}

?>

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css" integrity="sha384-Zug+QiDoJOrZ5t4lssLdxGhVrurbmBWopoEl+M6BdEfwnCJZtKxi1KgxUyJq13dy" crossorigin="anonymous">

    <title>PHPAuth - Language import tool</title>

</head>
<body data-spy="scroll" data-target="#navbarSupportedContent" data-offset="50">

    <div class="container pt-5 text-center">
        <h1>Language import tool</h1>
        <p>A tool to help you populate your translations database for <a href="https://github.com/PHPAuth/PHPAuth">PHPAuth</a></p>
    </div>

    <div class="container pt-5">

        <h3>How to use</h3>
        <ol>
            <li>Make sure the tables have been created in the database, refer to the respective <code>database_*.sql</code>-file for reference.</li>
            <li>Enter the connection details for your SQL-server below.</li>
            <li>Chooose wich language files to import, all language-files must be located in the same folder as this script to be shown below.</li>
            <li>Run import, this may take some time depending on your hardware.</li>
            <li>Review the result and make sure there were no errors.</li>
            <li class="text-danger font-weight-bold">Make sure you remove this script from the server when you're done.</li>
        <ol>

    </div>

    <div class="container pt-5">

        <h3>Import translations</h3>
        <form action="" method="post">

            <div class="form-group row pt-2">
                <label for="db_host" class="col-sm-2 col-form-label">Database host</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="db_host" name="db_host" placeholder="Enter database hostname or ip-address">
                </div>
            </div>

            <div class="form-group row">
                <label for="db_name" class="col-sm-2 col-form-label">Database name</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="db_name" name="db_name" placeholder="Enter database name">
                </div>
            </div>

            <div class="form-group row">
                <label for="db_user" class="col-sm-2 col-form-label">Database user</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="db_user" name="db_user" placeholder="Enter database username">
                </div>
            </div>

            <div class="form-group row">
                <label for="db_pass" class="col-sm-2 col-form-label">Database password</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" id="db_pass" name="db_pass" placeholder="Enter database user password">
                </div>
            </div>

            <div class="row pb-3">
                <div class="col-sm-2">Language files to import</div>

                
                <div class="col-sm-2">

                <?php

                    $checkbox = '
                    <div class="form-check">
                        <input class="form-check-input" checked type="checkbox" name="languages[]" value="%s" id="%s">
                        <label class="form-check-label" for="%s">
                            %s
                        </label>
                    </div>
                    ';

                    $newcolumn = '</div><div class="col-sm-2">';

                    $files = getLanguageFiles(__DIR__);
                    $length = count($files);
                    $split = round($length / 5);

                    for($i = 0, $splitcount = 1; $i < $length; $i++)
                    {
                        if($i == $split * $splitcount)
                        {
                            echo $newcolumn;
                            $splitcount++;
                        }

                        echo sprintf($checkbox, $files[$i], $files[$i], $files[$i], $files[$i]);
                    }

                ?>

                </div>

            </div>

            <div class="form-group row">
                <div class="col-sm-2">&nbsp;</div> <!-- Align button with other input fields -->
                <div class="col-sm-10">
                    <button type="submit" class="btn btn-success">Start import</button>
                </div>
            </div>

        </form>

    </div>

    <?php

        $resultfield = '
        <div class="container pt-5 pb-5">
            <div class="row">
                <div class="col-sm-2">Result</div>
                <div class="col-sm-10 bg-light">
                    <pre class="pre-scrollable">
                        %s
                    </pre>
                </div>
            </div>
        </div>
        ';

        if(isset($_POST['db_host']))
        {
            echo sprintf($resultfield, $res);
        }

    ?>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/usm/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/js/bootstrap.min.js" integrity="sha384-a5N7Y/aK3qNeh15eJKGWxsqtnX/wWdSZSKp+81YjTmS15nvnvxKHuzaWwXHDli+4" crossorigin="anonymous"></script>
</body>
</html>
