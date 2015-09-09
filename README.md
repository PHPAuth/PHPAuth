PHPAuth
=======

What is it
---------------

PHPAuth is a secure user authentication class for PHP websites, using a powerful password hashing system and attack blocking to keep your website and users secure.

Features
---------------
* Authentication by email and password combination
* Uses [bcrypt](http://en.wikipedia.org/wiki/Bcrypt) to hash passwords, a secure algorithm that uses an expensive key setup phase
* Uses an individual 128 bit salt for each user, pulled from /dev/urandom, making rainbow tables useless
* Uses PHP's [PDO](http://php.net/manual/en/book.pdo.php) database interface and uses prepared statements meaning an efficient system, resilient against SQL injection
* Blocks attackers by IP for any defined time after any amount of failed actions on the portal
* No plain text passwords are sent or stored by the system
* Integrates easily into most existing websites, and can be a great starting point for new projects
* Easy configuration of multiple system parameters
* Allows sending emails via SMTP or sendmail
* Blocks disposable email addresses from registration

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
* PHP 5.4
* MySQL / MariaDB database
* SMTP server / sendmail
* PHP Mcrypt

Configuration
---------------

The database table `config` contains multiple parameters allowing you to configure certain functions of the class.

* `site_name` : the name of the website to display in the activation and password reset emails
* `site_url`: the URL of the Auth root, where you installed the system, without the trailing slash, used for emails.
* `site_email` : the email address from which to send activation and password reset emails
* `cookie_name` : the name of the cookie that contains session information, do not change unless necessary
* `cookie_path` : the path of the session cookie, do not change unless necessary
* `cookie_domain` : the domain of the session cookie, do not change unless necessary
* `cookie_secure` : the HTTPS only setting of the session cookie, do not change unless necessary
* `cookie_http` : the HTTP only protocol setting of the session cookie, do not change unless necessary
* `site_key` : a random string that you should modify used to validate cookies to ensure they are not tampered with
* `cookie_remember` : the time that a user will remain logged in for when ticking "remember me" on login. Must respect PHP's [strtotime](http://php.net/manual/en/function.strtotime.php) format.
* `cookie_forget` : the time a user will remain logged in when not ticking "remember me" on login.  Must respect PHP's [strtotime](http://php.net/manual/en/function.strtotime.php) format.
* `bcrypt_cost` : the algorithmic cost of the bcrypt hashing function, can be changed based on hardware capabilities
* `site_timezone` : the timezone for correct datetime values
* `site_activation_page` : the activation page name appended to the `site_url` in the activation email
* `site_password_reset_page` : the password reset page name appended to the `site_url` in the password reset email
* `smtp` : `0` to use sendmail for emails, `1` to use SMTP
* `smtp_host` : hostname of the SMTP server
* `smtp_auth` : `0` if the SMTP server doesn't require authentication, `1` if authentication is required
* `smtp_username` : the username for the SMTP server
* `smtp_password` : the password for the SMTP server
* `smtp_port` : the port for the SMTP server
* `smtp_security` : `NULL` for no encryption, `tls` for TLS encryption, `ssl` for SSL encryption

The rest of the parameters generally do not need changing.

How to secure a page
---------------

Making a page accessible only to authenticated users is quick and easy, requiring only a few lines of code at the top of the page:

```php
<?php

include("languages/en_GB.php");
include("config.class.php");
include("auth.class.php");

$dbh = new PDO("mysql:host=localhost;dbname=phpauth", "username", "password");

$config = new PHPAuth\Config($dbh);
$auth   = new PHPAuth\Auth($dbh, $config, $lang);

if (!$auth->isLogged() {
    header('HTTP/1.0 403 Forbidden');
    echo "Forbidden";

    exit();
}

?>
```

Documentation
---------------

All class methods are documented in [the Wiki](https://github.com/PHPAuth/PHPAuth/wiki/Class-Methods)  
System error codes are listed and explained [here](https://github.com/PHPAuth/PHPAuth/wiki/System-error-codes)

License
---------------

Copyright (C) 2014 - 2015 PHPAuth

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program. If not, see http://www.gnu.org/licenses/

Contributing
---------------

Anyone can contribute to improve or fix PHPAuth, to do so you can either report an issue (a bug, an idea...) or fork the repository, perform modifications to your fork then request a merge.

Credits
---------------

* [password_compat](https://github.com/ircmaxell/password_compat) - @ircmaxell
* [disposable](https://github.com/lavab/disposable) - @lavab
* [PHPMailer](https://github.com/PHPMailer/PHPMailer) - @PHPMailer
