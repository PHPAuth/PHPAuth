<?php

namespace PHPAuth;

use ZxcvbnPhp\Zxcvbn;
use PHPMailer\PHPMailer\PHPMailer;
use ReCaptcha\ReCaptcha;

/**
 * Auth class
 * Required PHP 5.6 and above.
 *
 */

class Auth
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


    protected $islogged = NULL;
    protected $currentuser = NULL;

    /**
     * @var \stdClass $messages_dictionary
     */
    protected $messages_dictionary = [];

    /**
     * @var \stdClass $recaptcha_config
     */
    protected $recaptcha_config = [];


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
    }

    /**
     * Logs a user in
     * @param string $email
     * @param string $password
     * @param int $remember
     * @param string $captcha_response = NULL
     * @return array $return
     */
    public function login($email, $password, $remember = 0, $captcha_response = NULL)
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

        $validateEmail = $this->validateEmail($email);
        $validatePassword = $this->validatePassword($password);

        if ($validateEmail['error'] == 1) {
            $this->addAttempt();
            $return['message'] = $this->__lang("email_password_invalid");

            return $return;
        } elseif ($validatePassword['error'] == 1) {
            $this->addAttempt();
            $return['message'] = $this->__lang("email_password_invalid");

            return $return;
        } elseif ($remember != 0 && $remember != 1) {
            $this->addAttempt();
            $return['message'] = $this->__lang("remember_me_invalid");

            return $return;
        }

        $uid = $this->getUID(strtolower($email));

        if (!$uid) {
            $this->addAttempt();
            $return['message'] = $this->__lang("email_password_incorrect");

            return $return;
        }

        $user = $this->getBaseUser($uid);

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
    * Creates a new user, adds them to database
    * @param string $email
    * @param string $password
    * @param string $repeatpassword
    * @param array  $params
    * @param string $captcha_response = NULL
    * @param bool $use_email_activation = NULL
    * @return array $return
    */
    public function register($email, $password, $repeatpassword, $params = Array(), $captcha_response = NULL, $use_email_activation = NULL)
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
        $return['message'] = ($use_email_activation == true ? $this->__lang("register_success") : $this->__lang('register_success_emailmessage_suppressed') );

        return $return;
    }

    /**
    * Activates a user's account
    * @param string $key
    * @return array $return
    */
    public function activate($key)
    {
        $return['error'] = true;
        $block_status = $this->isBlocked();

        if ($block_status == "block") {
            $return['message'] = $this->__lang("user_blocked");

            return $return;
        }

        if (strlen($key) !== self::TOKEN_LENGTH) {
            $this->addAttempt();
            $return['message'] = $this->__lang("activationkey_invalid");

            return $return;
        }

        $getRequest = $this->getRequest($key, "activation");

        if ($getRequest['error'] == 1) {
            $return['message'] = $getRequest['message'];

            return $return;
        }

        if ($this->getBaseUser($getRequest['uid'])['isactive'] == 1) {
            $this->addAttempt();
            $this->deleteRequest($getRequest['id']);
            $return['message'] = $this->__lang("system_error") . " #02";

            return $return;
        }

        $query = $this->dbh->prepare("UPDATE {$this->config->table_users} SET isactive = :isactive WHERE id = :id");
        $query_params = [
            'isactive' => 1,
            'id' => $getRequest['uid']
        ];
        $query->execute($query_params);

        $this->deleteRequest($getRequest['id']);

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
    public function requestReset($email, $use_email_activation = NULL)
    {
        $return['error'] = true;
        $block_status = $this->isBlocked();

        if ($block_status == "block") {
            $return['message'] = $this->__lang("user_blocked");

            return $return;
        }

        $validateEmail = $this->validateEmail($email);

        if ($validateEmail['error'] == 1) {
            $return['message'] = $this->__lang("email_invalid");

            return $return;
        }

        $query = $this->dbh->prepare("SELECT id FROM {$this->config->table_users} WHERE email = ?");
        $query->execute(array($email));

        $row = $query->fetch(\PDO::FETCH_ASSOC);
		if (!$row) {
            $this->addAttempt();

            $return['message'] = $this->__lang("email_incorrect");

            return $return;
        }

        $addRequest = $this->addRequest($row['id'], $email, "reset", $use_email_activation);

        if ($addRequest['error'] == 1) {
            $this->addAttempt();
            $return['message'] = $addRequest['message'];

            return $return;
        }

        $return['error'] = false;
        $return['message'] = ($use_email_activation == true ? $this->__lang("reset_requested") : $this->__lang('reset_requested_emailmessage_suppressed'));

        return $return;
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

        return $this->deleteSession($hash);
    }

    /**
    * Hashes provided password with Bcrypt
    * @param string $password
    * @param string $password
    * @return string
    */
    public function getHash($password)
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => $this->config->bcrypt_cost]);
        // return password_hash($password, PASSWORD_DEFAULT, $this->config->password_hashOptions);
    }

    /**
    * Gets UID for a given email address and returns an array
    * @param string $email
    * @return int $uid
    */
    public function getUID($email)
    {
        $query = $this->dbh->prepare("SELECT id FROM {$this->config->table_users} WHERE email = :email");
        $query->execute(['email' => $email]);

        if ($query->rowCount() == 0) {
            return false;
        }

        return $query->fetchColumn();
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

        $this->deleteExistingSessions($uid);

        if ($remember == true) {
            $data['expire'] = strtotime($this->config->cookie_remember);
        } else {
            $data['expire'] = strtotime($this->config->cookie_forget);
        }

        $data['cookie_crc'] = sha1($data['hash'] . $this->config->site_key);

        // INET_ATON(:ip)
        $query = $this->dbh->prepare("
INSERT INTO {$this->config->table_sessions}
(uid, hash, expiredate, ip, agent, cookie_crc)
VALUES (:uid, :hash, :expiredate, :ip, :agent, :cookie_crc)
");
        $query_params = [
            'uid'       => $uid,
            'hash'      => $data['hash'],
            'expiredate'=> date("Y-m-d H:i:s", $data['expire']),
            'ip'        => $ip,
            'agent'     => $agent,
            'cookie_crc'=> $data['cookie_crc']
        ];

        if (!$query->execute($query_params)) {
            return false;
        }

        setcookie($this->config->cookie_name, $data['hash'], $data['expire'], $this->config->cookie_path, $this->config->cookie_domain, $this->config->cookie_secure, $this->config->cookie_http);
        $_COOKIE[$this->config->cookie_name] = $data['hash'];

        return $data;
    }

    /**
    * Removes all existing sessions for a given UID
    * @param int $uid
    * @return boolean
    */
    protected function deleteExistingSessions($uid)
    {
        $query = $this->dbh->prepare("DELETE FROM {$this->config->table_sessions} WHERE uid = :uid");
        $query->execute(['uid' => $uid]);

        return $query->rowCount() == 1;
    }

    /**
    * Removes a session based on hash
    * @param string $hash
    * @return boolean
    */

    protected function deleteSession($hash)
    {
        $query = $this->dbh->prepare("DELETE FROM {$this->config->table_sessions} WHERE hash = :hash");
        $query->execute(['hash' => $hash]);
        return $query->rowCount() == 1;
    }

    /**
    * Function to check if a session is valid
    * @param string $hash
    * @return boolean
    */
    public function checkSession($hash)
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
        $query = $this->dbh->prepare("SELECT id, uid, expiredate, ip, agent, cookie_crc FROM {$this->config->table_sessions} WHERE hash = :hash");
        $query_params = [
            'hash' => $hash
        ];
        $query->execute($query_params);

        if ($query->rowCount() == 0) {
            return false;
        }

        $row = $query->fetch(\PDO::FETCH_ASSOC);

        // $sid = $row['id'];
        $uid = $row['uid'];
        $expiredate = strtotime($row['expiredate']);
        $currentdate = strtotime(date("Y-m-d H:i:s"));
        $db_ip = $row['ip'];
        // $db_agent = $row['agent'];
        $db_cookie = $row['cookie_crc'];

        if ($currentdate > $expiredate) {
            $this->deleteExistingSessions($uid);

            return false;
        }

        if ($ip != $db_ip) {
            return false;
        }

        if ($db_cookie == sha1($hash . $this->config->site_key)) {
            if ($expiredate - $currentdate < strtotime($this->config->cookie_renew) - $currentdate) {
                $this->deleteExistingSessions($uid);
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
        $query = $this->dbh->prepare("SELECT uid FROM {$this->config->table_sessions} WHERE hash = :hash");
        $query_params = [
            'hash' => $hash
        ];
        $query->execute($query_params);

        if ($query->rowCount() == 0) {
            return false;
        }

        return $query->fetch(\PDO::FETCH_ASSOC)['uid'];
    }

    /**
    * Checks if an email is already in use
    * @param string $email
    * @return boolean
    */
    public function isEmailTaken($email)
    {
        $query = $this->dbh->prepare("SELECT count(*) FROM {$this->config->table_users} WHERE email = :email");
        $query->execute(['email' => $email]);

        if ($query->fetchColumn() == 0) {
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
        if (! $this->dbh->query("SHOW TABLES LIKE '{$this->config->table_emails_banned}'")->fetchAll() ) {
            return false;
        };

        $query = $this->dbh->prepare("SELECT count(*) FROM {$this->config->table_emails_banned} WHERE domain = :domain");
        $query->execute([
            'domain' => (strtolower(explode('@', $email)[1]))
        ]);

        if ($query->fetchColumn() == 0) {
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
    * @return int $uid
    */
    protected function addUser($email, $password, $params = array(), &$use_email_activation)
    {
        $return['error'] = true;

        $query = $this->dbh->prepare("INSERT INTO {$this->config->table_users} (isactive) VALUES (0)");

        if (!$query->execute()) {
            $return['message'] = $this->__lang("system_error") . " #03";
            return $return;
        }

        $uid = $this->dbh->lastInsertId("{$this->config->table_users}_id_seq");
        $email = htmlentities(strtolower($email));

        if ($use_email_activation) {
            $addRequest = $this->addRequest($uid, $email, "activation", $use_email_activation);

            if ($addRequest['error'] == 1) {
                $query = $this->dbh->prepare("DELETE FROM {$this->config->table_users} WHERE id = :id");
                $query_params = [
                    'id' => $uid
                ];
                $query->execute($query_params);

                $return['message'] = $addRequest['message'];
                return $return;
            }

            $isactive = 0;
        } else {
            $isactive = 1;
        }

        $password = $this->getHash($password);

        if (is_array($params)&& count($params) > 0) {
            $customParamsQueryArray = Array();

            foreach($params as $paramKey => $paramValue) {
                $customParamsQueryArray[] = array('value' => $paramKey . ' = ?');
            }

            $setParams = ', ' . implode(', ', array_map(function ($entry) {
                return $entry['value'];
            }, $customParamsQueryArray));
        } else { $setParams = ''; }

        $query = $this->dbh->prepare("UPDATE {$this->config->table_users} SET email = ?, password = ?, isactive = ? {$setParams} WHERE id = ?");

        $bindParams = array_values(array_merge(array($email, $password, $isactive), $params, array($uid)));

        if (!$query->execute($bindParams)) {
            $query = $this->dbh->prepare("DELETE FROM {$this->config->table_users} WHERE id = ?");
            $query->execute(array($uid));
            $return['message'] = $this->__lang("system_error") . " #04";

            return $return;
        }

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
        $query = $this->dbh->prepare("SELECT email, password, isactive FROM {$this->config->table_users} WHERE id = :id");
        $query->execute(['id' => $uid]);

        $data = $query->fetch(\PDO::FETCH_ASSOC);

        if (!$data) {
            return false;
        }

        $data['uid'] = $uid;

        return $data;
    }

    /**
    * Gets public user data for a given UID and returns an array, password will be returned if
    * param $withpassword is TRUE
    * @param int $uid
    * @param bool|false $withpassword
    * @return array $data
    */
    public function getUser($uid, $withpassword = false)
    {
        $query = $this->dbh->prepare("SELECT * FROM {$this->config->table_users} WHERE id = :id");
        $query->execute(['id' => $uid]);

        $data = $query->fetch(\PDO::FETCH_ASSOC);

        if (!$data) {
            return false;
        }

        $data['uid'] = $uid;

        if (!$withpassword)
            unset($data['password']);

        return $data;
    }


    /**
    * Allows a user to delete their account
    * @param int $uid
    * @param string $password
    * @param string $captcha_response = NULL
    * @return array $return
    */
    public function deleteUser($uid, $password, $captcha_response = NULL)
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

        $query = $this->dbh->prepare("DELETE FROM {$this->config->table_users} WHERE id = ?");

        if (!$query->execute(array($uid))) {
            $return['message'] = $this->__lang("system_error") . " #05";

            return $return;
        }

        $query = $this->dbh->prepare("DELETE FROM {$this->config->table_sessions} WHERE uid = ?");

        if (!$query->execute(array($uid))) {
            $return['message'] = $this->__lang("system_error") . " #06";

            return $return;
        }

        $query = $this->dbh->prepare("DELETE FROM {$this->config->table_requests} WHERE uid = ?");

        if (!$query->execute(array($uid))) {
            $return['message'] = $this->__lang("system_error") . " #07";

            return $return;
        }

        $return['error'] = false;
        $return['message'] = $this->__lang("account_deleted");

        return $return;
    }

    /**
    * Creates an activation entry and sends email to user
    * @param int $uid
    * @param string $email
    * @param string $type
    * @param boolean $use_email_activation
    * @return boolean
    */
    protected function addRequest($uid, $email, $type, &$use_email_activation)
    {
        $return['error'] = true;

        if ($type != "activation" && $type != "reset") {
            $return['message'] = $this->__lang("system_error") . " #08";

            return $return;
        }

        // if not set manually, check config data
        if ($use_email_activation === NULL) {
            $use_email_activation = true;
            if ($type == "reset" && $this->config->emailmessage_suppress_reset === true ) {
                $use_email_activation = false;
                $return['error'] = false;

                return $return;
            }

            if ($type == "activation" && $this->config->emailmessage_suppress_activation === true ) {
                $use_email_activation = false;
                $return['error'] = false;

                return $return;
            }
        }

        $query = $this->dbh->prepare("SELECT id, expire FROM {$this->config->table_requests} WHERE uid = :uid AND type = :type");
        $query->execute(['uid' => $uid, 'type' => $type]);

        if ($query->rowCount() > 0) {
            $row = $query->fetch(\PDO::FETCH_ASSOC);

            $expiredate = strtotime($row['expire']);
            $currentdate = strtotime(date("Y-m-d H:i:s"));

            if ($currentdate < $expiredate) {
                $return['message'] = $this->__lang("reset_exists");

                return $return;
            }

            $this->deleteRequest($row['id']);
        }

        if ($type == "activation" && $this->getBaseUser($uid)['isactive'] == 1) {
            $return['message'] = $this->__lang("already_activated");

            return $return;
        }

        $key = $this->getRandomKey(self::TOKEN_LENGTH);
        $expire = date("Y-m-d H:i:s", strtotime($this->config->request_key_expiration));

        $query = $this->dbh->prepare("INSERT INTO {$this->config->table_requests} (uid, token, expire, type) VALUES (:uid, :token, :expire, :type)");

        $query_params = [
            'uid' => $uid,
            'token' => $key,
            'expire' => $expire,
            'type' => $type
        ];

        if (!$query->execute($query_params)) {
            $return['message'] = $this->__lang("system_error") . " #09";

            return $return;
        }

        $request_id = $this->dbh->lastInsertId();

        if ($use_email_activation === true) {
            $sendmail_status = $this->do_SendMail($email, $type, $key);

            if ($sendmail_status['error']) {
                $this->deleteRequest($request_id);

                $return['message'] = $this->__lang("system_error") . $sendmail_status['message'] . " #10";
                return $return;
            }
        }

        $return['error'] = false;

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

        $query = $this->dbh->prepare("SELECT id, uid, expire FROM {$this->config->table_requests} WHERE token = ? AND type = ?");
        $query->execute(array($key, $type));

        if ($query->rowCount() === 0) {
            $this->addAttempt();
            $return['message'] = $this->__lang( $type."key_incorrect" );

            return $return;
        }

        $row = $query->fetch(\PDO::FETCH_ASSOC);

        $expiredate = strtotime($row['expire']);
        $currentdate = strtotime(date("Y-m-d H:i:s"));

        if ($currentdate > $expiredate) {
            $this->addAttempt();
            $this->deleteRequest($row['id']);
            $return['message'] = $this->__lang( $type."key_expired" );

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
        $query = $this->dbh->prepare("DELETE FROM {$this->config->table_requests} WHERE id = :id");
        return $query->execute(['id' => $id]);
    }

    /**
    * Verifies that a password is valid and respects security requirements
    * @param string $password
    * @return array $return
    */
    protected function validatePassword($password) {
        $return['error'] = true;

        if (strlen($password) < (int)$this->config->verify_password_min_length ) {
            $return['message'] = $this->__lang("password_short");

            return $return;
        }

        $return['error'] = false;

        return $return;
    }

    /**
    * Verifies that an email is valid
    * @param string $email
    * @return array $return
    */
    protected function validateEmail($email) {
        $return['error'] = true;

        if (strlen($email) < (int)$this->config->verify_email_min_length ) {
            $return['message'] = $this->__lang("email_short");

            return $return;
        } elseif (strlen($email) > (int)$this->config->verify_email_max_length ) {
            $return['message'] = $this->__lang("email_long");

            return $return;
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $return['message'] = $this->__lang("email_invalid");

            return $return;
        }
        
        if ((int)$this->config->verify_email_use_banlist && $this->isEmailBanned($email)) {
            $this->addAttempt();
            $return['message'] = $this->__lang("email_banned");

            return $return;
        }

        $return['error'] = false;

        return $return;
    }


    /**
    * Allows a user to reset their password after requesting a reset key.
    * @param string $key
    * @param string $password
    * @param string $repeatpassword
    * @param string $captcha_response = NULL
    * @return array $return
    */
    public function resetPass($key, $password, $repeatpassword, $captcha_response = NULL)
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

        if (strlen($key) != self::TOKEN_LENGTH) {
            $return['message'] = $this->__lang("resetkey_invalid");

            return $return;
        }

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
        
        if ($password !== $repeatpassword) {
            // Passwords don't match
            $return['message'] = $this->__lang("newpassword_nomatch");

            return $return;
        }

        $zxcvbn = new Zxcvbn();

        if ($zxcvbn->passwordStrength($password)['score'] < intval($this->config->password_min_score)) {
            $return['message'] = $this->__lang('password_weak');

            return $return;
        }
	    
        $data = $this->getRequest($key, "reset");

        if ($data['error'] == 1) {
            $return['message'] = $data['message'];

            return $return;
        }

        $user = $this->getBaseUser($data['uid']);

        if (!$user) {
            $this->addAttempt();
            $this->deleteRequest($data['id']);
            $return['message'] = $this->__lang("system_error") . " #11";

            return $return;
        }

        if (password_verify($password, $user['password'])) {
            $this->addAttempt();
            $return['message'] = $this->__lang("newpassword_match");

            return $return;
        }

        $password = $this->getHash($password);

        $query = $this->dbh->prepare("UPDATE {$this->config->table_users} SET password = :password WHERE id = :id");
        $query_params = [
            'password' => $password,
            'id' => $data['uid']
        ];
        $query->execute($query_params);

        if ($query->rowCount() == 0) {
            $return['message'] = $this->__lang("system_error") . " #12";

            return $return;
        }

        $this->deleteRequest($data['id']);
        $return['error'] = false;
        $return['message'] = $this->__lang("password_reset");

        return $return;
    }

    /**
    * Recreates activation email for a given email and sends
    * @param string $email
    * @param null $use_email_activation
    * @return array $return
    */
    public function resendActivation($email, $use_email_activation = NULL)
    {
        $return['error'] = true;
        $block_status = $this->isBlocked();

        if ($block_status == "block") {
            $return['message'] = $this->__lang("user_blocked");

            return $return;
        }

        if ($use_email_activation == NULL) {
            $return['message'] = $this->__lang('function_disabled');

            return $return;
        }

        $validateEmail = $this->validateEmail($email);

        if ($validateEmail['error'] == 1) {
            $return['message'] = $validateEmail['message'];

            return $return;
        }

        $query = $this->dbh->prepare("SELECT id FROM {$this->config->table_users} WHERE email = ?");
        $query->execute(array($email));

		if(!$row = $query->fetch(\PDO::FETCH_ASSOC)) {
            $this->addAttempt();
            $return['message'] = $this->__lang("email_incorrect");

            return $return;
        }

        if ($this->getBaseUser($row['id'])['isactive'] == 1) {
            $this->addAttempt();
            $return['message'] = $this->__lang("already_activated");

            return $return;
        }

        $addRequest = $this->addRequest($row['id'], $email, "activation", $use_email_activation);

        if ($addRequest['error'] == 1) {
            $this->addAttempt();
            $return['message'] = $addRequest['message'];

            return $return;
        }

        $return['error'] = false;
        $return['message'] = $this->__lang("activation_sent");
        return $return;
    }

    /**
    * Changes a user's password
    * @param int $uid
    * @param string $currpass
    * @param string $newpass
    * @param string $repeatnewpass
    * @param string $captcha_response = NULL
    * @return array $return
    */
    public function changePassword($uid, $currpass, $newpass, $repeatnewpass, $captcha_response = NULL)
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

        $query = $this->dbh->prepare("UPDATE {$this->config->table_users} SET password = ? WHERE id = ?");
        $query->execute(array($newpass, $uid));

        $return['error'] = false;
        $return['message'] = $this->__lang("password_changed");

        return $return;
    }

    /**
    * Changes a user's email
    * @param int $uid
    * @param string $email
    * @param string $password
    * @param string $captcha = NULL
    * @return array $return
    */
    public function changeEmail($uid, $email, $password, $captcha = NULL)
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

        $query = $this->dbh->prepare("UPDATE {$this->config->table_users} SET email = ? WHERE id = ?");
        $query->execute(array($email, $uid));

        if ($query->rowCount() == 0) {
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
        $query = $this->dbh->prepare("SELECT count(*) FROM {$this->config->table_attempts} WHERE ip = :ip"); // INET_ATON(:ip)
        $query->execute(['ip' => $ip]);
        $attempts = $query->fetchColumn();

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

        $query = $this->dbh->prepare("INSERT INTO {$this->config->table_attempts} (ip, expiredate) VALUES (:ip, :expiredate)"); // INET_ATON(:ip)
        return $query->execute([
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
        $chars = "A1B2C3D4E5F6G7H8I9J0K1L2M3N4O5P6Q7R8S9T0U1V2W3X4Y5Z6a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0u1v2w3x4y5z6";
        $key = "";

        for ($i = 0; $i < $length; $i++) {
            $key .= $chars{mt_rand(0, strlen($chars) - 1)};
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
     * @return boolean false if no cookie
     */
    public function getSessionHash(){
        return isset($_COOKIE[$this->config->cookie_name]) ? $_COOKIE[$this->config->cookie_name] : false;

        // PHP7+ : return $_COOKIE[$this->config->cookie_name] ?? null;
    }

    /**
     * Returns is user logged in
     * @return boolean
     */
    public function isLogged() {
        if ($this->islogged === NULL) {
            $this->islogged = $this->checkSession($this->getSessionHash());
        }
        return $this->islogged;
    }

   /**
    * Gets user data for current user (from cookie) and returns an array, password is not returned
    * @return array $data
    * @return boolean false if no current user
    */
    public function getCurrentUser()
    {
        if ($this->currentuser === NULL) {
            $hash = $this->getSessionHash();
            if ($hash === false) {
                return false;
            }

            $uid = $this->getSessionUID($hash);
            if ($uid === false) {
                return false;
            }

            $this->currentuser = $this->getUser($uid);
        }
        return $this->currentuser;
    }

    /**
     * Compare user's password with given password
     * @param int $userid
     * @param string $password_for_check
     * @return bool
     */
    public function comparePasswords($userid, $password_for_check)
    {
        $query = $this->dbh->prepare("SELECT password FROM {$this->config->table_users} WHERE id = ?");
        $query->execute(array($userid));

        $data = $query->fetch(\PDO::FETCH_ASSOC);

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
    
        if (password_needs_rehash($hash, PASSWORD_DEFAULT, array('cost' => $this->config->bcrypt_cost))) {
            $hash = $this->getHash($password);
    
            $query = $this->dbh->prepare("UPDATE {$this->config->table_users} SET password = ? WHERE id = ?");
            $query->execute(array($hash, $uid));
        }
    
        return true;
    }

    /**
     * Translates key-message to defined language
     *
     * @param $key
     * @return mixed
     */
    public function __lang($key)
    {
        return array_key_exists($key, $this->messages_dictionary) ? $this->messages_dictionary[$key] : $key;
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

        // Check configuration for SMTP parameters
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

            }

            //Recipients
            $mail->setFrom($this->config->site_email, $this->config->site_name);
            $mail->addAddress($email);

            $mail->CharSet = $this->config->mail_charset;

            //Content
            $mail->isHTML(true);

            if ($type == 'activation') {
                $mail->Subject = sprintf($this->__lang('email_activation_subject'), $this->config->site_name);
                $mail->Body = sprintf($this->__lang('email_activation_body'), $this->config->site_url, $this->config->site_activation_page, $key);
                $mail->AltBody = sprintf($this->__lang('email_activation_altbody'), $this->config->site_url, $this->config->site_activation_page, $key);
            } elseif ($type == 'reset') {
                $mail->Subject = sprintf($this->__lang('email_reset_subject'), $this->config->site_name);
                $mail->Body = sprintf($this->__lang('email_reset_body'), $this->config->site_url, $this->config->site_password_reset_page, $key);
                $mail->AltBody = sprintf($this->__lang('email_reset_altbody'), $this->config->site_url, $this->config->site_password_reset_page, $key);
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
    protected function updateUser($uid, $params)
    {
        $setParams = '';
        if (is_array($params) && count($params) > 0) {
            $customParamsQueryArray = Array();

            foreach ($params as $paramKey => $paramValue) {
                $customParamsQueryArray[] = array('value' => $paramKey . ' = ?');
            }

            $setParams = implode(', ', array_map(function ($entry) {
                return $entry['value'];
            }, $customParamsQueryArray));
        }
        $query = $this->dbh->prepare("UPDATE {$this->config->table_users} SET {$setParams} WHERE id = ?");
        $bindParams = array_values(array_merge($params, array($uid)));

        if (!$query->execute($bindParams)) {
            $return['message'] = $this->__lang("system_error") . " #04";
            return $return;
        }
        $return['error'] = false;
        $return['message'] = 'Ok.';
        return $return;
    }

    /**
     * Returns current user UID if logged or FALSE otherwise. Not optimised method.
     * @todo: optimise
     *
     * @return int
     */
    public function getCurrentUID()
    {
        return $this->getSessionUID($this->getSessionHash());
    }


}
