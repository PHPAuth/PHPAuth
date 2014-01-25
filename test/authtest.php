<?php
/** PHPAuth
 * @author PHPAuth
 * @version 2.1
 * @website http://phpauth.cuonic.com/
 * @copyright 2014 - 2014 - PHPAuth
 * @license LICENSE.md
 * 
 *  Copyright (C) 2014 - 2014  PHPAuth
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 * 
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 * 
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>
 * 
 */
// This script allows you to test main functionality of Auth class. Can only be run once, after that you need to remove the user from database.

$config = array();

// Database Host :
$config['db']['host'] = "localhost";
// Database Username :
$config['db']['user'] = "username";
// Database Password :
$config['db']['pass'] = "password";
// Database Name :
$config['db']['name'] = "phpauth";

// Include the Auth class
include ("../Auth.php");

// Initiate database connection
try {
    $dbh = new PDO('mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['name'],
        $config['db']['user'], $config['db']['pass']);
}
catch (exception $e) {
    echo "ERROR : STAGE 1 : " . $e->getMessage();
    exit();
}

// Create class instance with database connection
$auth = new cuonic\PHPAuth2\Auth($dbh);
// Create random password
$password = md5(microtime() . rand(0, 999));
// Hash the password twice with sha1 to imitate client-side hashing with javascript
$passwordhash = hash("sha1", hash("sha1", $password));
// Run the registration function to create a user called testuser with the random password
$register = $auth->register("testuser@test.com", "testuser", $passwordhash);
// Verify return code
if ($register['code'] != 4) {
    echo "ERROR : STAGE 2 : ";
    var_dump($register);
    exit();
}
// Account is now registered. Fetch user id
$user = $auth->getUserData("testuser");
// Verify return data
if ($user == false) {
    echo "ERROR : STAGE 3";
    exit();
}
// Fetch activation key from database to imitate user activation via email link
$query = $dbh->prepare("SELECT activekey FROM activations WHERE uid = ?");
$query->execute(array($user['uid']));
// Verify row count
if ($query->rowCount() == 0) {
    echo "ERROR : STAGE 4 : ";
    exit();
}
// Fetch data
$row = $query->fetch(PDO::FETCH_ASSOC);
// Activate the user
$activate = $auth->activate($row['activekey']);
// Verify return code
if ($activate['code'] != 5) {
    echo "ERROR : STAGE 5 : ";
    var_dump($activate);
    exit();
}
// Account is now activated. Attempt login
$login = $auth->login("testuser", $passwordhash);
// Verify return code
if ($login['code'] != 4) {
    echo "ERROR : STAGE 6 : ";
    var_dump($login);
    exit();
}
// Account now logged in. Check if session hash corresponds with username
$getusername = $auth->getUsername($login['session_hash']);
// Verify returned data
if (!$getusername) {
    echo "ERROR : STAGE 7";
    exit();
}
if ($getusername != "testuser") {
    echo "ERROR : STAGE 8 : " . $getusername;
    exit();
}
// Session is linked to correct username. Check if session is valid
if (!$auth->checkSession($login['session_hash'])) {
    echo "ERROR : STAGE 9";
    exit();
}
// Session is valid. Check if session hash corresponds with UID
$sessionuid = $auth->sessionUID($login['session_hash']);
// Verify session hash UID with previously retrieved UID
if ($user['uid'] != $sessionuid) {
    echo "ERROR : STAGE 10 : " . $sessionuid;
    exit();
}
// Session UID matches user UID. Create new random password
$password2 = md5(microtime() . rand(0, 999));
// Hash the password twice with sha1 to imitate client-side hashing with javascript
$password2hash = hash("sha1", hash("sha1", $password2));
// Change the password
$changepassword = $auth->changePassword($user['uid'], $passwordhash, $password2hash);
// Verify return code
if ($changepassword['code'] != 5) {
    echo "ERROR : STAGE 11 : ";
    var_dump($changepassword);
    exit();
}
// Account password changed. Fetch user email
$getemail = $auth->getEmail($user['uid']);
// Verify returned data
if (!$getemail) {
    echo "ERROR : STAGE 12";
    exit();
}
if ($getemail != "testuser@test.com") {
    echo "ERROR : STAGE 13 : " . $getemail;
    exit();
}
// Email matches registration email. Change account email address
$changeemail = $auth->changeEmail($user['uid'], "testuser@example.com", $password2hash);
// Verify return code
if ($changeemail['code'] != 5) {
    echo "ERROR : STAGE 14 : ";
    var_dump($changeemail);
    exit();
}
// Email has been changed. Verify email change.
$getemail = $auth->getEmail($user['uid']);
// Verify returned data
if (!$getemail) {
    echo "ERROR : STAGE 15";
    exit();
}
if ($getemail != "testuser@example.com") {
    echo "ERROR : STAGE 16 : " . $getemail;
    exit();
}
// Email change confirmed. Log account out
if (!$auth->logout($login['session_hash'])) {
    echo "ERROR : STAGE 17";
    exit();
}

?>