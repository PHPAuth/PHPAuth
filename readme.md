PHPAuth
=======

[![tip for next commit](https://tip4commit.com/projects/1003.svg)](https://tip4commit.com/github/PHPAuth/PHPAuth)

What is it
---------------

PHPAuth is a secure user authentication class for PHP websites, using a powerful password hashing system and attack blocking to keep your website and users secure.

Features
---------------
* Uses [bcrypt](http://en.wikipedia.org/wiki/Bcrypt) to hash passwords, a secure algorithm that uses an expensive key setup phase
* Uses an individual 128 bit salt for each user, pulled from /dev/urandom, making rainbow tables useless
* Uses PHP's [PDO](http://php.net/manual/en/book.pdo.php) database interface and uses prepared statements meaning an efficient system, resilient against SQL injection
* Logs user actions on  the site for various security reasons
* Blocks attackers by IP for any defined time after any amount of failed actions on the portal
* No plain text passwords are sent or stored by the system
* Integrates easily into most existing websites, and can be a great starting point for new projects
* Easy configuration of multiple system parameters

User actions
---------------
* Login
* Register
* Activate account
* Resend activation email
* Reset password
* Change password
* Change email address
* Delete account
* Logout

Requirements
---------------
PHPAuth requires  PHP 5.3.7 and above, a MySQL database and PHP sendmail needs setting up correctly so that account activation emails get sent correctly.

Configuration
---------------

The configuration file (config.class.php) contains multiple parameters allowing you to configure certain functions of the class.

* `$dbhost` : the database host
* `$dbuser` : the database user
* `$dbpass` : the database user's password
* `$dbname` : the database name
* `$sitename` : the name of the website to display in the activation and password reset emails
* `$authurl`: the URL of the Auth root, where you installed the system, without the trailing slash, also used for emails.
* `$fromemail` : the email address from which to send activation and password reset emails
* `$cookiename` : the name of the cookie that contains session information, do not change unless necessary
* `$cookiepath` : the path of the session cookie, do not change unless necessary
* `$cookiedomain` : the domain of the session cookie, do not change unless necessary
* `$cookiesecure` : the HTTPS only setting of the session cookie, do not change unless necessary
* `$cookiehttp` : the HTTP only protocol setting of the session cookie, do not change unless necessary
* `$sitekey` : a random string that you should modify used to validate cookies to ensure they are not tampered with
* `$duration_remember` : the time that a user will remain logged in for when ticking "remember me" on login. Must respect PHP's [strtotime](http://php.net/manual/en/function.strtotime.php) format.
* `$duration_non_remember` : the time a user will remain logged in when not ticking "remember me" on login.  Must respect PHP's [strtotime](http://php.net/manual/en/function.strtotime.php) format.
* `$bcrypt_cost` : the algorithmic cost of the bcrypt hashing function, can be changed based on hardware capabilities

The rest of the parameters generally do not need changing.

How to secure a page
---------------

Making a page accessible only to authenticated users is quick and easy, requiring only a few lines of code at the top of the page:

```php
<?php
include("config.class.php");
include("auth.class.php");

$config = new Config;
    
$dbh = new PDO("mysql:host={$config->dbhost};dbname={$config->dbname}", $config->dbuser, $config->dbpass);
$auth = new Auth($dbh, $config);
    
if(isset($_COOKIE[$config->cookiename]) && !$auth->checkSession($_COOKIE[$config->cookiename]) {
    header('HTTP/1.0 403 Forbidden');
    echo "Forbidden";
	    
    exit();
}
?>
```

License
---------------

Copyright (C) 2014 - 2014 PHPAuth

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program. If not, see http://www.gnu.org/licenses/

Contributing
---------------

Anyone can contribute to improve or fix PHPAuth, to do so you can either report an issue (a bug, an idea...) or fork the repository, perform modifications to your fork then request a merge.

Credits
---------------

* [password_compat](https://github.com/ircmaxell/password_compat) - ircmaxell