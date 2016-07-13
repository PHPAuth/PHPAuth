<?php

namespace PHPAuth;

use ZxcvbnPhp\Zxcvbn;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Auth class
 * Required PHP 5.4 and above.
 */

class Auth
{
    protected $dbh;
    public $config;
    public $lang;

    /**
     * Initiates database connection
     */
    public function __construct(\PDO $dbh, $config, $language = "en_GB")
    {
        $this->dbh = $dbh;
        $this->config = $config;

        if (version_compare(phpversion(), '5.4.0', '<')) {
            die('PHP 5.4.0 required for PHPAuth engine!');
        }

        if (version_compare(phpversion(), '5.5.0', '<')) {
            require("files/password.php");
        }

        // Load language
        require "languages/{$language}.php";
        $this->lang = $lang;

        date_default_timezone_set($this->config->site_timezone);
    }

    /**
     * Logs a user in
     * @param string $email
     * @param string $password
     * @param int $remember
     * @param string $captcha = NULL
     * @return array $return
     */
    public function login($email, $password, $remember = 0, $captcha = NULL)
    {
        $return['error'] = true;

        $block_status = $this->isBlocked();

        if ($block_status == "verify") {
            if ($this->checkCaptcha($captcha) == false) {
                $return['message'] = $this->lang["user_verify_failed"];

                return $return;
            }
        }

        if ($block_status == "block") {
            $return['message'] = $this->lang["user_blocked"];
            return $return;
        }

        $validateEmail = $this->validateEmail($email);
        $validatePassword = $this->validatePassword($password);

        if ($validateEmail['error'] == 1) {
            $this->addAttempt();
            $return['message'] = $this->lang["email_password_invalid"];

            return $return;
        } elseif ($validatePassword['error'] == 1) {
            $this->addAttempt();
            $return['message'] = $this->lang["email_password_invalid"];

            return $return;
        } elseif ($remember != 0 && $remember != 1) {
            $this->addAttempt();
            $return['message'] = $this->lang["remember_me_invalid"];

            return $return;
        }

        $uid = $this->getUID(strtolower($email));

        if (!$uid) {
            $this->addAttempt();
            $return['message'] = $this->lang["email_password_incorrect"];

            return $return;
        }

        $user = $this->getBaseUser($uid);

        if (!password_verify($password, $user['password'])) {
            $this->addAttempt();
            $return['message'] = $this->lang["email_password_incorrect"];

            return $return;
        }

        if ($user['isactive'] != 1) {
            $this->addAttempt();
            $return['message'] = $this->lang["account_inactive"];

            return $return;
        }

        $sessiondata = $this->addSession($user['uid'], $remember);

        if ($sessiondata == false) {
            $return['message'] = $this->lang["system_error"] . " #01";

            return $return;
        }

        $return['error'] = false;
        $return['message'] = $this->lang["logged_in"];

        $return['hash'] = $sessiondata['hash'];
        $return['expire'] = $sessiondata['expiretime'];

        return $return;
    }

    /**
    * Creates a new user, adds them to database
    * @param string $email
    * @param string $password
    * @param string $repeatpassword
    * @param array  $params
    * @param string $captcha = NULL
    * @param bool $sendmail = NULL
    * @return array $return
    */

    public function register($email, $password, $repeatpassword, $params = Array(), $captcha = NULL, $sendmail = NULL)
    {
        $return['error'] = true;
        $block_status = $this->isBlocked();

        if ($block_status == "verify") {
            if ($this->checkCaptcha($captcha) == false) {
                $return['message'] = $this->lang["user_verify_failed"];

                return $return;
            }
        }

        if ($block_status == "block") {
            $return['message'] = $this->lang["user_blocked"];

            return $return;
        }

        if ($password !== $repeatpassword) {
            $return['message'] = $this->lang["password_nomatch"];

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
            $return['message'] = $this->lang['password_weak'];

            return $return;
        }

        if ($this->isEmailTaken($email)) {
            $this->addAttempt();
            $return['message'] = $this->lang["email_taken"];

            return $return;
        }

        $addUser = $this->addUser($email, $password, $params, $sendmail);

        if ($addUser['error'] != 0) {
            $return['message'] = $addUser['message'];

            return $return;
        }

        $return['error'] = false;
        $return['message'] = ($sendmail == true ? $this->lang["register_success"] : $this->lang['register_success_emailmessage_suppressed'] );

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
            $return['message'] = $this->lang["user_blocked"];

            return $return;
        }

        if (strlen($key) !== 20) {
            $this->addAttempt();
            $return['message'] = $this->lang["activationkey_invalid"];

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
            $return['message'] = $this->lang["system_error"] . " #02";

            return $return;
        }

        $query = $this->dbh->prepare("UPDATE {$this->config->table_users} SET isactive = ? WHERE id = ?");
        $query->execute(array(1, $getRequest['uid']));

        $this->deleteRequest($getRequest['id']);

        $return['error'] = false;
        $return['message'] = $this->lang["account_activated"];

        return $return;
    }

    /**
    * Creates a reset key for an email address and sends email
    * @param string $email
    * @return array $return
    */

    public function requestReset($email, $sendmail = NULL)
    {
        $return['error'] = true;
        $block_status = $this->isBlocked();

        if ($block_status == "block") {
            $return['message'] = $this->lang["user_blocked"];

            return $return;
        }

        $validateEmail = $this->validateEmail($email);

        if ($validateEmail['error'] == 1) {
            $return['message'] = $this->lang["email_invalid"];

            return $return;
        }

        $query = $this->dbh->prepare("SELECT id FROM {$this->config->table_users} WHERE email = ?");
        $query->execute(array($email));

        if ($query->rowCount() == 0) {
            $this->addAttempt();

            $return['message'] = $this->lang["email_incorrect"];

            return $return;
        }

        $addRequest = $this->addRequest($query->fetch(\PDO::FETCH_ASSOC)['id'], $email, "reset", $sendmail);

        if ($addRequest['error'] == 1) {
            $this->addAttempt();
            $return['message'] = $addRequest['message'];

            return $return;
        }

        $return['error'] = false;
        $return['message'] = ($sendmail == true ? $this->lang["reset_requested"] : $this->lang['reset_requested_emailmessage_suppressed']);

        return $return;
    }

    /**
    * Logs out the session, identified by hash
    * @param string $hash
    * @return boolean
    */

    public function logout($hash)
    {
        if (strlen($hash) != 40) {
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
    }

    /**
    * Gets UID for a given email address and returns an array
    * @param string $email
    * @return array $uid
    */


    public function getUID($email)
    {
        $query = $this->dbh->prepare("SELECT id FROM {$this->config->table_users} WHERE email = ?");
        $query->execute(array($email));

        if ($query->rowCount() == 0) {
            return false;
        }

        return $query->fetch(\PDO::FETCH_ASSOC)['id'];
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
        $agent = $_SERVER['HTTP_USER_AGENT'];

        $this->deleteExistingSessions($uid);

        if ($remember == true) {
            $data['expire'] = date("Y-m-d H:i:s", strtotime($this->config->cookie_remember));
            $data['expiretime'] = strtotime($data['expire']);
        } else {
            $data['expire'] = date("Y-m-d H:i:s", strtotime($this->config->cookie_forget));
            $data['expiretime'] = 0;
        }

        $data['cookie_crc'] = sha1($data['hash'] . $this->config->site_key);

        $query = $this->dbh->prepare("INSERT INTO {$this->config->table_sessions} (uid, hash, expiredate, ip, agent, cookie_crc) VALUES (?, ?, ?, ?, ?, ?)");

        if (!$query->execute(array($uid, $data['hash'], $data['expire'], $ip, $agent, $data['cookie_crc']))) {
            return false;
        }

        $data['expire'] = strtotime($data['expire']);

        return $data;
    }

    /**
    * Removes all existing sessions for a given UID
    * @param int $uid
    * @return boolean
    */

    protected function deleteExistingSessions($uid)
    {
        $query = $this->dbh->prepare("DELETE FROM {$this->config->table_sessions} WHERE uid = ?");
        $query->execute(array($uid));

        return $query->rowCount() == 1;
    }

    /**
    * Removes a session based on hash
    * @param string $hash
    * @return boolean
    */

    protected function deleteSession($hash)
    {
        $query = $this->dbh->prepare("DELETE FROM {$this->config->table_sessions} WHERE hash = ?");
        $query->execute(array($hash));

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
            $return['message'] = $this->lang["user_blocked"];
            return false;
        }

        if (strlen($hash) != 40) {
            return false;
        }

        $query = $this->dbh->prepare("SELECT id, uid, expiredate, ip, agent, cookie_crc FROM {$this->config->table_sessions} WHERE hash = ?");
        $query->execute(array($hash));

        if ($query->rowCount() == 0) {
            return false;
        }

        $row = $query->fetch(\PDO::FETCH_ASSOC);
        $sid = $row['id'];
        $uid = $row['uid'];
        $expiredate = strtotime($row['expiredate']);
        $currentdate = strtotime(date("Y-m-d H:i:s"));
        $db_ip = $row['ip'];
        $db_agent = $row['agent'];
        $db_cookie = $row['cookie_crc'];

        if ($currentdate > $expiredate) {
            $this->deleteExistingSessions($uid);

            return false;
        }

        if ($ip != $db_ip) {
            return false;
        }

        if ($db_cookie == sha1($hash . $this->config->site_key)) {
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
        $query = $this->dbh->prepare("SELECT uid FROM {$this->config->table_sessions} WHERE hash = ?");
        $query->execute(array($hash));

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
        $query = $this->dbh->prepare("SELECT count(*) FROM {$this->config->table_users} WHERE email = ?");
        $query->execute(array($email));

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
    * @return int $uid
    */

    protected function addUser($email, $password, $params = array(), &$sendmail)
    {
        $return['error'] = true;

        $query = $this->dbh->prepare("INSERT INTO {$this->config->table_users} VALUES ()");

        if (!$query->execute()) {
            $return['message'] = $this->lang["system_error"] . " #03";
            return $return;
        }

        $uid = $this->dbh->lastInsertId();
        $email = htmlentities(strtolower($email));

        if ($sendmail) {
            $addRequest = $this->addRequest($uid, $email, "activation", $sendmail);

            if ($addRequest['error'] == 1) {
                $query = $this->dbh->prepare("DELETE FROM {$this->config->table_users} WHERE id = ?");
                $query->execute(array($uid));
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
            $return['message'] = $this->lang["system_error"] . " #04";

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
        $query = $this->dbh->prepare("SELECT email, password, isactive FROM {$this->config->table_users} WHERE id = ?");
        $query->execute(array($uid));

        if ($query->rowCount() == 0) {
            return false;
        }

        $data = $query->fetch(\PDO::FETCH_ASSOC);

        if (!$data) {
            return false;
        }

        $data['uid'] = $uid;

        return $data;
    }

    /**
    * Gets public user data for a given UID and returns an array, password is not returned
    * @param int $uid
    * @return array $data
    */

    public function getUser($uid)
    {
        $query = $this->dbh->prepare("SELECT * FROM {$this->config->table_users} WHERE id = ?");
        $query->execute(array($uid));

        if ($query->rowCount() == 0) {
            return false;
        }

        $data = $query->fetch(\PDO::FETCH_ASSOC);

        if (!$data) {
            return false;
        }

        $data['uid'] = $uid;
        unset($data['password']);

        return $data;
    }

    /**
    * Allows a user to delete their account
    * @param int $uid
    * @param string $password
    * @param string $captcha = NULL
    * @return array $return
    */

    public function deleteUser($uid, $password, $captcha = NULL)
    {
        $return['error'] = true;

        $block_status = $this->isBlocked();
        if ($block_status == "verify") {
            if ($this->checkCaptcha($captcha) == false) {
                $return['message'] = $this->lang["user_verify_failed"];

                return $return;
            }
        }

        if ($block_status == "block") {
            $return['message'] = $this->lang["user_blocked"];

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
            $return['message'] = $this->lang["password_incorrect"];

            return $return;
        }

        $query = $this->dbh->prepare("DELETE FROM {$this->config->table_users} WHERE id = ?");

        if (!$query->execute(array($uid))) {
            $return['message'] = $this->lang["system_error"] . " #05";

            return $return;
        }

        $query = $this->dbh->prepare("DELETE FROM {$this->config->table_sessions} WHERE uid = ?");

        if (!$query->execute(array($uid))) {
            $return['message'] = $this->lang["system_error"] . " #06";

            return $return;
        }

        $query = $this->dbh->prepare("DELETE FROM {$this->config->table_requests} WHERE uid = ?");

        if (!$query->execute(array($uid))) {
            $return['message'] = $this->lang["system_error"] . " #07";

            return $return;
        }

        $return['error'] = false;
        $return['message'] = $this->lang["account_deleted"];

        return $return;
    }

    /**
    * Creates an activation entry and sends email to user
    * @param int $uid
    * @param string $email
    * @param string $type
    * @param boolean $sendmail = NULL
    * @return boolean
    */

    protected function addRequest($uid, $email, $type, &$sendmail)
    {
        $return['error'] = true;

        if ($type != "activation" && $type != "reset") {
            $return['message'] = $this->lang["system_error"] . " #08";

            return $return;
        }

        // if not set manually, check config data
        if ($sendmail === NULL) {
            $sendmail = true;
            if ($type == "reset" && $this->config->emailmessage_suppress_reset === true ) {
                $sendmail = false;
                $return['error'] = false;

                return $return;
            }

            if ($type == "activation" && $this->config->emailmessage_suppress_activation === true ) {
                $sendmail = false;
                $return['error'] = false;

                return $return;
            }
        }

        $query = $this->dbh->prepare("SELECT id, expire FROM {$this->config->table_requests} WHERE uid = ? AND type = ?");
        $query->execute(array($uid, $type));

        if ($query->rowCount() > 0) {
            $row = $query->fetch(\PDO::FETCH_ASSOC);

            $expiredate = strtotime($row['expire']);
            $currentdate = strtotime(date("Y-m-d H:i:s"));

            if ($currentdate < $expiredate) {
                $return['message'] = $this->lang["reset_exists"];

                return $return;
            }

            $this->deleteRequest($row['id']);
        }

        if ($type == "activation" && $this->getBaseUser($uid)['isactive'] == 1) {
            $return['message'] = $this->lang["already_activated"];

            return $return;
        }

        $key = $this->getRandomKey(20);
        $expire = date("Y-m-d H:i:s", strtotime($this->config->request_key_expiration));

        $query = $this->dbh->prepare("INSERT INTO {$this->config->table_requests} (uid, rkey, expire, type) VALUES (?, ?, ?, ?)");

        if (!$query->execute(array($uid, $key, $expire, $type))) {
            $return['message'] = $this->lang["system_error"] . " #09";

            return $return;
        }

        $request_id = $this->dbh->lastInsertId();

        if ($sendmail === true) {
            // Check configuration for SMTP parameters
            $mail = new PHPMailer;
			$mail->CharSet = $this->config->mail_charset;
            if ($this->config->smtp) {
                $mail->isSMTP();
                $mail->Host = $this->config->smtp_host;
                $mail->SMTPAuth = $this->config->smtp_auth;
                if (!is_null($this->config->smtp_auth)) {
                    $mail->Username = $this->config->smtp_username;
                    $mail->Password = $this->config->smtp_password;
                }
                $mail->Port = $this->config->smtp_port;

                if (!is_null($this->config->smtp_security)) {
                    $mail->SMTPSecure = $this->config->smtp_security;
                }
            }

            $mail->From = $this->config->site_email;
            $mail->FromName = $this->config->site_name;
            $mail->addAddress($email);
            $mail->isHTML(true);

            if ($type == "activation") {
                    $mail->Subject = sprintf($this->lang['email_activation_subject'], $this->config->site_name);
                    $mail->Body = sprintf($this->lang['email_activation_body'], $this->config->site_url, $this->config->site_activation_page, $key);
                    $mail->AltBody = sprintf($this->lang['email_activation_altbody'], $this->config->site_url, $this->config->site_activation_page, $key);
            } else {
                $mail->Subject = sprintf($this->lang['email_reset_subject'], $this->config->site_name);
                $mail->Body = sprintf($this->lang['email_reset_body'], $this->config->site_url, $this->config->site_password_reset_page, $key);
                $mail->AltBody = sprintf($this->lang['email_reset_altbody'], $this->config->site_url, $this->config->site_password_reset_page, $key);
            }

            if (!$mail->send()) {
                $this->deleteRequest($request_id);
                $return['message'] = $this->lang["system_error"] . " #10";

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

        $query = $this->dbh->prepare("SELECT id, uid, expire FROM {$this->config->table_requests} WHERE rkey = ? AND type = ?");
        $query->execute(array($key, $type));

        if ($query->rowCount() === 0) {
            $this->addAttempt();
            $return['message'] = $this->lang[$type."key_incorrect"];

            return $return;
        }

        $row = $query->fetch();

        $expiredate = strtotime($row['expire']);
        $currentdate = strtotime(date("Y-m-d H:i:s"));

        if ($currentdate > $expiredate) {
            $this->addAttempt();
            $this->deleteRequest($row['id']);
            $return['message'] = $this->lang[$type."key_expired"];

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
        $query = $this->dbh->prepare("DELETE FROM {$this->config->table_requests} WHERE id = ?");

        return $query->execute(array($id));
    }

    /**
    * Verifies that a password is valid and respects security requirements
    * @param string $password
    * @return array $return
    */

    protected function validatePassword($password) {
        $return['error'] = true;

        if (strlen($password) < (int)$this->config->verify_password_min_length ) {
            $return['message'] = $this->lang["password_short"];

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
            $return['message'] = $this->lang["email_short"];

            return $return;
        } elseif (strlen($email) > (int)$this->config->verify_email_max_length ) {
            $return['message'] = $this->lang["email_long"];

            return $return;
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $return['message'] = $this->lang["email_invalid"];

            return $return;
        }

        if ( (int)$this->config->verify_email_use_banlist ) {
            $bannedEmails = json_decode(file_get_contents(__DIR__ . "/files/domains.json"));

            if (in_array(strtolower(explode('@', $email)[1]), $bannedEmails)) {
                $return['message'] = $this->lang["email_banned"];

                return $return;
            }
        }

        $return['error'] = false;

        return $return;
    }


    /**
    * Allows a user to reset their password after requesting a reset key.
    * @param string $key
    * @param string $password
    * @param string $repeatpassword
    * @param string $captcha = NULL
    * @return array $return
    */

    public function resetPass($key, $password, $repeatpassword, $captcha = NULL)
    {
        $return['error'] = true;
        $block_status = $this->isBlocked();

        if ($block_status == "verify") {
            if ($this->checkCaptcha($captcha) == false) {
                $return['message'] = $this->lang["user_verify_failed"];

                return $return;
            }
        }

        if ($block_status == "block") {
            $return['message'] = $this->lang["user_blocked"];

            return $return;
        }

        if (strlen($key) != 20) {
            $return['message'] = $this->lang["resetkey_invalid"];

            return $return;
        }

        $validatePassword = $this->validatePassword($password);

        if ($validatePassword['error'] == 1) {
            $return['message'] = $validatePassword['message'];
            return $return;
        }

        if ($password !== $repeatpassword) {
            // Passwords don't match
            $return['message'] = $this->lang["newpassword_nomatch"];

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
            $return['message'] = $this->lang["system_error"] . " #11";

            return $return;
        }

        if (password_verify($password, $user['password'])) {
            $this->addAttempt();
            $return['message'] = $this->lang["newpassword_match"];

            return $return;
        }

        $password = $this->getHash($password);
        $query = $this->dbh->prepare("UPDATE {$this->config->table_users} SET password = ? WHERE id = ?");
        $query->execute(array($password, $data['uid']));

        if ($query->rowCount() == 0) {
            $return['message'] = $this->lang["system_error"] . " #12";

            return $return;
        }

        $this->deleteRequest($data['id']);
        $return['error'] = false;
        $return['message'] = $this->lang["password_reset"];

        return $return;
    }

    /**
    * Recreates activation email for a given email and sends
    * @param string $email
    * @return array $return
    */

    public function resendActivation($email, $sendmail = NULL)
    {
        $return['error'] = true;
        $block_status = $this->isBlocked();

        if ($block_status == "block") {
            $return['message'] = $this->lang["user_blocked"];

            return $return;
        }

        if ($sendmail == NULL) {
            $return['message'] = $this->lang['function_disabled'];

            return $return;
        }

        $validateEmail = $this->validateEmail($email);

        if ($validateEmail['error'] == 1) {
            $return['message'] = $validateEmail['message'];

            return $return;
        }

        $query = $this->dbh->prepare("SELECT id FROM {$this->config->table_users} WHERE email = ?");
        $query->execute(array($email));

        if ($query->rowCount() == 0) {
            $this->addAttempt();
            $return['message'] = $this->lang["email_incorrect"];

            return $return;
        }

        $row = $query->fetch(\PDO::FETCH_ASSOC);

        if ($this->getBaseUser($row['id'])['isactive'] == 1) {
            $this->addAttempt();
            $return['message'] = $this->lang["already_activated"];

            return $return;
        }

        $addRequest = $this->addRequest($row['id'], $email, "activation", $sendmail);

        if ($addRequest['error'] == 1) {
            $this->addAttempt();
            $return['message'] = $addRequest['message'];

            return $return;
        }

        $return['error'] = false;
        $return['message'] = $this->lang["activation_sent"];
        return $return;
    }

    /**
    * Changes a user's password
    * @param int $uid
    * @param string $currpass
    * @param string $newpass
    * @param string $repeatnewpass
    * @param string $captcha = NULL
    * @return array $return
    */
    public function changePassword($uid, $currpass, $newpass, $repeatnewpass, $captcha = NULL)
    {
        $return['error'] = true;
        $block_status = $this->isBlocked();

        if ($block_status == "verify") {
            if ($this->checkCaptcha($captcha) == false) {
                $return['message'] = $this->lang["user_verify_failed"];
                return $return;
            }
        }

        if ($block_status == "block") {
            $return['message'] = $this->lang["user_blocked"];

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
            $return['message'] = $this->lang["newpassword_nomatch"];

            return $return;
        }

        $zxcvbn = new Zxcvbn();

        if ($zxcvbn->passwordStrength($newpass)['score'] < intval($this->config->password_min_score)) {
            $return['message'] = $this->lang['password_weak'];

            return $return;
        }

        $user = $this->getBaseUser($uid);

        if (!$user) {
            $this->addAttempt();
            $return['message'] = $this->lang["system_error"] . " #13";

            return $return;
        }

        if (!password_verify($currpass, $user['password'])) {
            $this->addAttempt();
            $return['message'] = $this->lang["password_incorrect"];

            return $return;
        }

        $newpass = $this->getHash($newpass);

        $query = $this->dbh->prepare("UPDATE {$this->config->table_users} SET password = ? WHERE id = ?");
        $query->execute(array($newpass, $uid));

        $return['error'] = false;
        $return['message'] = $this->lang["password_changed"];

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
                $return['message'] = $this->lang["user_verify_failed"];

                return $return;
            }
        }

        if ($block_status == "block") {
            $return['message'] = $this->lang["user_blocked"];

            return $return;
        }

        $validateEmail = $this->validateEmail($email);

        if ($validateEmail['error'] == 1) {
            $return['message'] = $validateEmail['message'];

            return $return;
        }

        $validatePassword = $this->validatePassword($password);

        if ($validatePassword['error'] == 1) {
            $return['message'] = $this->lang["password_notvalid"];

            return $return;
        }

        $user = $this->getBaseUser($uid);

        if (!$user) {
            $this->addAttempt();
            $return['message'] = $this->lang["system_error"] . " #14";

            return $return;
        }

        if (!password_verify($password, $user['password'])) {
            $this->addAttempt();
            $return['message'] = $this->lang["password_incorrect"];

            return $return;
        }

        if ($email == $user['email']) {
            $this->addAttempt();
            $return['message'] = $this->lang["newemail_match"];

            return $return;
        }

        $query = $this->dbh->prepare("UPDATE {$this->config->table_users} SET email = ? WHERE id = ?");
        $query->execute(array($email, $uid));

        if ($query->rowCount() == 0) {
            $return['message'] = $this->lang["system_error"] . " #15";

            return $return;
        }

        $return['error'] = false;
        $return['message'] = $this->lang["email_changed"];

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
        $query = $this->dbh->prepare("SELECT count(*) FROM {$this->config->table_attempts} WHERE ip = ?");
        $query->execute(array($ip));
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
    * Adds an attempt to database
    * @return boolean
    */

    protected function addAttempt()
    {
        $ip = $this->getIp();
        $attempt_expiredate = date("Y-m-d H:i:s", strtotime($this->config->attack_mitigation_time));
        $query = $this->dbh->prepare("INSERT INTO {$this->config->table_attempts} (ip, expiredate) VALUES (?, ?)");

        return $query->execute(array($ip, $attempt_expiredate));
    }

    /**
    * Deletes all attempts for a given IP from database
    * @param string $ip
        * @param boolean $all = false
    * @return boolean
    */

    protected function deleteAttempts($ip, $all = false)
    {
        if ($all==true) {
            $query = $this->dbh->prepare("DELETE FROM {$this->config->table_attempts} WHERE ip = ?");

            return $query->execute(array($ip));
        }

        $query = $this->dbh->prepare("SELECT id, expiredate FROM {$this->config->table_attempts} WHERE ip = ?");
        $query->execute(array($ip));

        while ($row = $query->fetch(\PDO::FETCH_ASSOC)) {
            $expiredate = strtotime($row['expiredate']);
            $currentdate = strtotime(date("Y-m-d H:i:s"));
            if ($currentdate > $expiredate) {
                $queryDel = $this->dbh->prepare("DELETE FROM {$this->config->table_attempts} WHERE id = ?");
                $queryDel->execute(array($row['id']));
            }
        }
    }

    /**
    * Returns a random string of a specified length
    * @param int $length
    * @return string $key
    */
    public function getRandomKey($length = 20)
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
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
           return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
           return $_SERVER['REMOTE_ADDR'];
        }
    }

    /**
    * Returns is user logged in
    * @return boolean
    */
    public function isLogged() {
        return (isset($_COOKIE[$this->config->cookie_name]) && $this->checkSession($_COOKIE[$this->config->cookie_name]));
    }

    /**
     * Returns current session hash
     * @return string
     */
    public function getSessionHash(){
        return $_COOKIE[$this->config->cookie_name];
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

        if ($query->rowCount() == 0) {
            return false;
        }

        $data = $query->fetch(\PDO::FETCH_ASSOC);

        if (!$data) {
            return false;
        }

        return password_verify($password_for_check, $data['password']);
    }
}
