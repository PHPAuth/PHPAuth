<?php

$pathparts = explode("/", $_SERVER['PHP_SELF']);
$filename = $pathparts[count($pathparts) -1];
$res = "";

// Scan directory for language files.
foreach(scandir(__DIR__) as $file)
{
    if(is_file($file) && $file != $filename)
    {
        $files[] = explode(".", $file)[0];
    }
}

if(isset($_POST['db_host']))
{
    $dbh = new PDO('mysql:dbname=' . $_POST['db_name'] . ';host=' . $_POST['db_host'] . ';charset=utf8', $_POST['db_user'], $_POST['db_pass']);

    foreach($_POST['languages'] as $lang_code)
    {
        $lang = array();
        require $lang_code . ".php";
        
        $query = $dbh->prepare("INSERT INTO languages (`lang`) VALUES (?)");
        
        if(true) if($query->execute(array($lang_code)))
        {
            $res .= "\n" . "SUCCESS: " . $lang_code . "\n";
            
            $lang_codeid = $dbh->lastInsertId();
            
            foreach($lang as $key => $text)
            {
                $query = $dbh->prepare("INSERT INTO translations (`lang`, `key`, `text`) VALUES (?, ?, ?)");
                
                if($query->execute(array($lang_codeid, $key, $text)))
                {
                    $res .= "SUCCESS: " . $key . "\n";
                } else {
                    $res .= "FAIL: " . $key . "\n";
                }
            }
        } else {
            $res .= "\n" . "FAIL: " . $lang_code . "\n";
        }
    }
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

    <title>PHPAuth - Language importer tool</title>

</head>
<body data-spy="scroll" data-target="#navbarSupportedContent" data-offset="50">

    <div class="container pt-5 text-center">
        <h1>Language importer tool</h1>
        <p>A tool to help you populate your translations database for PHPAuth</p>
    </div>

    <div class="container pt-5">

        <h3>How to use</h3>
        <ol>
            <li>Make sure the tables have been created in the database, see <code>database_*.sql</code> for reference.</li>
            <li>Enter the connection details for your SQL-server below.</li>
            <li>Chooose wich language files to import, all language-files must be located in the same folder as this script to be shown below.</li>
            <li>Run import, this may take some time depending on your hardware.</li>
            <li>Review the result and make sure there were no errors.</li>
            <li class="text-danger font-weight-bold">At last, make sure you remove this script from the server when you're done.</li>
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

                    $length = count($files);
                    $split = round($length / 5);
                    $splitcount = 1;

                    for($i = 0; $i < $length; $i++)
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
                <div class="col-sm-10">
                    <pre class="pre-scrollable bg-light pl-4 pr-4">
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
