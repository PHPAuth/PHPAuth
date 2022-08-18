<?php

include __DIR__ . '/../../vendor/autoload.php';

//database-connection-object
$dbh = new PDO('mysql:host=localhost;dbname=database', 'username', 'password');

//creating a config-object is enough at this point

$config = new \PHPAuth\Config($dbh);

// $auth   = new \PHPAuth\Auth($dbh, $config);

?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- js file for password evaluation -->
    <script type="text/javascript" src="zxcvbn.js"></script>
    <title>Title</title>

    <!-- some styles -->
    <style>
        form {
            width: 500px;
            height: 300px;
            -webkit-box-shadow: 0px 0px 16px 1px rgba(0, 0, 0, 0.75);
            -moz-box-shadow: 0px 0px 16px 1px rgba(0, 0, 0, 0.75);
            box-shadow: 0px 0px 16px 1px rgba(0, 0, 0, 0.75);
            text-align: center;
            margin: 0 auto;
            padding: 15px 0;
            position: absolute;
            top: calc(35% - 150px);
            left: calc(50% - 250px);
            background-color: lightgray;
        }

        body {
            background-color: gray;
            font-family: Arial, Helvetica, sans-serif;
        }

        #user-info-text {
            color: red;
        }
    </style>

</head>

<body>
    <!-- login / registration form -->
    <form action="/">
        <input type="password" id="password-field" />
        <br>
        <p id="user-info-text">enter your password</p>
        <br>
        <input type="submit" id="login-button" value="register account / login" disabled />
    </form>

    <!-- after body content -->
    <script>
        //selectors
        let sel_password_field = document.getElementById("password-field");
        let sel_login_button = document.getElementById("login-button");
        let sel_user_info_paragraph = document.getElementById("user-info-text");

        //add event listener for changes of password field
        sel_password_field.addEventListener("keyup", function() {

            //read current password
            let password = sel_password_field.value;

            //evaluate password strength
            let result = zxcvbn(password);

            //set minimum_score via php
            let minimum_score = <?php echo $config->password_min_score; ?>;

            //enable submit button
            if (result.score >= minimum_score) {
                sel_login_button.disabled = false;
                sel_user_info_paragraph.style.color = "green";
            } else {
                sel_login_button.disabled = true;
                sel_user_info_paragraph.style.color = "red";
            }

            //change user info paragraph text
            switch (result.score) {

                case 0:
                    sel_user_info_paragraph.innerHTML = "very unsafe";
                    break;

                case 1:
                    sel_user_info_paragraph.innerHTML = "unsafe";
                    break;

                case 2:
                    sel_user_info_paragraph.innerHTML = "not good enough";
                    break;

                case 3:
                    sel_user_info_paragraph.innerHTML = "good";
                    break;

                case 4:
                    sel_user_info_paragraph.innerHTML = "excellent";
                    break;

                default:
                    sel_user_info_paragraph.innerHTML = "very unsafe";
                    break;
            }

        })
    </script>

</body>

</html>
