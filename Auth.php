<?php

namespace PHPAuth;

use ZxcvbnPhp\Zxcvbn;
use PHPMailer\PHPMailer\PHPMailer;
use ReCaptcha\ReCaptcha;
use \Firebase\JWT\JWT;


/*require_once 'AuthInterface.php';*/

/**
 * Auth class
 * Required PHP 5.6 and above.
 *
 */
class Auth/* implements AuthInterface*/
{
    const HASH_LENGTH = 40;
    const TOKEN_LENGTH = 20;

    /**
     * @var \PDO $dbh
     */
    protected $dbh;

    /**
     * @var \stdClass Config
     */
    public $config;


    /**
     * Public 'is_logged' field
     * @var bool
     */
    public $isAuthenticated = false;

    /**
     * @var null
     */
    protected $currentuser = null;

    /**
     * @var \stdClass $messages_dictionary
     */
    protected $messages_dictionary = [];

    /**
     * @var \stdClass $recaptcha_config
     */
    protected $recaptcha_config = [];

    /**
     * @var string private key used for jtw 
     */
    protected $private = "";

    /**
     * @var string secret public key if RS256 is used
     */
    protected $public = "";

    /**
     * Initiates database connection
     *
     * @param \PDO $dbh
     * @param $config
     */
    public function __construct(\PDO $dbh, Config $config)
    {
        if (version_compare(phpversion(), '5.6.0', '<')) {
            die('PHP 5.6.0 required for PHPAuth engine!');
        }

        $this->dbh = $dbh;
        $this->config = $config;

        $this->recaptcha_config = $this->config->recaptcha;
        $this->messages_dictionary = $this->config->dictionary;

        date_default_timezone_set($this->config->site_timezone);
        $this->isAuthenticated = false;
        $this->private = false;
        $this->public =  false;
    }

    /**
     * Logs a user in
     * @param string $email
     * @param string $password
     * @param int $remember
     * @param string $captcha_response = null
     * @return array $return
     */
    //@todo: => loginUser
    public function login($email, $password, $remember = 0, $captcha_response = null)
    {
        $return['error'] = true;
        $return['password_expired'] = false;

        $block_status = $this->isBlocked();

        if ($block_status == "verify") {
            if ($this->checkCaptcha($captcha_response) == false) {
                $return['message'] = $this->__lang("user_verify_failed");
                return $return;
            }
        }

        if ($block_status == "block") {
            $return['message'] = $this->__lang("user_blocked");
            return $return;
        }


        $status = $this->verifyStatus($email);
        $validateEmail = $this->validateEmail($email);
        $validatePassword = $this->validatePassword($password);

        if ($status['error'] == 1) {
            $return['message'] = $status['message'];

            return $return;
        } elseif ($validateEmail['error'] == 1) {
            $this->addAttempt();
            $return['message'] = $validateEmail['message']; // ?? $this->__lang("account_email_invalid");

            return $return;
        } elseif ($validatePassword['error'] == 1) {
            $this->addAttempt();
            $return['message'] = $validatePassword['message']; // ?? $this->__lang("account_password_invalid");

            return $return;
        } elseif ($remember != 0 && $remember != 1) {
            $this->addAttempt();
            $return['message'] = $this->__lang("remember_me_invalid");      //@todo => login_remember_me_invalid

            return $return;
        }

        $uid = $this->getUID(strtolower($email));

        if (!$uid) {
            $this->addAttempt();
            $return['message'] = $this->__lang("account_not_found");

            return $return;
        }

        $user = $this->getBaseUser($uid);

        if ($user['expiration'] == null) {
            $user['expiration'] = "0000-00-00 00:00:00";
        }

        if ($user['days2expire'] > 0 && time() > strtotime($user['expiration'])) {
            $this->addAttempt();
            $return['message'] = $this->__lang("password_expired");
            $return['password_expired'] = true;

            return $return;
        }

        if (!$this->password_verify_with_rehash($password, $user['password'], $uid)) {
            $this->addAttempt();
            $return['message'] = $this->__lang("email_password_incorrect");

            return $return;
        }

        if ($user['isactive'] != 1) {
            $this->addAttempt();
            $return['message'] = $this->__lang("account_inactive");

            return $return;
        }

        $sessiondata = $this->addSession($user['uid'], $remember);

        if ($sessiondata == false) {
            $return['message'] = $this->__lang("system_error") . " #01";

            return $return;
        }

        $return['error'] = false;
        $return['message'] = $this->__lang("logged_in");

        $return['hash'] = $sessiondata['hash'];
        $return['expire'] = $sessiondata['expire'];

        $return['cookie_name'] = $this->config->cookie_name;

        return $return;
    }

    /**
     * Logs a user in and genereate JTW token
     * @param string $email
     * @param string $password
     * @param int $remember
     * @param string $captcha_response = null
     * @return array with $result 
     */
    //@todo: => loginUser
    public function appLogin($email, $password, $remember = 0, $captcha_response = null, $callback = false, $iss = null, $aud = null)
    {
        $result['error'] = true;
        $auth = $this->login($email, $password, $remember, $captcha_response);
        if ($auth['error']) {
            return $auth;
        }

        if (is_null($iss)) {
            $iss = $this->config->default_jwt_iss;
        }

        if (is_null($aud)) {
            $aud = $this->config->default_jwt_aud;
        }

        $jwt = $this->generateJWT($iss, $aud, $remember, $auth['hash'], $callback);
        if (!$jwt) {
            $result['message'] = $this->__lang("token_not_created");
            return $result;
        }

        $result['error'] = false;
        $result['jwt'] = $jwt;
        return $result;
    }


    /**
     * Creates a new user, adds them to database
     * @param string $email
     * @param string $password
     * @param string $repeatpassword
     * @param array  $params
     * @param string $captcha_response = null
     * @param bool $use_email_activation = null
     * @return array $return
     */
    //@todo: => registerUserAccount
    public function register($email, $password, $repeatpassword, $params = [], $captcha_response = null, $use_email_activation = null)
    {
        $return['error'] = true;
        $block_status = $this->isBlocked();

        if ($block_status == "verify") {
            if ($this->checkCaptcha($captcha_response) == false) {
                $return['message'] = $this->__lang("user_verify_failed");

                return $return;
            }
        }

        if ($block_status == "block") {
            $return['message'] = $this->__lang("user_blocked");

            return $return;
        }

        if ($password !== $repeatpassword) {
            $return['message'] = $this->__lang("password_nomatch");

            return $return;
        }

        // Validate email
        $validateEmail = $this->validateEmail($email);

        if ($validateEmail['error'] == 1) {
            $return['message'] = $validateEmail['message'];

            return $return;
        }
        // passsword not the same as email
        if (strcmp($email, $password) == 0) {
            $return['message'] = $this->__lang('same_as_email');

            return $return;
        }


        // Validate password
        $validatePassword = $this->validatePassword($password);

        if ($validatePassword['error'] == 1) {
            $return['message'] = $validatePassword['message'];

            return $return;
        }

        $zxcvbn = new Zxcvbn();

        if ($zxcvbn->passwordStrength($password)['score'] < intval($this->config->password_min_score)) {
            $return['message'] = $this->__lang('password_weak');

            return $return;
        }

        if ($this->isEmailTaken($email)) {
            $this->addAttempt();
            $return['message'] = $this->__lang("email_taken");

            return $return;
        }

        $addUser = $this->addUser($email, $password, $params, $use_email_activation);

        if ($addUser['error'] != 0) {
            $return['message'] = $addUser['message'];

            return $return;
        }

        $return['error'] = false;
        $return['message'] =
            ($use_email_activation == true
                ? $this->__lang("register_success")
                : $this->__lang('register_success_emailmessage_suppressed'));

        return $return;
    }

    /**
     * Activates a user's account
     * @param string $activate_token
     * @return array $return
     */
    public function activate($activate_token) //@todo: rename to 'activateUserAccount'
    {
        $return['error'] = true;
        $block_status = $this->isBlocked();

        if ($block_status == "block") {
            $return['message'] = $this->__lang("user_blocked");

            return $return;
        }

        if (strlen($activate_token) !== self::TOKEN_LENGTH) {
            $this->addAttempt();
            $return['message'] = $this->__lang("activationkey_invalid");

            return $return;
        }

        // 'user is already activated' will never triggered, because after successful activation token removed from DB
        // NOW is no any way to determine, is this token used for activation or not?

        $request_result = $this->getRequest($activate_token, "activation");


        if ($request_result['error']) {
            $return['message'] = $request_result['message'];

            return $return;
        }


        if ($this->getBaseUser($request_result['uid'])['isactive'] == 1) {
            $this->addAttempt();
            $this->deleteRequest($request_result['id']);
            $return['message'] = $this->__lang("system_error") . " #02"; // user activated, but activate token not expired

            return $return;
        }

        $status = $this->verifyStatusbyid($request_result['uid']);

        if ($status['error']) {
            $return['message'] = $status['message'];

            return $return;
        }



        $query = "UPDATE {$this->config->table_users} SET isactive = :isactive WHERE id = :id";
        $query_prepared = $this->dbh->prepare($query);
        $query_params = [
            'isactive' => 1,
            'id' => $request_result['uid']
        ];
        $query_prepared->execute($query_params);

        $this->deleteRequest($request_result['id']);

        $return['error'] = false;
        $return['message'] = $this->__lang("account_activated");

        return $return;
    }

    /**
     * Creates a reset key for an email address and sends email
     * @param string $email
     * @param null $use_email_activation
     * @return array $return
     */
    public function requestReset($email, $use_email_activation = null)
    {
        $state['error'] = true;
        $block_status = $this->isBlocked();

        if ($block_status == "block") {
            $state['message'] = $this->__lang("user_blocked");

            return $state;
        }

        $validateEmail = $this->validateEmail($email);

        if ($validateEmail['error'] == 1) {
            $state['message'] = $this->__lang("email_invalid");

            return $state;
        }

        $query = "SELECT id, email FROM {$this->config->table_users} WHERE email = :email";
        $query_prepared = $this->dbh->prepare($query);
        $query_prepared->execute(['email' => $email]);

        $row = $query_prepared->fetch(\PDO::FETCH_ASSOC);
        if (!$row) {
            $this->addAttempt();

            $state['message'] = $this->__lang("email_incorrect");

            return $state;
        }

        $addRequest = $this->addRequest($row['id'], $email, "reset", $use_email_activation);

        if ($addRequest['error'] == 1) {
            $this->addAttempt();
            $state['message'] = $addRequest['message'];

            return $state;
        }

        $state['uid'] = $row['id'];
        $state['error'] = false;
        $state['message'] = ($use_email_activation == true ? $this->__lang("reset_requested") : $this->__lang('reset_requested_emailmessage_suppressed'));
        $state['token'] = $addRequest['token'];
        $state['expire'] = $addRequest["expire"];

        return $state;
    }

    /**
     * Logs out the session, identified by hash
     * @param string $hash
     * @return boolean
     */
    public function logout($hash)
    {
        if (strlen($hash) != self::HASH_LENGTH) {
            return false;
        }

        $this->isAuthenticated = false;
        $this->currentuser = null;

        return $this->deleteSession($hash);
    }

    /**
     * Logs out of all sessions for specified uid
     * @param int $uid
     * @return boolean
     */
    public function logoutAll($uid)
    {
        $this->isAuthenticated = false;
        $this->currentuser = null;

        return $this->deleteExistingSessions($uid);
    }


    /**
     * Hashes provided password with Bcrypt
     * @param string $password
     * @return string
     */
    public function getHash($password)
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => $this->config->bcrypt_cost]);
    }

    /**
     * Gets UID for a given email address, return int
     * @param string $email
     * @return int $uid
     */
    public function getUID($email)
    {
        $query = "SELECT id FROM {$this->config->table_users} WHERE email = :email";
        $query_prepared = $this->dbh->prepare($query);
        $query_prepared->execute(['email' => $email]);

        if ($query_prepared->rowCount() == 0) {
            return false;
        }

        return $query_prepared->fetchColumn();
    }

    /**
     * Creates a session for a specified user id
     * @param int $uid
     * @param boolean $remember
     * @return array $data
     */
    protected function addSession($uid, $remember)
    {
        $ip = $this->getIp();
        $user = $this->getBaseUser($uid);

        if (!$user) {
            return false;
        }

        $data['hash'] = sha1($this->config->site_key . microtime());
        $agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

        if (!$this->config->allow_concurrent_sessions) {
            $this->deleteExistingSessions($uid);
        }

        if ($remember == true) {
            $data['expire'] = strtotime($this->config->cookie_remember);
        } else {
            $data['expire'] = strtotime($this->config->cookie_forget);
        }

        $data['cookie_crc'] = sha1($data['hash'] . $this->config->site_key);

        // don't use INET_ATON(:ip), use ip2long(), 'cause SQLite or PosgreSQL does not have INET_ATON() function
        $query = "
INSERT INTO {$this->config->table_sessions}
(uid, hash, expiredate, ip, agent, cookie_crc)
VALUES (:uid, :hash, :expiredate, :ip, :agent, :cookie_crc)
";
        $query_prepared = $this->dbh->prepare($query);
        $query_params = [
            'uid'       => $uid,
            'hash'      => $data['hash'],
            'expiredate' => date("Y-m-d H:i:s", $data['expire']),
            'ip'        => $ip,
            'agent'     => $agent,
            'cookie_crc' => $data['cookie_crc']
        ];

        if (!$query_prepared->execute($query_params)) {
            return false;
        }

        setcookie($this->config->cookie_name, $data['hash'], $data['expire'], $this->config->cookie_path, $this->config->cookie_domain, $this->config->cookie_secure, $this->config->cookie_http);
        $_COOKIE[$this->config->cookie_name] = $data['hash'];

        return $data;
    }

    //@todo: delete cookie at deleteSession

    /**
     * Removes all existing sessions for a given UID wher phpauth_users.id=UID
     * @param int $uid
     * @return boolean
     */
    protected function deleteExistingSessions($uid)
    {
        $query = "DELETE FROM {$this->config->table_sessions} WHERE uid = :uid";
        $query_prepared = $this->dbh->prepare($query);
        $query_prepared->execute(['uid' => $uid]);

        return $query_prepared->rowCount() > 0;
    }

    /**
     * Removes a session based on hash
     * @param string $hash
     * @return boolean
     */

    protected function deleteSession($hash)
    {
        $query = "DELETE FROM {$this->config->table_sessions} WHERE hash = :hash";
        $query_prepared = $this->dbh->prepare($query);
        $query_prepared->execute(['hash' => $hash]);
        return $query_prepared->rowCount() == 1;
    }

    /**
     * Function to check if a session is valid
     * @param string $hash
     * @param bool $jwt check if we are verifying on behalf a jwt token
     * @return boolean
     */
    public function checkSession($hash, $jwt = false)
    {


        $ip = $this->getIp();
        $block_status = $this->isBlocked();
        if ($block_status == "block") {
            $return['message'] = $this->__lang("user_blocked");
            return false;
        }

        if (strlen($hash) != self::HASH_LENGTH) {
            return false;
        }

        // INET_NTOA(ip)
        $query = "SELECT id, uid, expiredate, ip, agent, cookie_crc FROM {$this->config->table_sessions} WHERE hash = :hash";
        $query_prepared = $this->dbh->prepare($query);
        $query_params = [
            'hash' => $hash
        ];
        $query_prepared->execute($query_params);
        if ($query_prepared->rowCount() == 0) {
            return false;
        }

        $row = $query_prepared->fetch(\PDO::FETCH_ASSOC);

        $uid = $row['uid'];
        $expiredate = strtotime($row['expiredate']);
        $currentdate = strtotime(date("Y-m-d H:i:s"));
        $db_ip = $row['ip'];
        $db_cookie = $row['cookie_crc'];


        $status = $this->verifyStatusbyid($uid);

        if ($status['error']) {
            $this->deleteSession($hash);

            return false;
        }

        if ($currentdate > $expiredate) {
            $this->deleteSession($hash);

            return false;
        }

        if ($ip != $db_ip) {
            return false;
        }

        if ($db_cookie == sha1($hash . $this->config->site_key)) {
            //jwt can't be automatically refreseh, they had to be explicity refreshed
            if (!$jwt && $expiredate - $currentdate < strtotime($this->config->cookie_renew) - $currentdate) {
                $this->deleteSession($hash);
                $this->addSession($uid, false);
            }
            return true;
        }

        return false;
    }

    /**
     * Retrieves the UID associated with a given session hash
     * @param string $hash
     * @return int $uid
     */
    public function getSessionUID($hash)
    {
        $query = "SELECT uid FROM {$this->config->table_sessions} WHERE hash = :hash";
        $query_prepared = $this->dbh->prepare($query);
        $query_params = [
            'hash' => $hash
        ];
        $query_prepared->execute($query_params);

        if ($query_prepared->rowCount() == 0) {
            return false;
        }

        return $query_prepared->fetch(\PDO::FETCH_ASSOC)['uid'];
    }

    /**
     * Checks if an email is already in use
     * @param string $email
     * @return boolean
     */
    public function isEmailTaken($email)
    {
        $query = "SELECT count(*) FROM {$this->config->table_users} WHERE email = :email";
        $query_prepared = $this->dbh->prepare($query);
        $query_prepared->execute(['email' => $email]);

        if ($query_prepared->fetchColumn() == 0) {
            return false;
        }

        return true;
    }

    /**
     * Checks if an email is banned
     * @param string $email
     * @return boolean
     */
    public function isEmailBanned($email)
    {
        try {
            $this->dbh->query("SELECT * FROM {$this->config->table_emails_banned} LIMIT 1;");
        } catch (\PDOException $e) {
            return false;
        }

        $query = "SELECT count(*) FROM {$this->config->table_emails_banned} WHERE domain = :domain";
        $query_prepared = $this->dbh->prepare($query);
        $query_prepared->execute([
            'domain' => (strtolower(explode('@', $email)[1]))
        ]);

        if ($query_prepared->fetchColumn() == 0) {
            return false;
        }

        return true;
    }

    /**
     * Adds a new user to database
     * @param string $email      -- email
     * @param string $password   -- password
     * @param array $params      -- additional params
     * @param boolean $use_email_activation  -- activate email confirm or not
     * @return array
     */
    protected function addUser($email, $password, $params = [], &$use_email_activation)
    {
        $return['error'] = true;

        $this->dbh->beginTransaction();

        $query = "INSERT INTO {$this->config->table_users} (isactive) VALUES (0)";
        $query_prepared = $this->dbh->prepare($query);

        if (!$query_prepared->execute()) {
            $return['message'] = $this->__lang("system_error") . " #03";
            return $return;
        }

        $uid = $this->dbh->lastInsertId("{$this->config->table_users}_id_seq");
        $email = htmlentities(strtolower($email));

        if ($use_email_activation) {
            $addRequest = $this->addRequest($uid, $email, "activation", $use_email_activation);

            if ($addRequest['error'] == 1) {
                $this->dbh->rollBack();
                $return['message'] = $addRequest['message'];
                return $return;
            }

            $isactive = 0;
        } else {
            $isactive = 1;
        }

        $password = $this->getHash($password);
        if (empty($this->config->days_for_automatic_password_expiration) || $this->config->days_for_automatic_password_expiration < 1) {
            $days2expire = 0;
            $expiration = null;
        } else {
            $days2expire = $this->config->days_for_automatic_password_expiration;
            $expiration = date("Y-m-d H:i:s", strtotime("now +{$this->config->days_for_automatic_password_expiration} days"));
        }

        if (empty($this->config->table_params)) {
            //retain backwards compatibility
            if (is_array($params) && count($params) > 0) {
                $customParamsQueryArray = [];

                foreach ($params as $paramKey => $paramValue) {
                    $customParamsQueryArray[] = ['value' => $paramKey . ' = ?'];
                }

                $setParams = ', ' . implode(', ', array_map(function ($entry) {
                    return $entry['value'];
                }, $customParamsQueryArray));
            } else {
                $setParams = '';
            }

            $query = "UPDATE {$this->config->table_users} SET email = ?, password = ?, isactive = ?, days2expire =?, expiration=?, status = 'enabled', statuschange=Now() {$setParams} WHERE id = ?";
            $query_prepared = $this->dbh->prepare($query);
            $bindParams = array_values(array_merge([$email, $password, $isactive, $days2expire, $expiration], $params, [$uid]));
        } else {
            try {
                $json_params = json_encode($this->applyFilterParams($params));
            } catch (\Exception $e) {
                $this->dbh->rollBack();
                $return['message'] = $e->getMessage();
                return $return;
            }
            $query = "UPDATE {$this->config->table_users} SET email = ?, password = ?, isactive = ?, days2expire = ?, expiration = ?, params = ?, status = 'enabled', statuschange=Now() WHERE id = ?";
            $query_prepared = $this->dbh->prepare($query);
            $bindParams = array($email, $password, $isactive, $days2expire, $expiration, $json_params, $uid);
        }

        if (!$query_prepared->execute($bindParams)) {
            $this->dbh->rollBack();
            $return['message'] = $this->__lang("system_error") . " #04";
            return $return;
        }
        $this->dbh->commit();
        $return['uid'] = $uid;
        $return['error'] = false;
        return $return;
    }

    /**
     * Gets basic user data for a given UID and returns an array
     * @param int $uid
     * @return array $data
     */
    protected function getBaseUser($uid)
    {
        $query = "SELECT email, password, isactive, days2expire, expiration, status FROM {$this->config->table_users} WHERE id = :id";
        $query_prepared = $this->dbh->prepare($query);
        $query_prepared->execute(['id' => $uid]);

        $data = $query_prepared->fetch(\PDO::FETCH_ASSOC);

        if (!$data) {
            return false;
        }

        $data['uid'] = $uid;

        return $data;
    }

    /**
     * Gets public user data for a given UID and returns an array, password will be returned if param $withpassword is TRUE
     * @param int $uid
     * @param bool|false $withpassword
     * @return array $data
     */
    public function getUser($uid, $withpassword = false)
    {
        $query = "SELECT * FROM {$this->config->table_users} WHERE id = :id";
        $query_prepared = $this->dbh->prepare($query);
        $query_prepared->execute(['id' => $uid]);

        $data = $query_prepared->fetch(\PDO::FETCH_ASSOC);

        if (!$data) {
            return false;
        }

        $data['uid'] = $uid;

        if (!$withpassword)
            unset($data['password']);

        if (!empty($this->config->table_params)) {
            $params = $this->applyFilterParams(json_decode($data['params']));
            $data['params'] = $params;
            $data['params'] = json_decode($params);
        }

        return $data;
    }


    /**
     * Allows a user to delete their account
     * @param int $uid
     * @param string $password
     * @param string $captcha_response = null
     * @return array $return
     */
    public function deleteUser($uid, $password, $captcha_response = null)
    {
        $return['error'] = true;

        $block_status = $this->isBlocked();
        if ($block_status == "verify") {
            if ($this->checkCaptcha($captcha_response) == false) {
                $return['message'] = $this->__lang("user_verify_failed");

                return $return;
            }
        }

        if ($block_status == "block") {
            $return['message'] = $this->__lang("user_blocked");

            return $return;
        }

        $validatePassword = $this->validatePassword($password);

        if ($validatePassword['error'] == 1) {
            $this->addAttempt();
            $return['message'] = $validatePassword['message'];

            return $return;
        }

        $user = $this->getBaseUser($uid);

        if (!password_verify($password, $user['password'])) {
            $this->addAttempt();
            $return['message'] = $this->__lang("password_incorrect");

            return $return;
        }

        $query = "UPDATE {$this->config->table_users} set status = 'deleted', statuschange = now(), id = :uid";
        $query_prepared = $this->dbh->prepare($query);

        if (!$query_prepared->execute(['uid' => $uid])) {
            $return['message'] = $this->__lang("system_error") . " #05";

            return $return;
        }

        $query = "DELETE FROM {$this->config->table_sessions} WHERE uid = :uid";
        $query_prepared = $this->dbh->prepare($query);

        if (!$query_prepared->execute(['uid' => $uid])) {
            $return['message'] = $this->__lang("system_error") . " #06";

            return $return;
        }

        $query = "DELETE FROM {$this->config->table_requests} WHERE uid = :uid";
        $query_prepared = $this->dbh->prepare($query);

        if (!$query_prepared->execute(['uid' => $uid])) {
            $return['message'] = $this->__lang("system_error") . " #07";

            return $return;
        }

        $return['error'] = false;
        $return['message'] = $this->__lang("account_deleted");

        return $return;
    }

    /**
     * Force delete user without password or captcha verification.
     *
     * @param $uid
     * @return mixed
     */
    public function deleteUserForced($uid)
    {
        $return['error'] = true;

        $query = "UPDATE {$this->config->table_users} set status = 'deleted', statuschange = now(), id = :uid";
        $query_prepared = $this->dbh->prepare($query);

        if (!$query_prepared->execute(['uid' => $uid])) {
            $return['message'] = $this->__lang("system_error") . " #05";

            return $return;
        }

        $query = "DELETE FROM {$this->config->table_sessions} WHERE uid = :uid";
        $query_prepared = $this->dbh->prepare($query);

        if (!$query_prepared->execute(['uid' => $uid])) {
            $return['message'] = $this->__lang("system_error") . " #06";

            return $return;
        }

        $query = "DELETE FROM {$this->config->table_requests} WHERE uid = :uid";
        $query_prepared = $this->dbh->prepare($query);

        if (!$query_prepared->execute(['uid' => $uid])) {
            $return['message'] = $this->__lang("system_error") . " #07";

            return $return;
        }

        $return['error'] = false;
        $return['message'] = $this->__lang("account_deleted");

        return $return;
    }

    /** 
     * Purge user, delete permanently 
     * @param $uid
     * @return mixed
     */
    public function purgeUser($uid)
    {
        $return['error'] = true;

        $query = "DELETE FROM {$this->config->table_users} WHERE id = :uid";
        $query_prepared = $this->dbh->prepare($query);

        if (!$query_prepared->execute(['uid' => $uid])) {
            $return['message'] = $this->__lang("system_error") . " #05";

            return $return;
        }

        $query = "DELETE FROM {$this->config->table_sessions} WHERE uid = :uid";
        $query_prepared = $this->dbh->prepare($query);

        if (!$query_prepared->execute(['uid' => $uid])) {
            $return['message'] = $this->__lang("system_error") . " #06";

            return $return;
        }

        $query = "DELETE FROM {$this->config->table_requests} WHERE uid = :uid";
        $query_prepared = $this->dbh->prepare($query);

        if (!$query_prepared->execute(['uid' => $uid])) {
            $return['message'] = $this->__lang("system_error") . " #07";

            return $return;
        }

        $return['error'] = false;
        $return['message'] = $this->__lang("account_deleted");

        return $return;
    }

    /**
     * Disable user administratively 
     * @param $email
     * @param mixed
     */
    public function disable($email)
    {
        $return['error'] = true;

        $query = "UPDATE {$this->config->table_users} set status = 'disabled', statuschange = now(),  email = :email";
        $query_prepared = $this->dbh->prepare($query);

        if (!$query_prepared->execute(['email' => $email])) {
            $return['message'] = $this->__lang("system_error") . " #23";

            return $return;
        }

        $return['error'] = false;
        $return['message'] = $this->__lang("account_disabled");

        return $return;
    }

    /**
     * Enable user administratively 
     * @param $email
     * @param mixed
     */
    public function enable($email)
    {
        $return['error'] = true;

        $query = "UPDATE {$this->config->table_users} set status = 'enabled', statuschange = now(),  email = :email";
        $query_prepared = $this->dbh->prepare($query);

        if (!$query_prepared->execute(['email' => $email])) {
            $return['message'] = $this->__lang("system_error") . " #24";

            return $return;
        }

        $return['error'] = false;
        $return['message'] = $this->__lang("account_enabled");

        return $return;
    }

    // protected function add

    /**
     * Creates an activation entry and sends email to user
     * @param int $uid
     * @param string $email
     * @param string $type
     * @param boolean $use_email_activation
     * @return array
     */
    protected function addRequest($uid, $email, $type, &$use_email_activation)
    {
        $return['error'] = true;

        $dictionary_key__request_exists = '';

        if ($type == 'activation') {

            $dictionary_key__request_exists = 'activation_exists';
        } elseif ($type == 'reset') {

            $dictionary_key__request_exists = 'reset_exists';
        } else {
            $return['message'] = $this->__lang("system_error") . " #08";

            return $return;
        }

        $send_email = true;

        // if not set up manually, check config data
        if (false !== $use_email_activation) {
            $use_email_activation = true;

            if ($type == "reset" && !!$this->config->emailmessage_suppress_reset) {
                $send_email = false;
            }

            if ($type == "activation" && !!$this->config->emailmessage_suppress_activation) {
                $send_email = false;
            }
        }

        $query = "SELECT id, expire FROM {$this->config->table_requests} WHERE uid = :uid AND type = :type";
        $query_prepared = $this->dbh->prepare($query);
        $query_prepared->execute(['uid' => $uid, 'type' => $type]);

        $row_count = $query_prepared->rowCount();

        if ($row_count > 0) {

            $row = $query_prepared->fetch(\PDO::FETCH_ASSOC);

            $expiredate  = strtotime($row['expire']);
            $currentdate = strtotime(date("Y-m-d H:i:s"));

            if ($currentdate < $expiredate) {
                $return['message'] = $this->__lang($dictionary_key__request_exists, date($this->config->custom_datetime_format, $expiredate));
                return $return;
            }

            $this->deleteRequest($row['id']);
        }

        /*// uneffective call. And, never be called, 'cause "Activation key is incorrect." throwen before
        if ($type == "activation" && $this->getBaseUser($uid)['isactive'] == 1) {
            $return['message'] = $this->__lang("already_activated");

            return $return;
        }
        */

        $token = $this->getRandomKey(self::TOKEN_LENGTH); // use GUID for tokens?
        $expire = date("Y-m-d H:i:s", strtotime($this->config->request_key_expiration));

        $query = "INSERT INTO {$this->config->table_requests} (uid, token, expire, type) VALUES (:uid, :token, :expire, :type)";
        $query_prepared = $this->dbh->prepare($query);

        $query_params = [
            'uid' => $uid,
            'token' => $token,
            'expire' => $expire,
            'type' => $type
        ];

        if (!$query_prepared->execute($query_params)) {
            $return['message'] = $this->__lang("system_error") . " #09";

            return $return;
        }

        $request_id = $this->dbh->lastInsertId();

        if ($use_email_activation === true && $send_email) {
            $sendmail_status = $this->do_SendMail($email, $type, $token);

            if ($sendmail_status['error']) {
                $this->deleteRequest($request_id);

                $return['message'] = $this->__lang("system_error") . ' ' . $sendmail_status['message'] . " #10";
                return $return;
            }
        }

        $return['error'] = false;
        $return['token'] = $token;
        $return['expire'] = $expire;

        return $return;
    }

    /**
     * Returns request data if key is valid
     * @param string $key
     * @param string $type
     * @return array $return
     */
    public function getRequest($key, $type)
    {
        $return['error'] = true;

        $query = "SELECT id, uid, expire FROM {$this->config->table_requests} WHERE token = ? AND type = ?";
        $query_prepared = $this->dbh->prepare($query);
        $query_prepared->execute([$key, $type]);

        if ($query_prepared->rowCount() === 0) {
            $this->addAttempt();
            $return['message'] = $this->__lang($type . "key_incorrect");

            return $return;
        }

        $row = $query_prepared->fetch(\PDO::FETCH_ASSOC);

        $expiredate = strtotime($row['expire']);
        $currentdate = strtotime(date("Y-m-d H:i:s"));

        if ($currentdate > $expiredate) {
            $this->addAttempt();
            $this->deleteRequest($row['id']);
            $return['message'] = $this->__lang($type . "key_expired");

            return $return;
        }

        $return['error'] = false;
        $return['id'] = $row['id'];
        $return['uid'] = $row['uid'];

        return $return;
    }

    /**
     * Deletes request from database
     * @param int $id
     * @return boolean
     */
    protected function deleteRequest($id)
    {
        $query = "DELETE FROM {$this->config->table_requests} WHERE id = :id";
        $query_prepared = $this->dbh->prepare($query);
        return $query_prepared->execute(['id' => $id]);
    }

    /**
     * Verifies the administrative status 
     * @param int $uid 
     * @return array $return ['error', 'message']
     */
    protected function verifyStatusbyid($id)
    {
        $state['error'] = true;
        $query = "SELECT status,id FROM {$this->config->table_users} where id = :id";
        $query_prepared = $this->dbh->prepare($query);
        $query_prepared->execute(["id" => $id]);
        $status = $query_prepared->fetch(\PDO::FETCH_ASSOC);
        if (!$status) {
            $status['message'] = $this->__lang("system_error" . "#21");
        } else {
            switch ($status['status']) {
                case "enabled":
                    $state['error'] = false;
                    $state['message'] = false;
                    break;
                case "disabled":
                    $state['message'] = $this->__lang("account_disabled");
                    $this->deleteExistingSessions($status['id']);
                    break;
                case "deleted":
                    $state['message'] = $this->__lang("system_error" . "#22");
                    $this->deleteExistingSessions($status['id']);
                    break;
            }
        }
        return $state;
    }

    /**
     * Verifies the administrative status 
     * @param string email 
     * @return array $return ['error', 'message']
     */

    protected function verifyStatus($email)
    {
        $state['error'] = true;
        $query = "SELECT status,id FROM {$this->config->table_users} where email = :email";
        $query_prepared = $this->dbh->prepare($query);
        $query_prepared->execute(["email" => $email]);
        $status = $query_prepared->fetch(\PDO::FETCH_ASSOC);
        if (!$status) {
            $status['message'] = $this->__lang("system_error" . "#21");
        } else {
            switch ($status['status']) {
                case "enabled":
                    $state['error'] = false;
                    $state['message'] = false;
                    break;
                case "disabled":
                    $state['message'] = $this->__lang("account_disabled");
                    $this->deleteExistingSessions($status['id']);
                    break;
                case "deleted":
                    $state['message'] = $this->__lang("system_error" . "#22");
                    $this->deleteExistingSessions($status['id']);
                    break;
            }
        }
        return $state;
    }


    /**
     * Verifies that a password is greater than minimal length
     *
     * security requirements (ZxcvbnPhp\Zxcvbn) not checked now.
     * @param string $password
     * @return array $return ['error', 'message']
     */
    protected function validatePassword($password)
    {
        $state['error'] = true;

        if (strlen($password) < (int) $this->config->verify_password_min_length) {
            $state['message'] = $this->__lang("password_short");

            return $state;
        }

        $state['error'] = false;

        return $state;
    }

    /**
     * Verifies that an email is valid
     * @param string $email
     * @return array $return
     */
    protected function validateEmail($email)
    {
        $state['error'] = true;

        if (strlen($email) < (int) $this->config->verify_email_min_length) {
            $state['message'] = $this->__lang("email_short", (int) $this->config->verify_email_min_length);

            return $state;
        } elseif (strlen($email) > (int) $this->config->verify_email_max_length) {
            $state['message'] = $this->__lang("email_long", (int) $this->config->verify_email_max_length);

            return $state;
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $state['message'] = $this->__lang("email_invalid", $email);

            return $state;
        }

        if ((int) $this->config->verify_email_use_banlist && $this->isEmailBanned($email)) {
            $this->addAttempt();
            $state['message'] = $this->__lang("email_banned");

            return $state;
        }

        $state['error'] = false;

        return $state;
    }


    /**
     * Allows a user to reset their password after requesting a reset key.
     * @param string $key
     * @param string $password
     * @param string $repeatpassword
     * @param string $captcha_response = null
     * @return array $return
     */
    public function resetPass($key, $password, $repeatpassword, $captcha_response = null)
    {
        $state['error'] = true;
        $block_status = $this->isBlocked();

        if ($block_status == "verify") {
            if ($this->checkCaptcha($captcha_response) == false) {
                $state['message'] = $this->__lang("user_verify_failed");

                return $state;
            }
        }

        if ($block_status == "block") {
            $state['message'] = $this->__lang("user_blocked");

            return $state;
        }

        if (strlen($key) != self::TOKEN_LENGTH) {
            $state['message'] = $this->__lang("resetkey_invalid");

            return $state;
        }

        $validatePasswordState = $this->validatePassword($password);

        if ($validatePasswordState['error']) {
            $state['message'] = $validatePasswordState['message'];
            return $state;
        }

        $zxcvbn = new Zxcvbn();

        if ($zxcvbn->passwordStrength($password)['score'] < intval($this->config->password_min_score)) {
            $state['message'] = $this->__lang('password_weak');

            return $state;
        }

        if ($password !== $repeatpassword) {
            // Passwords don't match
            $state['message'] = $this->__lang("newpassword_nomatch");

            return $state;
        }

        $zxcvbn = new Zxcvbn();

        if ($zxcvbn->passwordStrength($password)['score'] < intval($this->config->password_min_score)) {
            $state['message'] = $this->__lang('password_weak');

            return $state;
        }

        $data = $this->getRequest($key, "reset");

        if ($data['error'] == 1) {
            $state['message'] = $data['message'];

            return $state;
        }

        $user = $this->getBaseUser($data['uid']);

        if (!$user) {
            $this->addAttempt();
            $this->deleteRequest($data['id']);
            $state['message'] = $this->__lang("system_error") . " #11";

            return $state;
        }

        if (password_verify($password, $user['password'])) {
            $this->addAttempt();
            $state['message'] = $this->__lang("newpassword_match");

            return $state;
        }

        $password = $this->getHash($password);


        if ($user['days2expire'] > 0) {
            $expiration = date("Y-m-d H:i:s", strtotime("now +{$user['days2expire']} days"));
        } else {
            $expiration = null;
        }

        $query = "UPDATE {$this->config->table_users} SET password = :password, expiration = :expiration WHERE id = :id";
        $query_prepared = $this->dbh->prepare($query);
        $query_params = [
            'password' => $password,
            'expiration' => $expiration,
            'id' => $data['uid']
        ];
        $query_prepared->execute($query_params);

        if ($query_prepared->rowCount() == 0) {
            $state['message'] = $this->__lang("system_error") . " #12";

            return $state;
        }


        $this->deleteRequest($data['id']);
        $state['error'] = false;
        $state['message'] = $this->__lang("password_reset");

        return $state;
    }

    /**
     * Recreates activation email for a given email and sends
     * @param string $email
     * @param null $use_email_activation
     * @return array $return
     */
    public function resendActivation($email, $use_email_activation = null)
    {
        $state['error'] = true;
        $block_status = $this->isBlocked();

        if ($block_status == "block") {
            $state['message'] = $this->__lang("user_blocked");

            return $state;
        }

        if ($use_email_activation == null) {
            $state['message'] = $this->__lang('function_disabled');

            return $state;
        }

        $validateEmail = $this->validateEmail($email);

        if ($validateEmail['error']) {
            $state['message'] = $validateEmail['message'];

            return $state;
        }

        $query = "SELECT id, isactive, email FROM {$this->config->table_users} WHERE email = :email";
        $query_prepared = $this->dbh->prepare($query);
        $query_prepared->execute(['email' => $email]);

        $found_user = $query_prepared->fetch(\PDO::FETCH_ASSOC);

        if (!$found_user) {
            $this->addAttempt();
            $state['message'] = $this->__lang("email_incorrect"); // Really: account not found!

            return $state;
        }

        // this check must be implemented in activateAccount() method
        if ($found_user['isactive']) {
            $this->addAttempt();
            $state['message'] = $this->__lang("already_activated");

            return $state;
        }

        // Create an activation entry and sends email to user
        $addRequest = $this->addRequest($found_user['id'], $email, "activation", $use_email_activation);

        // $addRequest = $this->addRequestOptimized($found_user, "activation", $use_email_activation);


        if ($addRequest['error'] == 1) {
            $this->addAttempt();
            $state['message'] = $addRequest['message'];

            return $state;
        }

        $state['error'] = false;
        $state['message'] = $this->__lang("activation_sent");
        return $state;
    }

    /**
     * Changes a user's password
     * @param int $uid
     * @param string $currpass
     * @param string $newpass
     * @param string $repeatnewpass
     * @param string $captcha_response = null
     * @return array $return
     */
    public function changePassword($uid, $currpass, $newpass, $repeatnewpass, $captcha_response = null)
    {
        $return['error'] = true;
        $block_status = $this->isBlocked();

        if ($block_status == "verify") {
            if ($this->checkCaptcha($captcha_response) == false) {
                $return['message'] = $this->__lang("user_verify_failed");
                return $return;
            }
        }

        if ($block_status == "block") {
            $return['message'] = $this->__lang("user_blocked");

            return $return;
        }

        $validatePassword = $this->validatePassword($currpass);

        if ($validatePassword['error'] == 1) {
            $this->addAttempt();
            $return['message'] = $validatePassword['message'];

            return $return;
        }

        $validatePassword = $this->validatePassword($newpass);

        if ($validatePassword['error'] == 1) {
            $return['message'] = $validatePassword['message'];

            return $return;
        } elseif ($newpass !== $repeatnewpass) {
            $return['message'] = $this->__lang("newpassword_nomatch");

            return $return;
        }

        $zxcvbn = new Zxcvbn();

        if ($zxcvbn->passwordStrength($newpass)['score'] < intval($this->config->password_min_score)) {
            $return['message'] = $this->__lang('password_weak');

            return $return;
        }

        $user = $this->getBaseUser($uid);

        if (strcmp($user['email'], $newpass) == 0) {
            $return['message'] = $this->__lang('same_as_email');

            return $return;
        }

        if (!$user) {
            $this->addAttempt();
            $return['message'] = $this->__lang("system_error") . " #13";

            return $return;
        }

        if (!password_verify($currpass, $user['password'])) {
            $this->addAttempt();
            $return['message'] = $this->__lang("password_incorrect");

            return $return;
        }

        $newpass = $this->getHash($newpass);

        if ($user['days2expire'] > 0) {
            $expiration = date("Y-m-d H:i:s", strtotime("now +{$user['days2expire']} days"));
        } else {
            $expiration = null;
        }


        $query = "UPDATE {$this->config->table_users} SET password = ?, expiration = ? WHERE id = ?";
        $query_prepared = $this->dbh->prepare($query);
        $query_prepared->execute([$newpass, $expiration, $uid]);

        $return['error'] = false;
        $return['message'] = $this->__lang("password_changed");

        return $return;
    }

    /**
     * Changes a user's email
     * @param int $uid
     * @param string $email
     * @param string $password
     * @param string $captcha = null
     * @return array $return
     */
    public function changeEmail($uid, $email, $password, $captcha = null)
    {
        $return['error'] = true;
        $block_status = $this->isBlocked();

        if ($block_status == "verify") {
            if ($this->checkCaptcha($captcha) == false) {
                $return['message'] = $this->__lang("user_verify_failed");

                return $return;
            }
        }

        if ($block_status == "block") {
            $return['message'] = $this->__lang("user_blocked");

            return $return;
        }

        $validateEmail = $this->validateEmail($email);

        if ($validateEmail['error'] == 1) {
            $return['message'] = $validateEmail['message'];

            return $return;
        }

        if ($this->isEmailTaken($email)) {
            $this->addAttempt();
            $return['message'] = $this->__lang("email_taken");

            return $return;
        }

        $validatePassword = $this->validatePassword($password);

        if ($validatePassword['error'] == 1) {
            $return['message'] = $this->__lang("password_notvalid");

            return $return;
        }

        $user = $this->getBaseUser($uid);

        if (!$user) {
            $this->addAttempt();
            $return['message'] = $this->__lang("system_error") . " #14";

            return $return;
        }

        if (!password_verify($password, $user['password'])) {
            $this->addAttempt();
            $return['message'] = $this->__lang("password_incorrect");

            return $return;
        }

        if ($email == $user['email']) {
            $this->addAttempt();
            $return['message'] = $this->__lang("newemail_match");

            return $return;
        }

        $query = "UPDATE {$this->config->table_users} SET email = ? WHERE id = ?";
        $query_prepared = $this->dbh->prepare($query);
        $query_prepared->execute([$email, $uid]);

        if ($query_prepared->rowCount() == 0) {
            $return['message'] = $this->__lang("system_error") . " #15";

            return $return;
        }

        $return['error'] = false;
        $return['message'] = $this->__lang("email_changed");

        return $return;
    }

    /**
     * Informs if a user is locked out
     * @return string
     */
    public function isBlocked()
    {
        $ip = $this->getIp();
        $this->deleteAttempts($ip, false);

        // INET_ATON
        $query = "SELECT count(*) FROM {$this->config->table_attempts} WHERE ip = :ip";
        $query_prepared = $this->dbh->prepare($query); // INET_ATON(:ip)
        $query_prepared->execute(['ip' => $ip]);
        $attempts = $query_prepared->fetchColumn();

        if ($attempts < intval($this->config->attempts_before_verify)) {
            return "allow";
        }

        if ($attempts < intval($this->config->attempts_before_ban)) {
            return "verify";
        }

        return "block";
    }


    /**
     * Verifies a captcha code
     * @param string $captcha
     * @return boolean
     */
    protected function checkCaptcha($captcha)
    {
        return true;
    }


    /**
     * Check Google Recaptcha code.
     * If reCaptcha disabled in config or config not defined - return TRUE (captcha passed)
     *
     * @param $captcha_response
     * @return bool
     */
    protected function checkReCaptcha($captcha_response)
    {
        if (empty($this->recaptcha_config)) return true;

        if ($this->recaptcha_config['recaptcha_enabled']) {

            if (empty($this->recaptcha_config['recaptcha_secret_key'])) throw new \RuntimeException('No secret provided');
            if (!is_string($this->recaptcha_config['recaptcha_secret_key'])) throw new \RuntimeException('The provided secret must be a string');

            $recaptcha = new ReCaptcha($this->recaptcha_config['recaptcha_secret_key']);
            $checkout = $recaptcha->verify($captcha_response, $this->getIp());

            if (!$checkout->isSuccess()) {
                return false;
            }
        }

        return true;
    }


    /**
     * Adds an attempt to database
     * @return boolean
     */

    protected function addAttempt()
    {
        $ip = $this->getIp();
        $attempt_expiredate = date("Y-m-d H:i:s", strtotime($this->config->attack_mitigation_time));

        $query = "INSERT INTO {$this->config->table_attempts} (ip, expiredate) VALUES (:ip, :expiredate)";
        $query_prepared = $this->dbh->prepare($query); // INET_ATON(:ip)
        return $query_prepared->execute([
            'ip'         => $ip,
            'expiredate' => $attempt_expiredate
        ]);
    }

    /**
     * Deletes all attempts for a given IP from database
     *
     * @param string $ip
     * @param bool|false $all
     * @return bool
     */
    protected function deleteAttempts($ip, $all = false)
    {
        // NEXT : 'ip = INET_ATON(:ip)'
        $query = ($all)
            ? "DELETE FROM {$this->config->table_attempts} WHERE ip = :ip"
            : "DELETE FROM {$this->config->table_attempts} WHERE ip = :ip AND NOW() > expiredate ";

        $sth = $this->dbh->prepare($query);
        return $sth->execute([
            'ip' => $ip
        ]);
    }

    /**
     * Returns a random string of a specified length
     * @param int $length
     * @return string $key
     */
    public function getRandomKey($length = self::TOKEN_LENGTH)
    {
        $dictionary = "A1B2C3D4E5F6G7H8I9J0K1L2M3N4O5P6Q7R8S9T0U1V2W3X4Y5Z6a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0u1v2w3x4y5z6";
        $dictionary_length = strlen($dictionary);
        $key = "";

        for ($i = 0; $i < $length; $i++) {
            $key .= $dictionary[mt_rand(0, $dictionary_length - 1)];
        }

        return $key;
    }

    /**
     * Returns IP address
     * @return string $ip
     */
    protected function getIp()
    {
        if (getenv('HTTP_CLIENT_IP')) {
            $ipAddress = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ipAddress = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $ipAddress = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $ipAddress = getenv('HTTP_FORWARDED_FOR');
        } elseif (getenv('HTTP_FORWARDED')) {
            $ipAddress = getenv('HTTP_FORWARDED');
        } elseif (getenv('REMOTE_ADDR')) {
            $ipAddress = getenv('REMOTE_ADDR');
        } else {
            $ipAddress = '127.0.0.1';
        }

        return $ipAddress;
    }

    /**
     * Returns current session hash
     * @return string
     * @return boolean, false if no cookie
     */
    public function getCurrentSessionHash()
    {
        return $_COOKIE[$this->config->cookie_name] ?? false;
    }

    /**
     * Returns is user logged in
     * @return boolean
     */
    public function isLogged()
    {
        if ($this->isAuthenticated === false) {
            $this->isAuthenticated = $this->checkSession($this->getCurrentSessionHash());
        }
        return $this->isAuthenticated;
    }

    /**
     * Gets user data for current user (from cookie/session_hash) and returns an array, password is not returned
     * @param bool $updateSession = false
     * @return array $data
     * @return boolean false if no current user
     */
    public function getCurrentUser($updateSession = false)
    {
        if ($this->currentuser === null) {
            $hash = $this->getCurrentSessionHash();
            if ($hash === false) {
                return false;
            }

            $uid = $this->getSessionUID($hash);
            if ($uid === false) {
                return false;
            }

            $this->currentuser = $this->getUser($uid);
        }

        if ($updateSession) {
            $this->renewUserSession($hash);
        }
        return $this->currentuser;
    }

    /**
     * Update user session expire time using either session hash or uid
     * @param string $hash
     * @param int $uid = null
     * @return
     * 
     */

    private function renewUserSession($hash, $uid = null)
    {
        $expire = date("Y-m-d H:i:s", strtotime($this->config->cookie_remember));

        $where = (is_null(($uid))) ? "hash" : "uid";
        $arr = (is_null($uid)) ? $hash : $uid;

        $STH = $this->dbh->prepare("UPDATE {$this->config->table_sessions} SET expiredate = ? WHERE {$where} = ?");
        $STH->execute([$expire, $arr]);

        return;
    }


    /**
     * Compare user's password with given password
     * @param int $userid
     * @param string $password_for_check
     * @return bool
     */
    public function comparePasswords($userid, $password_for_check)
    {
        $query = "SELECT password FROM {$this->config->table_users} WHERE id = ?";
        $query_prepared = $this->dbh->prepare($query);
        $query_prepared->execute([$userid]);

        $data = $query_prepared->fetch(\PDO::FETCH_ASSOC);

        if (!$data) {
            return false;
        }

        return password_verify($password_for_check, $data['password']);
    }

    /**
     * Check if users password needs to be rehashed
     * @param string $password
     * @param string $hash
     * @param int $uid
     * @return bool
     */
    public function password_verify_with_rehash($password, $hash, $uid)
    {
        if (!password_verify($password, $hash)) {
            return false;
        }

        if (password_needs_rehash($hash, PASSWORD_DEFAULT, ['cost' => $this->config->bcrypt_cost])) {
            $hash = $this->getHash($password);

            $query = "UPDATE {$this->config->table_users} SET password = ? WHERE id = ?";
            $query_prepared = $this->dbh->prepare($query);
            $query_prepared->execute([$hash, $uid]);
        }

        return true;
    }

    /**
     * Translates key-message to defined language using substitutional params
     *
     * @param $key
     * @return mixed
     */
    public function __lang($key, ...$args)
    {
        $string = array_key_exists($key, $this->messages_dictionary) ? $this->messages_dictionary[$key] : $key;
        return (func_num_args() > 1) ? vsprintf($string, $args) : $string;
    }


    /**
     * Send email via PHPMailer
     *
     * @param $email
     * @param $type
     * @param $key
     * @return array $return (contains error code and error message)
     */
    public function do_SendMail($email, $type, $key)
    {
        $return = [
            'error' => true
        ];
        $mail = new PHPMailer();

        // Check configuration for custom SMTP parameters
        try {
            // Server settings
            if ($this->config->smtp) {

                if ($this->config->smtp_debug) {
                    $mail->SMTPDebug = $this->config->smtp_debug;
                }

                $mail->isSMTP();

                $mail->Host = $this->config->smtp_host;
                $mail->SMTPAuth = $this->config->smtp_auth;

                // set SMTP auth username/password
                if (!is_null($this->config->smtp_auth)) {
                    $mail->Username = $this->config->smtp_username;
                    $mail->Password = $this->config->smtp_password;
                }

                // set SMTPSecure (tls|ssl)
                if (!is_null($this->config->smtp_security)) {
                    $mail->SMTPSecure = $this->config->smtp_security;
                }

                $mail->Port = $this->config->smtp_port;
            } //without this params internal mailer will be used.

            //Recipients
            $mail->setFrom($this->config->site_email, $this->config->site_name);
            $mail->addAddress($email);

            $mail->CharSet = $this->config->mail_charset;

            //Content
            $mail->isHTML(true);

            if ($type == 'activation') {
                $mail->Subject  = $this->__lang('email_activation_subject', $this->config->site_name);
                if ($this->config->site_activation_page_append_code)
                    $url = $this->config->site_activation_page . "/" . $key;
                else
                    $url = $this->config->site_activation_page;
                $mail->Subject  = $this->__lang('email_activation_subject', $this->config->site_name);
                $mail->Body     = $this->__lang('email_activation_body', $this->config->site_url, $url, $key);
                $mail->AltBody  = $this->__lang('email_activation_altbody', $this->config->site_url, $url, $key);
            } elseif ($type == 'reset') {
                if ($this->config->site_password_reset_page_append_code)
                    $url = $this->config->site_password_reset_page . "/" . $key;
                else
                    $url = $this->config->site_password_reset_page;
                $mail->Subject  = $this->__lang('email_reset_subject', $this->config->site_name);
                $mail->Body     = $this->__lang('email_reset_body', $this->config->site_url, $url, $key);
                $mail->AltBody  = $this->__lang('email_reset_altbody', $this->config->site_url, $url, $key);
            } else {
                return false;
            }

            if (!$mail->send())
                throw new \Exception($mail->ErrorInfo);

            $return['error'] = false;
        } catch (\Exception $e) {
            $return['message'] = $mail->ErrorInfo;
        }

        return $return;
    }

    /**
     * Update userinfo for user with given id = $uid
     * @param int $uid
     * @param array $params
     * @return array $return[error/message]
     */
    public function updateUser($uid, $params)
    {
        $setParams = '';

        //unset uid which is set in getUser(). array generated in getUser() is now usable as parameter for updateUser()
        unset($params['uid']);
        if (empty($this->config->tables_params)) {
            if (is_array($params) && count($params) > 0) {
                $setParams = implode(', ', array_map(function ($key, $value) {
                    return $key . ' = ?';
                }, array_keys($params), $params));
            }

            $query = "UPDATE {$this->config->table_users} SET {$setParams} WHERE id = ?";

            //NB: There is NO possible SQL-injection here, 'cause $setParams will be like 'name = ?, age = ?'

            $query_prepared = $this->dbh->prepare($query);
            $bindParams = array_values(array_merge($params, [$uid]));

            if (!$query_prepared->execute($bindParams)) {
                $return['message'] = $this->__lang("system_error") . " #04";
                return $return;
            }
        } else {
            try {
                $json_params = json_encode($this->applyFilterParams($params));
            } catch (\Exception $e) {
                $return['message'] = $e->getMessage();
                return $return;
            }
            $query = "UPDATE {$this->config->table_users} params = ?  WHERE id = ?";
            $query_prepared = $this->dbh->prepare($query);
            $bindParams = array($json_params, $uid);
        }

        $return['error'] = false;
        $return['message'] = 'Ok.';

        return $return;
    }

    /**
     * Returns current user UID if logged or FALSE otherwise.
     *
     * @return int
     */
    public function getCurrentUID()
    {
        return $this->getSessionUID($this->getCurrentSessionHash());
    }

    /**
     * Return current user sesssion info
     *
     * @return bool|mixed
     */
    public function getCurrentSessionUserInfo()
    {
        $ts = $this->config->table_sessions;
        $tu = $this->config->table_users;

        $query = "
		SELECT {$ts}.uid, {$ts}.expiredate, {$ts}.ip, {$tu}.email
		FROM {$ts}, {$tu}
		WHERE hash = :hash AND {$ts}.uid = {$tu}.id";

        $query = $this->dbh->prepare($query);
        $query->execute([
            'hash'    =>    $this->getCurrentSessionHash()
        ]);

        if ($query->rowCount() == 0) {
            return false;
        }

        return $query->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Deletes expired attempts from the database
     */
    private function deleteExpiredAttempts()
    {
        $this->dbh->exec("
            DELETE FROM {$this->config->table_attempts} WHERE NOW() > expiredate
        ");
    }

    /**
     * Deletes expired sessions from the database
     */
    private function deleteExpiredSessions()
    {
        $this->dbh->exec("
            DELETE FROM {$this->config->table_sessions} WHERE NOW() > expiredate
        ");
    }

    /**
     * Deletes expired requests from the database
     */
    private function deleteExpiredRequests()
    {
        $this->dbh->exec("
            DELETE FROM {$this->config->table_requests} WHERE NOW() > expire
        ");
    }

    /**
     * Daily cron job to remove expired data from the database
     */
    public function cron()
    {
        $this->deleteExpiredAttempts();
        $this->deleteExpiredSessions();
        $this->deleteExpiredRequests();
    }

    /**
     * Add a parameter to be validated by the json object, the object is restricted to be unidimensional, to store pair of $key=>$values
     * @param string    $key the name of the key at the jsob object, cannot be empty and will be sanitized and lowerized
     * @param string    $name human name of the variable, suitable for UI, if empty, key will be used instead 
     * @param enum      $required y/n, default y,  
     * @param string    $description description of the filed, human redeable, suitable for UI, can be empty
     * @param enum      $filter, filter_var php to be applied, options: NONE,  FILTER_SANITIZE_EMAIL, FILTER_SANITIZE_ENCODED, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_SANITIZE_NUMBER_INT, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_SANITIZE_STRING, FILTER_SANITIZE_URL, default NONE 
     * @return array $return[error/message]
     */
    public function addParam($key = null, $name = null, $required = null, $description = null, $filter = "NONE")
    {
        $return['error'] = true;

        $filterOptions = array("NONE", "FILTER_SANITIZE_EMAIL", "FILTER_SANITIZE_ENCODED", "FILTER_SANITIZE_NUMBER_FLOAT", "FILTER_SANITIZE_NUMBER_INT", "FILTER_SANITIZE_SPECIAL_CHARS", "FILTER_SANITIZE_STRING", "FILTER_SANITIZE_URL");

        if (is_null($key) || empty($key)) {
            $return['message'] = $this->__lang("system_error" . "#17");
            return $return;
        }
        if (!in_array($filter, $filterOptions)) {
            $return['message'] = $this->__lang("system_error" . "#18");
            return $return;
        }
        if (is_null($name) || empty($name)) {
            $name = $key;
        }
        if (is_null($required) || empty($required) || strcmp($required, "n") != 0) {
            $required = "y";
        }

        $key = strtolower(str_replace(" ", "", filter_var($key, FILTER_SANITIZE_STRING)));
        $query = "SELECT name,description FROM {$this->config->table_params} WHERE json_key=:key";
        $query_prepared = $this->dbh->prepare($query);
        if (!$query_prepared->execute(['key' => $key])) {
            $return['message'] = $this->__lang("system_error" . "#19");
            return $return;
        }
        $row = $query_prepared->fetch(\PDO::FETCH_ASSOC);
        if ($row) {
            $return['message'] = $this->__lang("system_error" . "#20");
            $return[$key] = $row;
            return $return;
        }

        $query = "INSERT INTO {$this->config->table_params} values ( :key, :name, :required, :description, :filter )";
        $query_prepared = $this->dbh->prepare($query);
        if (!$query_prepared->execute(['key' => $key, 'name' => $name, 'required' => $required, 'description' => $description, 'filter' => $filter])) {
            $return['message'] = $this->__lang("system_error" . "#21");
            return $return;
        };
        $return['message'] = $this->__lang("Key added") . ": {$key}";
        $return['error'] = false;
        return $return;
    }

    /**
     * Removes a param, always return true... 
     * @return true
     */
    public function removeParam($key = null)
    {
        $query = "DELETE FROM {$this->config->table_params} where json_key=:key";
        $query_prepared = $this->dbh->prepare($query);
        $query_prepared->execute(["key" => $key]);
        return true;
    }

    /**
     * Builds the array to be converted into json_object from the database with the filter aplicable to the value 
     * @return array where key is the json_key from table_params
     */
    private function buildParams()
    {
        $jsonParams = array();
        $query  = "SELECT json_key,filter,required from {$this->config->table_params} order by json_key";
        $query_prepared = $this->dbh->prepare($query);
        $query_prepared->execute();
        while ($key = $query_prepared->fetch(\PDO::FETCH_ASSOC)) {
            $jsonParams[$key['json_key']] = array("required" => $key['required'], "filter" => $key['filter']);
        }
        return $jsonParams;
    }

    /**
     * Updates vigency of the password for the user, applys for the next password change
     * @param int $uid 
     * @param int $days2expire, 0 to disable expiration
     * @returns true;
     */
    public function updateDays2Expire($uid, $days2expire, $doExpiration = false)
    {
        $days2expire = intval($days2expire);
        $query = "UPDATE {$this->config->table_users} set days2expire = :days2expire where id=:uid";
        $query_prepared = $this->dbh->prepare($query);
        $query_prepared->execute(['days2expire' => $days2expire, "uid" => $uid]);
        $query = "UPDATE {$this->config->table_users} set days2expire = :days2expire where id=:uid";
        if ($doExpiration) {
            if ($days2expire > 0) {
                $expiration = date("Y-m-d 23:59:59", strtotime("now +{$days2expire} days"));
            } else {
                $expiration = null;
            }
            $query = "UPDATE {$this->config->table_users} set expiration = :expiration where id=:uid";
            $query_prepared = $this->dbh->prepare($query);
            $query_prepared->execute(['expiration' => $expiration, "uid" => $uid]);
        }
        return true;
    }

    /**
     * Updates expiration of a password to an arbitrary date
     * @param int   $uid 
     * @param sting $expiration format YYYY-mm-dd time will be set to 23:59:59
     * @returns bool, false  if exporation is not correct;
     */
    public function updateExpiration($uid, $expiration)
    {
        if (strcmp($expiration, date("Y-m-d", strtotime($expiration))) != 0)
            return false;
        $query = "UPDATE {$this->config->table_users} set expiration = :expiration where id=:uid";
        $query_prepared = $this->dbh->prepare($query);
        $query_prepared->execute(['expiration' => $expiration . " 23:59:59", "uid" => $uid]);
        return true;
    }

    /**
     * Set secret to be use for jwt
     * @param string $secret JWT secret key, must comply with Zxcvbn score
     * @return true,  trow exception if Zxcvbn not satisfied 
     */
    public function setPrivateKey($secret)
    {
        $zxcvbn = new Zxcvbn();
        if ($zxcvbn->passwordStrength($secret)['score'] < intval($this->config->password_min_score)) {
            throw new \Exception($this->__lang('password_weak'));
            return false;
        }
        $this->private = $secret;
        return true;
    }

    /**
     * Set public key for RS256 JWT algorithm
     * @param string public key for RS256 algo 
     * @return true
     */
    public function setPublicKey($publicKey)
    {
        $this->public = $publicKey;
        return true;
    }



    /** 
     * Generate JWT token, 
     * @param $iss string jwt issuer claim
     * @param $aud string  jtw audience audience 
     - @param $remember bool to use short time or long time
     * @param $hash string hashed autentication 
     * @param $callback  array object::function, $hash will be passed as argument, should return an array to be the data payload 
     * @param $return bool jwt token or false 
     */
    public function generateJWT($iss, $aud, $remember, $hash, $callback = null)
    {
        if (!$this->private) {
            throw new Exception($this->__lang('system_error'));
            return false;
        }
        if ($this->public) {
            $algo = 'RS256';
        } else {
            $algo = 'HS512';
        }
        if (!$callback) {
            $params = array("hash" => $hash);
        } else {
            $uid = $this->getSessionUID($hash);
            $user = $this->getBaseUser($uid);
            if (!is_array($user)) {
                throw new \Exception($this->__lang("system_error"));
                exit;
            }
            $user["hash"] = $hash;
            $params = call_user_func($callback, $user);
            $params['hash'] = $hash;
            $params['remember'] = $remember;
            if (!is_array($params)) {
                throw new \Exception($this->__lang('system_error'));
                return false;
            }
        }
        if ($remember) {
            $ttl = strtotime($this->config->cookie_remember) - time();
        } else {
            $ttl = strtotime($this->config->cookie_forget) - time();
        }


        $iat = time();
        $nbf = $iat;
        $exp = $iat + $ttl;
        $token = array(
            "iss" => $iss,
            "aud" => $aud,
            "iat" => $iat,
            "nbf" => $nbf,
            "exp" => $exp,
            "data" => $params
        );
        $jwt = \Firebase\JWT\JWT::encode($token, $this->private, $algo);
        return $jwt;
    }

    /**
     * Decode a JWT 
     * @param jwt 
     * @return array decoded data claims, error if invalid jwt
     */
    public function decodeJwt($jwt)
    {
        if ($this->public) {
            $algo = 'RS256';
            $key = $this->public;
        } else {
            $algo = 'HS512';
            $key = $this->private;
        }
        $decode = \Firebase\JWT\JWT::decode($jwt, $key, array($algo));
        $decode = json_decode(json_encode($decode), true);;
        return $decode;
    }

    /**
     * Validate jwt token AND hash, hash must inside data claim as an array
     * @param $jwt string signed token
     * @return bool true if valid jwt and user false otherwise
     */

    public function verifyJwtAuth($jwt)
    {
        try {
            $decoded = $this->decodeJwt($jwt);
            $session = $this->checkSession($decoded['data']['hash'], true);
            return $session;
        } catch (\UnexpectedValueException $e) {
            $message = $e->getMessage();
            return false;
        }
    }

    /**
     * Validate jwt token AND hash, hash must inside data claim as an array
     * @param $jwt string signed token
     * @return bool true if valid jwt and user false otherwise
     */

    public function renewJwt($jwt)
    {
        try {
            $decoded = $this->decodeJwt($jwt);
            $session = $this->checkSession($decoded['data']['hash'], true);
            if ($session) {
                $uid = $this->getSessionUID($decoded['data']['hash']);
                $this->deleteSession($decoded['data']['hash']);
                $session = $this->addSession($uid, 0);
                $ttl = strtotime($this->config->cookie_forget) - time();
                $data = $decoded['data'];
                $jwt = $this->generateJWT($decoded['iss'], $decoded['aud'], $data['remember'], $session['hash'], function ($user) use ($data) {
                    return $data;
                });
                return array("error" => false, "jwt" => $jwt);
            }
            return array("error" => true, "message" => $this->__lang("invalid"));
        } catch (\UnexpectedValueException $e) {
            return array("error" => true, "message" => $e->getMessage());
        }
    }

    /**
     * Filter the params by the defined filter and validate if vale is required,if required, trows and exeption
     * @returns array built from tables_params definition and sanitizing filter applied
     */
    private function applyFilterParams($params)
    {
        $jsonParams = $this->buildParams();
        $finalParams = array();
        foreach ($jsonParams as $k => $v) {
            if (strcmp($v['required'], 'y') == 0 && !isset($params[$k])) {
                throw new \Exception("${k} required but not set");
            }
            if (strcmp($v['filter'], 'NONE') == 0) {
                $finalParams[$k] = @$params[$k] ? $params[$k] : false;
            } else {
                $finalParams[$k] = @$params[$k] ? filter_var($params[$k], constant($v['filter'])) : false;
            }
        }
        return $finalParams;
    }

    /**
     * Verifies authentication from google and creates a session
     * @param string code
     * @return array $return_arr
     */

    function authenticateViaGoogle($code)
    {
        $return_arr = [];
        $return_arr['error'] = true;
        $return_arr['message'] = "unknown error";
        $pay_load = null;

        //create google auth object
        $g_client = new \Google\Client();

        $g_client->setClientId($this->config->google_client_id);
        $g_client->setClientSecret($this->config->google_client_secret);
        $g_client->setRedirectUri($this->config->google_redirect_url);
        $g_client->addScope("email");

        $pay_load = null;

        try {
            $googleToken = $g_client->fetchAccessTokenWithAuthCode($code);
            $g_client->setAccessToken($googleToken);
        } catch (\Exception $e) {
            error_log($e);
            return $return_arr;
        }

        try {
            $pay_load = $g_client->verifyIdToken();
        } catch (\Exception $e) {
            error_log($e);
            return $return_arr;
        }

        if ($pay_load !== null) {
            error_log(print_r($pay_load, true));
            $google_mail = $pay_load["email"];
        } else {
            error_log("PAYLOAD LEER");
            $return_arr['message'] = "payload empty";
            return $return_arr;
        }

        $query = "SELECT id FROM {$this->config->table_users} WHERE email = :email";
        $query_prepared = $this->dbh->prepare($query);
        $query_params = [':email' => $google_mail];

        $query_prepared->execute($query_params);

        $data = $query_prepared->fetch(\PDO::FETCH_ASSOC);

        if (!isset($data['id'])) {
            $return_arr['message'] = "Account with given email not present in users-table";
            return $return_arr;
        }

        $result_add_session = $this->addSession($data['id'], true);

        if ($result_add_session['error'] == true) {
            $return_arr['message'] = "could not add Session";
            return $return_arr;
        }

        $return_arr['error'] = false;
        $return_arr['message'] = "Your logged in now!";

        return $return_arr;
    }

    /**
     * Generates and returns Google-Auth-URL
     * @return array $return_arr
     */

    function getGoogleAuthURL()
    {

        $return_arr = [];
        $return_arr['error'] = true;
        $return_arr['auth_url'] = null;
        $auth_url = null;

        $g_client = new \Google\Client();

        try {
            $g_client->setClientId($this->config->google_client_id);
            $g_client->setClientSecret($this->config->google_client_secret);
            $g_client->setRedirectUri($this->config->google_redirect_url);
            $g_client->addScope("email");

            $auth_url = $g_client->createAuthUrl();
        } catch (\Exception $e) {
            error_log($e);
            return $return_arr;
        }

        if ($auth_url != null) {
            $return_arr['auth_url'] = $auth_url;
            $return_arr['error'] = false;
        }

        return $return_arr;
    }
}
