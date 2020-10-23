![PHPAuth banner](https://github.com/PHPAUth/PHPAUth/blob/master/banner_small.png?raw=true)

<!-- ALL-CONTRIBUTORS-BADGE:START - Do not remove or modify this section -->
[![All Contributors](https://img.shields.io/badge/all_contributors-6-orange.svg?style=flat-square)](#contributors-)
<!-- ALL-CONTRIBUTORS-BADGE:END -->
[![Build Status](https://api.travis-ci.org/PHPAuth/PHPAuth.png)](https://travis-ci.org/PHPAuth/PHPAuth)
![PHP version from Travis config](https://img.shields.io/travis/php-v/phpauth/phpauth/master)
[![Discord server](https://img.shields.io/discord/761354508860653619?logo=discord)](https://discord.gg/ewGcMN4)
![Lines of code](https://img.shields.io/tokei/lines/github/PHPAuth/PHPAuth)
![GitHub code size in bytes](https://img.shields.io/github/languages/code-size/PHPAuth/PHPAuth)
![GitHub All Releases](https://img.shields.io/github/downloads/PHPAuth/PHPAuth/total)
![GitHub issues](https://img.shields.io/github/issues-raw/PHPAuth/PHPAuth)
![GitHub closed issues](https://img.shields.io/github/issues-closed/PHPAuth/PHPAuth)
![GitHub pull requests](https://img.shields.io/github/issues-pr/PHPAuth/PHPAuth)
![GitHub closed pull requests](https://img.shields.io/github/issues-pr-closed/PHPAuth/PHPAuth)
![GitHub forks](https://img.shields.io/github/forks/PHPAuth/PHPAuth?label=Forks&style=plastic)
![GitHub Repo stars](https://img.shields.io/github/stars/PHPAuth/PHPAuth?style=plastic)
![GitHub watchers](https://img.shields.io/github/watchers/PHPAUth/PHPAuth?style=plastic)
![GitHub release (latest by date)](https://img.shields.io/github/v/release/PHPAuth/PHPAuth)
![GitHub contributors](https://img.shields.io/github/contributors/phpauth/phpauth)
![GitHub last commit](https://img.shields.io/github/last-commit/phpauth/phpauth)
[![MIT license](https://img.shields.io/badge/License-MIT-blue.svg)](https://lbesson.mit-license.org/)
[![Open Source? Yes!](https://badgen.net/badge/Open%20Source%20%3F/Yes%21/blue?icon=github)](https://github.com/PHPAuth/PHPAuth)



PHPAuth
=======

Notice! (pr 1/10/2020)
---------------
PHPAuth is under going a complete rewrite to bring the code up to date, the project has been on hold for way to long time now and I decided to work on it again making sure EVERYONE can use it and not just advanded programmers. My goal is to make a Auth framework that is secure, extendable and useable for everyone. It will take some time but we have a good amount of users already using this code which are happily to help out.

#### Goals:
- Bring code up to newest PHP version with min. of v7.1 to v7.4 (If new version comes out while rewriting the code will be pushed up to that version also)
- Making the code even more seure to use by adding things like one time keys (OTP, 2FA etc)
- Make sure that the code can be used by everyone, also beginners.
- Write much better documentation.
- Make database queries faster.
- Uptimize the code.
- Bring down issue count.
- Respond faster to issue and PRs.
- And much more!



What is it
---------------

PHPAuth is a secure user authentication class for PHP websites, using a powerful password hashing system and attack blocking to keep your website and users secure.

PHPAuth is work in progress, and not meant for people that don't know how to program, its meant for people that know what they are doing. We cannot help everyone because they don't understand this class.

IT'S NOT FOR BEGINNERS!

Features
---------------
* Authentication by email and password combination
* Uses [bcrypt](http://en.wikipedia.org/wiki/Bcrypt) to hash passwords, a secure algorithm that uses an expensive key setup phase
* Uses an individual 128 bit salt for each user, pulled from /dev/urandom, making rainbow tables useless
* Uses PHP's [PDO](http://php.net/manual/en/book.pdo.php) database interface and uses prepared statements meaning an efficient system, resilient against SQL injection
* Blocks (or verifies) attackers by IP for any defined time after any amount of failed actions on the portal
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
* PHP 7.0+
* MySQL / MariaDB database or PostGreSQL database

Composer Support
---------------
PHPAuth can now be installed with the following command:

`composer require phpauth/phpauth:dev-master`

Then: `require 'vendor/autoload.php';`

Configuration
---------------

The database table `config` contains multiple parameters allowing you to configure certain functions of the class.

* `site_name`   : the name of the website to display in the activation and password reset emails
* `site_url`    : the URL of the Auth root, where you installed the system, without the trailing slash, used for emails.
* `site_email`  : the email address from which to send activation and password reset emails
* `site_key`    : a random string that you should modify used to validate cookies to ensure they are not tampered with
* `site_timezone` : the timezone for correct DateTime values
* `site_activation_page` : the activation page name appended to the `site_url` in the activation email
* `site_activation_page_append_code` : `1` to append /key to the `site_url` in the activation email to simplier UX, a RESTful API should be implemented for this option
* `site_password_reset_page` : the password reset page name appended to the `site_url` in the password reset email
* `site_password_reset_page_append_code` : `1` to append /key to the `site_url` in the reset email to simplier UX, a RESTful API should be implemented for this option
* `cookie_name` : the name of the cookie that contains session information, do not change unless necessary
* `cookie_path` : the path of the session cookie, do not change unless necessary
* `cookie_domain` : the domain of the session cookie, do not change unless necessary
* `cookie_secure` : the HTTPS-only setting of the session cookie, do not change unless necessary
* `cookie_http` : the HTTP only protocol setting of the session cookie, do not change unless necessary
* `cookie_remember` : the time that a user will remain logged in for when ticking "remember me" on login. Must respect PHP's [strtotime](http://php.net/manual/en/function.strtotime.php) format.
* `cookie_forget` : the time a user will remain logged in when not ticking "remember me" on login.  Must respect PHP's [strtotime](http://php.net/manual/en/function.strtotime.php) format.
* `cookie_renew` : the maximum time difference between session expiration and last page load before allowing the session to be renewed. Must respect PHP`s [strtotime](http://php.net/manual/en/function.strtotime.php) format.
* `allow_concurrent_sessions` : Allow a user to have multiple active sessions (boolean). If false (default), logging in will end any existing sessions.
* `bcrypt_cost` : the algorithmic cost of the bcrypt hashing function, can be changed based on hardware capabilities
* `smtp` : `0` to use sendmail for emails, `1` to use SMTP
* `smtp_debug` : `0` to disable SMTP debugging, `1` to enable SMTP debugging, useful when you are having email/smtp issues
* `smtp_host` : hostname of the SMTP server
* `smtp_auth` : `0` if the SMTP server doesn't require authentication, `1` if authentication is required
* `smtp_username` : the username for the SMTP server
* `smtp_password` : the password for the SMTP server
* `smtp_port` : the port for the SMTP server
* `smtp_security` : `NULL` for no encryption, `tls` for TLS encryption, `ssl` for SSL encryption
* `verify_password_min_length` : minimum password length, default is `3`
* `verify_email_min_length` : minimum EMail length, default is `5`
* `verify_email_max_length` : maximum EMail length, default is `100`
* `verify_email_use_banlist` : use banlist while checking allowed EMails (see `/files/domains.json`), default is `1` (`true`)
* `attack_mitigation_time` : time used for rolling attempts timeout, default is `+30 minutes`. Must respect PHP's [strtotime](http://php.net/manual/en/function.strtotime.php) format.
* `attempts_before_verify` : maximum amount of attempts to be made within `attack_mitigation_time` before requiring captcha. Default is `5`
* `attempt_before_ban` : maximum amount of attempts to be made within `attack_mitigation_time` before temporally blocking the IP address. Default is `30`
* `password_min_score` : the minimum score given by [zxcvbn](https://github.com/bjeavons/zxcvbn-php) that is allowed. Default is `3`
* `translation_source`: source of translation, possible values: 'sql' (data from <table_translations> will be used), 'php' (default, translations will be loaded from languages/*.php), 'ini' (will be used languages/*.ini files)
* `table_translations` : name of the table with translation for all messages
* `table_attempts` : name of the table with all attempts (default is 'phpauth_attempts')
* `table_requests` : name of the table with all requests (default is 'phpauth_requests')
* `table_sessions` : name of the table with all sessions (default is 'phpauth_sessions')
* `table_users` : name of the table with all users (default is 'phpauth_users')
* `table_emails_banned` : name of the table with all banned email domains (default is 'phpauth_emails_banned')
* `recaptcha_enabled`: 1 for Google reCaptcha enabled, 0 - disabled (default)
* `recaptcha_site_key`: string, contains public reCaptcha key (for javascripts)
* `recaptcha_secret_key`: string, contains secret reCaptcha key

The rest of the parameters generally do not need changing.

CAPTCHA Implementation
---------------

If `isBlocked()` returns `verify`, then a CAPTCHA code should be displayed.
The method `checkCaptcha($captcha)` is called to verify a CAPTCHA code. By default, this method returns `true` but should be overridden to verify a CAPTCHA.

For example, if you are using Google's ReCaptcha NoCaptcha, use the following code:

```php
    private function checkCaptcha($captcha)
    {
 try {

        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = ['secret'   => 'your_secret_here',
            'response' => $captcha,
            'remoteip' => $this->getIp()];

        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            ]
        ];

        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return json_decode($result)->success;
    }
    catch (\Exception $e) {
        return false;
    }
}
```

If a CAPTCHA is not to be used, please ensure to set `attempt_before_block` to the same value as `attempts_before_verify`.

Also, `Auth::checkReCaptcha()` method can be called.

How to secure a page
---------------

Making a page accessible only to authenticated users is quick and easy, requiring only a few lines of code at the top of the page:

```php
<?php

include("Config.php");
include("Auth.php");

$dbh = new PDO("mysql:host=localhost;dbname=phpauth", "username", "password");

$config = new PHPAuth\Config($dbh);
$auth   = new PHPAuth\Auth($dbh, $config);

if (!$auth->isLogged()) {
    header('HTTP/1.0 403 Forbidden');
    echo "Forbidden";

    exit();
}

?>
```

or

```php
<?php

require_once 'vendor/autoload.php';

use PHPAuth\Config as PHPAuthConfig;
use PHPAuth\Auth as PHPAuth;

$dbh = new PDO("mysql:host=localhost;dbname=phpauth", "username", "password");

$config = new PHPAuthConfig($dbh);
$auth = new PHPAuth($dbh, $config);

if (!$auth->isLogged()) {
    header('HTTP/1.0 403 Forbidden');
    echo "Forbidden";

    exit();
}

?>
```
**NB:** required package installed via composer: `composer require phpauth/phpauth:dev-master`!!!

Validate user password in front-end
-----------------------------------

PHPAuth evaluates the strength of a password on user registration and manually added Users via `addUser()` function. The minimum score of accepted passwords is controlled via the `password_min_score` config-parameter.

In this example, the front-end is based on html, generated via php. The score is passed as a javascript variable like

```
<?php echo 'let minimum_score =' . $config->password_min_score; ?>
```

A full example can be found in the source: /examples/html-frontend-password-strength-gui-feedback/index.php

**NB:** requires a database with phpauth tables from database_defs

Custom config sources
---------------------

By default, config defined at `phpauth_config` data table.

It is possible to define custom config from other sources: ini-file, other SQL-table or php-array:

```
Config($dbh, $config_type, $config_source, $config_language)
```
* `config_type`:
  * 'sql' (or empty value) - load config from database,
  * 'ini' - config must be declared in INI file (sections can be used for better readability, but will not be parsed)
  * 'array' - config will be loaded from $config_source (type of array)
* `config_source` -
  * for 'sql': name of custom table with configuration
  * for 'ini': path and name of INI file (for example: '$/config/config.ini', '$' means application root)
  * for 'array': it is a array with configuration
* `config_language` - custom language for site as locale value (default is 'en_GB')

Examples:

```
new Config($dbh); // load config from SQL table 'phpauth_config', language is 'en_GB'

new Config($dbh, '', 'my_config'); // load config from SQL table 'my_config', language is 'en_GB'

new Config($dbh, 'ini', '$/config/phpauth.ini'); // configuration will be loaded from INI file, '$' means Application basedir

new Config($dbh, 'array', $CONFIG_ARRAY); // configuration must be defined in $CONFIG_ARRAY value

new Config($dbh, '', '', 'ru_RU'); // load configuration from default SQL table and use ru_RU locale
```



Message languages
---------------------

The language for error and success messages returned by PHPAuth can be configured by passing in one of
the available languages as the third parameter to the Auth constructor. If no language parameter is provided
then the default `en_GB`language is used.

Example: `$auth   = new PHPAuth\Auth($dbh, $config, "fr_FR");`

Available languages:

* `ar-TN`
* `bs-BA`
* `cs_CZ`
* `da_DK`
* `de_DE`
* `en_GB` (Default)
* `es_MX`
* `fa_IR`
* `fr_FR`
* `gr_GR`
* `hu_HU`
* `id_ID`
* `it_IT`
* `nl_BE`
* `nl_NL`
* `no_NB`
* `pl_PL`
* `ps_AF`
* `pt_BR`
* `ro_RO`
* `ru_RU`
* `se_SE`
* `sk_SK`
* `sl_SI`
* `sr_RS`
* `th_TH`
* `tr_TR`
* `uk_UA`
* `vi_VN`
* `zh_CN`
* `zh_TW`

Documentation
---------------

All class methods are documented in [the Wiki](https://github.com/PHPAuth/PHPAuth/wiki/Class-Methods)
System error codes are listed and explained [here](https://github.com/PHPAuth/PHPAuth/wiki/System-error-codes)


Contributing
---------------

Anyone can contribute to improve or fix PHPAuth, to do so you can either report an issue (a bug, an idea...) or fork the repository, perform modifications to your fork then request a merge.

Credits
---------------

* [password_compat](https://github.com/ircmaxell/password_compat) - @ircmaxell
* [disposable](https://github.com/lavab/disposable) - @lavab
* [PHPMailer](https://github.com/PHPMailer/PHPMailer) - @PHPMailer
* [zxcvbn-php](https://github.com/bjeavons/zxcvbn-php) - @bjeavons

## Donation

You can help with a donation, so we can rent servers to test on, we can tip our contributors as thank for their help.

Bitcoin: 1PrXRMb9R8GkSRB8wSJ2MWhF9cc6YXCS8w

## Contributors ✨

Thanks goes to these wonderful people ([emoji key](https://allcontributors.org/docs/en/emoji-key)):

<!-- ALL-CONTRIBUTORS-LIST:START - Do not remove or modify this section -->
<!-- prettier-ignore-start -->
<!-- markdownlint-disable -->
<table>
  <tr>
    <td align="center"><a href="https://hemk.es/"><img src="https://avatars2.githubusercontent.com/u/15839724?v=4" width="100px;" alt=""/><br /><sub><b>Nico</b></sub></a><br /><a href="https://github.com/PHPAuth/PHPAuth/commits?author=turbopixel" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/hajro92"><img src="https://avatars0.githubusercontent.com/u/15570002?v=4" width="100px;" alt=""/><br /><sub><b>Hajrudin</b></sub></a><br /><a href="https://github.com/PHPAuth/PHPAuth/commits?author=hajro92" title="Translation">🌍</a></td>
    <td align="center"><a href="https://github.com/Conver"><img src="https://avatars1.githubusercontent.com/u/6231022?v=4" width="100px;" alt=""/><br /><sub><b>conver</b></sub></a><br /><a href="https://github.com/PHPAuth/PHPAuth/commits?author=conver" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/louis123562"><img src="https://avatars1.githubusercontent.com/u/36068395?v=4" width="100px;" alt=""/><br /><sub><b>louis123562</b></sub></a><br /><a href="https://github.com/PHPAuth/PHPAuth/commits?author=louis123562" title="Documentation">📖</a></td>
    <td align="center"><a href="http://www.ifscore.info"><img src="https://avatars1.githubusercontent.com/u/4574233?v=4" width="100px;" alt=""/><br /><sub><b>ANDRES TELLO</b></sub></a><br /><a href="https://github.com/PHPAuth/PHPAuth/commits?author=Criptos" title="Code">💻</a></td>
    <td align="center"><a href="https://张成林.中国"><img src="https://avatars2.githubusercontent.com/u/30773389?v=4" width="100px;" alt=""/><br /><sub><b>张成林</b></sub></a><br /><a href="https://github.com/PHPAuth/PHPAuth/commits?author=zhangchenglin" title="Code">💻</a></td>
  </tr>
</table>

<!-- markdownlint-enable -->
<!-- prettier-ignore-end -->
<!-- ALL-CONTRIBUTORS-LIST:END -->

This project follows the [all-contributors](https://github.com/all-contributors/all-contributors) specification. Contributions of any kind welcome!
