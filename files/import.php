<?php

/**
 * Inserts a domain into the database
 * @param $dbh
 * @param array $domains
 * @return array $return
 */
function importDomains($dbh, $domains)
{
    $return['error'] = true;
    $return['message'] = "";

    if(count($domains) < 1)
    {
        $return['message'] = "Domains array are empty.";
        return $return;
    }
    
    foreach($domains as $domain)
    {
        if(!filter_var("test@" . $domain, FILTER_VALIDATE_EMAIL))
        {
            $return['message'] .= "Not a valid domain: " . $domain . " - Skipping \n";
        }

        $query = $dbh->prepare("INSERT INTO phpauth_emails_banned (`domain`) VALUES (?)");
                
        if(!$query->execute(array($domain)))
        {
            $return['message'] .= "Failed to import domain: " . $domain . "\n";
        }
    }

    $return['error'] = false;

    return $return;
}

/**
 * Veryfies the file path entered are not pointing to any parent foler
 * @param $file_path
 * @return array $return
 */
function validateFilePath($file_path)
{
    $return['error'] = true;

    $filePath = preg_match("(\.+\/|^\/)", $file_path, $matches);

    if(count($matches) > 0)
    {
        $return['message'] = "Not a valid file path";
        return $return;
    }

    $return['file_path'] = $file_path;
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

    $validateFilePath = validateFilePath($_POST['file_path']);

    if($validateFilePath['error'] == true)
    {
        $res .= $validateFilePath['message'] . "\n";
    } else {
        $domains = json_decode(file_get_contents($validateFilePath['file_path']));

        $importDomains = importDomains($dbh, $domains);

        $res .= $importDomains['message'];
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

    <title>PHPAuth - Email banlist import tool</title>

</head>
<body data-spy="scroll" data-target="#navbarSupportedContent" data-offset="50">

    <div class="container pt-5 text-center">
        <h1>Email banlist import tool</h1>
        <p>A tool to help you populate your banlist database for <a href="https://github.com/PHPAuth/PHPAuth">PHPAuth</a></p>
    </div>

    <div class="container pt-5">

        <h3>How to use</h3>
        <ol>
            <li>Make sure the table have been created in the database, refer to the respective <code>database_*.sql</code>-file for reference.</li>
            <li>Enter the connection details for your SQL-server below.</li>
            <li>Enter the filename and path below. The file path is relative to this script and can not point at a parent directory due to security reasons.</li>
            <li>Run import, this may take some time depending on your hardware.</li>
            <li>Review the result and make sure there were no errors.</li>
            <li class="text-danger font-weight-bold">Make sure you remove this script from the server when you're done.</li>
        </ol>

    </div>

    <div class="container pt-5">

        <h3>Import banlist</h3>
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

            <div class="form-group row">
                <label for="file_path" class="col-sm-2 col-form-label">File path</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="file_path" name="file_path" placeholder="Enter path to file" value="domains.json">
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
