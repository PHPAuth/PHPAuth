<?php

class Config
{
    /*
    * Database settings
    */

    private $dbhost = "localhost";
    private $dbuser = "root";
    private $dbpass = "";
    private $dbname = "frontend";

    /*
    * Site specific settings
    */

    // The website's name
    private $sitename = "Auth";

    // The base URL of the Authentication portal, without trailing slash
    private $authurl = "http://example.com";

    // The "from" email address for all system emails
    private $fromemail = "no-reply@example.com";

    // Session cookie name
    private $cookiename = "authID";

    // Session cookie path
    private $cookiepath = "/";

    // Session cookie domain
    private $cookiedomain = "";

    // Session cookie secure parameter
    private $cookiesecure = false;

    // Session cookie HttpOnly parameter
    private $cookiehttp = true;

    // Site key for cookie verification
    private $sitekey = 'fghuior.)/%dgdhjUyhdbv7867HVHG7777ghg';

    // Remember me database and cookie session duration
    private $duration_remember = "+1 month";

    // Non remember me database session duration
    private $duration_non_remember = "+30 minutes";

    // Bcrypt hashing cost
    private $bcrypt_cost = 10;

    /*
    * Database table settings
    */

    private $table_attempts = 'attempts';
    private $table_log = 'log';
    private $table_requests = 'requests';
    private $table_sessions = 'sessions';
    private $table_users = 'users';

    /*
    * Get / Set functions
    */

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }
}