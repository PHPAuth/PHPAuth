<?php

namespace PHPAuth;

use Exception;
use PDO;
use PDOException;
use PHPAuth\Core\Result;
use PHPMailer\PHPMailer\PHPMailer;
use ReCaptcha\ReCaptcha;
use RuntimeException;
use stdClass;
use function setcookie;

class Auth implements AuthInterface
{
    use Helpers;

    /**
     * @var PDO $dbh
     */
    protected $dbh;

    /**
     * @var stdClass Config
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
     * @var stdClass $messages_dictionary
     */
    protected $messages_dictionary = [];

    /**
     * @var stdClass $recaptcha_config
     */
    protected $recaptcha_config = [];

    /**
     * Custom E-Mail validator callback
     *
     * @var callable
     */
    public $emailValidator;

    /**
     * Custom Password validator callback
     *
     * @var callable
     */
    public $passwordValidator;

    /**
     * Custom Mailer callback
     *
     * @var callable
     */
    public $customMailer;

    public function __construct($dbh, Config $config)
    {
        $this->dbh = $dbh;
        $this->config = $config;

        $this->recaptcha_config = $this->config->recaptcha;
        $this->messages_dictionary = $this->config->dictionary;

        $this->emailValidator = $this->config->emailValidator;
        $this->passwordValidator = $this->config->passwordValidator;
        $this->customMailer = $this->config->customMailer;

        if (!empty($this->config->site_timezone)) {
            date_default_timezone_set($this->config->site_timezone);
        }

        $this->isAuthenticated = $this->isLogged();
    }

    public function login(string $email, string $password, int $remember = 0, string $captcha_response = ''):array
    {
        $return          = [];
        $return['error'] = true;
        $return['hash']  = '';

        $block_status = $this->isBlocked();

        if ($block_status == 'verify') {
            // checkCaptcha always return true!
            if (!$this->checkCaptcha($captcha_response)) {
                $return['message'] = $this->__lang('captcha.verify_code_invalid');
                return $return;
            }
        }

        if ($block_status == 'block') {
            $return['message'] = $this->__lang('user.temporary_banned');
            return $return;
        }

        $validateEmail = $this->validateEmail($email);
        $validatePassword = $this->validatePasswordLength($password);

        if ($validateEmail['error'] == 1) {
            $this->addAttempt();

            $return['message'] = $validateEmail['message']; // ?? $this->__lang("account_email_invalid");

            return $return;
        } elseif ($validatePassword['error'] == 1) {
            $this->addAttempt();
            $return['message'] = $validatePassword['message']; // ?? $this->__lang("account_password_invalid");

            return $return;
        } elseif ($remember != 0 && $remember != 1) {
            $this->addAttempt();
            $return['message'] = $this->__lang('remember_me_invalid');      //@todo => login.remember_me_invalid_value

            return $return;
        }

        //@todo: объединить getUID и getBaseUser в один вызов.

        $uid = $this->getUID($email); // Gets UID for a given email address or zero if email not found

        if (!$uid) {
            $this->addAttempt();
            $return['message'] = $this->__lang('account.not_found');

            return $return;
        }

        $user = $this->getBaseUser($uid); // Gets basic user data for a given UID

        if (!$this->password_verify_with_rehash($password, $user['password'], $uid)) {
            $this->addAttempt();
            $return['message'] = $this->__lang('account.no_pair_user_and_password');
            return $return;
        }

        if ($user['isactive'] != 1) {
            $this->addAttempt();
            $return['message'] = $this->__lang('account.not_activated');

            return $return;
        }

        $sessiondata = $this->addSession($user['uid'], $remember);

        if (!$sessiondata) {
            $return['message'] = $this->__lang('system.error') . ' #01';

            return $return;
        }

        $return['error'] = false;
        $return['message'] = $this->__lang('logged_in'); // => user.logged_in

        $return['hash'] = $sessiondata['hash'];
        $return['expire'] = $sessiondata['expire'];

        $return['cookie_name'] = $this->config->cookie_name;

        return $return;
    }

    public function register(string $email, string $password, string $repeat_password, array $params = [], string $captcha_response = '', bool $use_email_activation = false):array
    {
        $return = [];
        $return['error'] = true;
        $block_status = $this->isBlocked();

        if ($block_status == 'verify') {
            if (!$this->checkCaptcha($captcha_response)) {
                $return['message'] = $this->__lang('captcha.verify_code_invalid');

                // return new Result(false, '', $this->__lang('captcha.verify_code_invalid'));

                return $return;
            }
        }

        if ($block_status == 'block') {
            $return['message'] = $this->__lang('user.temporary_banned');

            // return new Result(false, '', $this->__lang('user.temporary_banned'));

            return $return;
        }

        if ($password !== $repeat_password) {
            $return['message'] = $this->__lang('password_nomatch'); // => password.not_equal

            return $return;
        }

        // Validate email
        $validateEmail = $this->validateEmail($email);

        if ($validateEmail['error'] == 1) {
            $return['message'] = $validateEmail['message'];

            return $return;
        }

        // Validate password
        $validatePassword = $this->validatePasswordLength($password);

        if ($validatePassword['error'] == 1) {
            $return['message'] = $validatePassword['message'];

            return $return;
        }

        if (!$this->isPasswordStrong($password)) {
            $return['message'] = $this->__lang('password.too_weak');

            return $return;
        }

        // before all password checks
        if ($this->isEmailTaken($email)) {
            $this->addAttempt();
            $return['message'] = $this->__lang('email_taken');

            return $return;
        }

        $addUser = $this->addUser($email, $password, $params, $use_email_activation);

        if ($addUser['error'] != 0) {
            $return['message'] = $addUser['message'];

            return $return;
        }

        $return['error'] = false;
        $return['message'] =
            $use_email_activation
                ? $this->__lang('register_success')
                : $this->__lang('register_success_emailmessage_suppressed');
        $return['uid'] = $addUser['uid'];
        $return['token'] = $addUser['token'];

        return $return;
    }

    public function activate(string $activate_token):array
    {
        $return['error'] = true;
        $block_status = $this->isBlocked();

        if ($block_status == 'block') {
            $return['message'] = $this->__lang('user_blocked');

            return $return;
        }

        if (strlen($activate_token) !== self::TOKEN_LENGTH) {
            $this->addAttempt();
            $return['message'] = $this->__lang('activationkey_invalid');

            return $return;
        }

        // 'user is already activated' will never triggered, because after successful activation token removed from DB
        // NOW is no any way to determine, is this token used for activation or not?

        $request_result = $this->getRequest($activate_token, 'activation');

        if ($request_result['error']) {
            $return['message'] = $request_result['message'];

            return $return;
        }

        //@todo: extract value (we will be able to use user email after)
        if ($this->getBaseUser($request_result['uid'])['isactive'] == 1) {
            $this->addAttempt();
            $this->deleteRequest($request_result['id']);
            $return['message'] = $this->__lang('system_error') . ' #02'; // user activated, but activate token not expired

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
        $return['message'] = $this->__lang('account_activated'); // => account.activated

        return $return;
    }

    public function requestReset(string $email, bool $use_email_activation = false):array
    {
        $state['error'] = true;
        $block_status = $this->isBlocked();

        if ($block_status == 'block') {
            $state['message'] = $this->__lang('user_blocked');

            return $state;
        }

        $validateEmail = $this->validateEmail($email);

        if ($validateEmail['error'] == 1) {
            $state['message'] = $this->__lang('email_invalid');

            return $state;
        }

        $query = "SELECT id, email FROM {$this->config->table_users} WHERE email = :email";
        $query_prepared = $this->dbh->prepare($query);
        $query_prepared->execute(['email' => $email]);

        $row = $query_prepared->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            $this->addAttempt();

            $state['message'] = $this->__lang('email_incorrect');

            return $state;
        }

        $addRequest = $this->addRequest($row['id'], $email, 'reset', $use_email_activation);

        if ($addRequest['error'] == 1) {
            $this->addAttempt();
            $state['message'] = $addRequest['message'];

            return $state;
        }

        $state['uid'] = $row['id'];
        $state['error'] = false;
        $state['message'] = ($use_email_activation ? $this->__lang('reset_requested') : $this->__lang('reset_requested_emailmessage_suppressed'));
        $state['token'] = $addRequest['token'];
        $state['expire'] = $addRequest['expire'];

        return $state;
    }

    public function logout(string $hash):bool
    {
        if (strlen($hash) != self::HASH_LENGTH) {
            return false;
        }

        $this->isAuthenticated = false;
        $this->currentuser = null;

        return $this->deleteSession($hash);
    }

    public function logoutAll(int $uid):bool
    {
        $this->isAuthenticated = false;
        $this->currentuser = null;

        return $this->deleteExistingSessions($uid);
    }

    public function getUID(string $email):int
    {
        $query = "SELECT id FROM {$this->config->table_users} WHERE email = :email";
        $query_prepared = $this->dbh->prepare($query);
        $query_prepared->execute(['email' => mb_strtolower($email)]);

        $uid = $query_prepared->fetchColumn();

        return $uid === false ? 0 : $uid;
    }

    public function getUser(int $uid, bool $with_password = false):?array
    {
        $query = "SELECT * FROM {$this->config->table_users} WHERE id = :id";
        $query_prepared = $this->dbh->prepare($query);
        $query_prepared->execute(['id' => $uid]);

        $data = $query_prepared->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $data['uid'] = $uid;

        if ($with_password !== true) {
            unset($data['password']);
        }

        return $data;
    }

    public function deleteUser(int $uid, string $password, string $captcha_response = ''):array
    {
        $return = [];
        $return['error'] = true;

        $block_status = $this->isBlocked();
        if ($block_status == 'verify') {
            if ($this->checkCaptcha($captcha_response) == false) {
                $return['message'] = $this->__lang('user_verify_failed');

                return $return;
            }
        }

        if ($block_status == 'block') {
            $return['message'] = $this->__lang('user_blocked');

            return $return;
        }

        // check password

        // password length? not required!
        $validatePassword = $this->validatePasswordLength($password);

        if ($validatePassword['error'] == 1) {
            $this->addAttempt();
            $return['message'] = $validatePassword['message'];

            return $return;
        }

        $user = $this->getBaseUser($uid);

        if (!password_verify($password, $user['password'])) {
            $this->addAttempt();
            $return['message'] = $this->__lang('password.incorrect');

            return $return;
        }

        // remove user by function
        $return = $this->deleteUserForced($uid);

        return $return;
    }

    public function deleteUserForced(int $uid):array
    {
        $return = [];
        $return['error'] = true;

        $query = "DELETE FROM {$this->config->table_users} WHERE id = :uid";
        $query_prepared = $this->dbh->prepare($query);

        if (!$query_prepared->execute(['uid' => $uid])) {
            $return['message'] = $this->__lang('system_error') . ' #05';

            return $return;
        }

        $query = "DELETE FROM {$this->config->table_sessions} WHERE uid = :uid";
        $query_prepared = $this->dbh->prepare($query);

        if (!$query_prepared->execute(['uid' => $uid])) {
            $return['message'] = $this->__lang('system_error') . ' #06';

            return $return;
        }

        $query = "DELETE FROM {$this->config->table_requests} WHERE uid = :uid";
        $query_prepared = $this->dbh->prepare($query);

        if (!$query_prepared->execute(['uid' => $uid])) {
            $return['message'] = $this->__lang('system_error') . ' #07';

            return $return;
        }

        $return['error'] = false;
        $return['message'] = $this->__lang('account_deleted'); // => account.deleted_successfully

        return $return;
    }

    public function checkSession(string $hash, ?string $device_id = null):bool
    {
        $ip = self::getIp();
        $block_status = $this->isBlocked();

        if ($block_status == 'block') {
            $return['message'] = $this->__lang('user_blocked');
            return false;
        }

        if (strlen($hash) != self::HASH_LENGTH) {
            return false;
        }

        // INET_NTOA(ip)
        $query = "SELECT id, uid, expiredate, ip, agent, cookie_crc, device_id FROM {$this->config->table_sessions} WHERE hash = :hash";
        $query_prepared = $this->dbh->prepare($query);
        $query_params = [
            'hash' => $hash
        ];
        $query_prepared->execute($query_params);

        $row = $query_prepared->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return false;
        }

        $uid = $row['uid'];
        $expire_date = strtotime($row['expiredate']);
        $current_date = strtotime(date('Y-m-d H:i:s'));
        $db_ip = $row['ip'];
        $db_cookie = $row['cookie_crc'];
        $db_device_id = $row['device_id'];

        if ($current_date > $expire_date) {
            $this->deleteSession($hash);

            return false;
        }

        if ($device_id != null) {
            if ($db_device_id !== $device_id) {
                return false;
            }
        } else {
            if ($ip !== $db_ip) {
                return false;
            }
        }

        if ($db_cookie == sha1($hash . $this->config->site_key)) {
            if ($expire_date - $current_date < strtotime($this->config->cookie_renew) - $current_date) {
                $this->deleteSession($hash);
                $this->addSession($uid, false);
            }
            return true;
        }

        return false;
    }

    public function getSessionUID(string $hash):int
    {
        $query = "SELECT uid FROM {$this->config->table_sessions} WHERE hash = :hash";
        $query_prepared = $this->dbh->prepare($query);
        $query_params = [
            'hash' => $hash
        ];
        $query_prepared->execute($query_params);

        $uid = $query_prepared->fetch(PDO::FETCH_ASSOC)['uid'];

        if (!$uid) {
            return 0;
        }

        return (int)$uid;
    }

    public function isEmailTaken(string $email):bool
    {
        $query = "SELECT count(*) FROM {$this->config->table_users} WHERE email = :email";
        $query_prepared = $this->dbh->prepare($query);
        $query_prepared->execute(['email' => $email]);

        if ($query_prepared->fetchColumn() == 0) {
            return false;
        }

        return true;
    }

    //@todo: use custom validator instead
    public function isEmailBanned(string $email):bool
    {
        try {
            $this->dbh->query("SELECT * FROM {$this->config->table_emails_banned} LIMIT 1;");
        } catch (PDOException $e) {
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

    public function getRequest(string $key, string $type):array
    {
        $return = [];
        $return['error'] = true;

        $query = "SELECT id, uid, expire FROM {$this->config->table_requests} WHERE token = ? AND type = ?";
        $query_prepared = $this->dbh->prepare($query);
        $query_prepared->execute([$key, $type]);

        if ($query_prepared->rowCount() === 0) {
            $this->addAttempt();
            $return['message'] = $this->__lang($type . 'key_incorrect');

            return $return;
        }

        $row = $query_prepared->fetch(PDO::FETCH_ASSOC);

        $expiredate = strtotime($row['expire']);
        $currentdate = strtotime(date('Y-m-d H:i:s'));

        if ($currentdate > $expiredate) {
            $this->addAttempt();
            $this->deleteRequest($row['id']);
            $return['message'] = $this->__lang($type . 'key_expired');

            return $return;
        }

        $return['error'] = false;
        $return['id'] = $row['id'];
        $return['uid'] = $row['uid'];

        return $return;
    }

    public function resetPass(string $key, string $password, string $repeatpassword, string $captcha_response = '')
    {
        $state['error'] = true;
        $block_status = $this->isBlocked();

        if ($block_status == 'verify') {
            if ($this->checkCaptcha($captcha_response) == false) {
                $state['message'] = $this->__lang('user_verify_failed');

                return $state;
            }
        }

        if ($block_status == 'block') {
            $state['message'] = $this->__lang('user_blocked');

            return $state;
        }

        if (strlen($key) != self::TOKEN_LENGTH) {
            $state['message'] = $this->__lang('resetkey_invalid');

            return $state;
        }

        $validatePasswordState = $this->validatePasswordLength($password);

        if ($validatePasswordState['error']) {
            $state['message'] = $validatePasswordState['message'];
            return $state;
        }

        // check password strength using custom validator
        if (!$this->isPasswordStrong($password)) {
            $state['message'] = $this->__lang('password_weak');
            return $state;
        }

        if ($password !== $repeatpassword) {
            // Passwords don't match
            $state['message'] = $this->__lang('newpassword_nomatch');

            return $state;
        }

        $data = $this->getRequest($key, 'reset');

        if ($data['error'] == 1) {
            $state['message'] = $data['message'];

            return $state;
        }

        $user = $this->getBaseUser($data['uid']);

        if (!$user) {
            $this->addAttempt();
            $this->deleteRequest($data['id']);
            $state['message'] = $this->__lang('system_error') . ' #11';

            return $state;
        }

        if (password_verify($password, $user['password'])) {
            $this->addAttempt();
            $state['message'] = $this->__lang('newpassword_match');

            return $state;
        }

        $password = self::getHash($password, $this->config->bcrypt_cost);

        $query = "UPDATE {$this->config->table_users} SET password = :password WHERE id = :id";
        $query_prepared = $this->dbh->prepare($query);
        $query_params = [
            'password' => $password,
            'id' => $data['uid']
        ];
        $query_prepared->execute($query_params);

        if ($query_prepared->rowCount() == 0) {
            $state['message'] = $this->__lang('system_error') . ' #12';

            return $state;
        }

        $this->deleteRequest($data['id']);
        $state['error'] = false;
        $state['message'] = $this->__lang('password_reset');

        return $state;
    }

    public function resendActivation(string $email, bool $use_email_activation = false):array
    {
        $state['error'] = true;
        $block_status = $this->isBlocked();

        if ($block_status == 'block') {
            $state['message'] = $this->__lang('user_blocked');

            return $state;
        }

        if ($use_email_activation == false) {
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

        $found_user = $query_prepared->fetch(PDO::FETCH_ASSOC);

        if (!$found_user) {
            $this->addAttempt();
            $state['message'] = $this->__lang('email_incorrect'); // Really: account not found!

            return $state;
        }

        // this check must be implemented in activateAccount() method
        if ($found_user['isactive']) {
            $this->addAttempt();
            $state['message'] = $this->__lang('already_activated');

            return $state;
        }

        // Create an activation entry and sends email to user
        $addRequest = $this->addRequest($found_user['id'], $email, 'activation', $use_email_activation);

        if ($addRequest['error'] == 1) {
            $this->addAttempt();
            $state['message'] = $addRequest['message'];

            return $state;
        }

        $state['error'] = false;
        $state['message'] = $this->__lang('activation_sent');
        $state['token'] = $addRequest['token'];
        return $state;
    }

    public function changePassword(int $uid, string $currpass, string $newpass, string $repeatnewpass, string $captcha_response = ''):array
    {
        $return = [];
        $return['error'] = true;
        $block_status = $this->isBlocked();

        if ($block_status == 'verify') {
            if ($this->checkCaptcha($captcha_response) == false) {
                $return['message'] = $this->__lang('user_verify_failed');
                return $return;
            }
        }

        if ($block_status == 'block') {
            $return['message'] = $this->__lang('user_blocked');

            return $return;
        }

        $validatePassword = $this->validatePasswordLength($currpass);

        if ($validatePassword['error'] == 1) {
            $this->addAttempt();
            $return['message'] = $validatePassword['message'];

            return $return;
        }

        $validatePassword = $this->validatePasswordLength($newpass);

        if ($validatePassword['error'] == 1) {
            $return['message'] = $validatePassword['message'];

            return $return;
        } elseif ($newpass !== $repeatnewpass) {
            $return['message'] = $this->__lang('newpassword_nomatch');

            return $return;
        }

        // check password strength using custom validator
        if (!$this->isPasswordStrong($newpass)) {
            $return['message'] = $this->__lang('password_weak');
            return $return;
        }

        $user = $this->getBaseUser($uid);

        if (!$user) {
            $this->addAttempt();
            $return['message'] = $this->__lang('system_error') . ' #13';

            return $return;
        }

        if (!password_verify($currpass, $user['password'])) {
            $this->addAttempt();
            $return['message'] = $this->__lang('password_incorrect');

            return $return;
        }

        $newpass = self::getHash($newpass, $this->config->bcrypt_cost);

        $query = "UPDATE {$this->config->table_users} SET password = ? WHERE id = ?";
        $query_prepared = $this->dbh->prepare($query);
        $query_prepared->execute([$newpass, $uid]);

        $return['error'] = false;
        $return['message'] = $this->__lang('password_changed');

        return $return;
    }

    public function changeEmail(int $uid, string $email, string $password, string $captcha = ''):array
    {
        $return = [];
        $return['error'] = true;
        $block_status = $this->isBlocked();

        if ($block_status == 'verify') {
            if ($this->checkCaptcha($captcha) == false) {
                $return['message'] = $this->__lang('user_verify_failed');

                return $return;
            }
        }

        if ($block_status == 'block') {
            $return['message'] = $this->__lang('user_blocked');

            return $return;
        }

        $validateEmail = $this->validateEmail($email);

        if ($validateEmail['error'] == 1) {
            $return['message'] = $validateEmail['message'];

            return $return;
        }

        if ($this->isEmailTaken($email)) {
            $this->addAttempt();
            $return['message'] = $this->__lang('email_taken');

            return $return;
        }

        $validatePassword = $this->validatePasswordLength($password); //@todo: WTF???

        if ($validatePassword['error'] == 1) {
            $return['message'] = $this->__lang('password_notvalid');

            return $return;
        }

        $user = $this->getBaseUser($uid);

        if (!$user) {
            $this->addAttempt();
            $return['message'] = $this->__lang('system_error') . ' #14';

            return $return;
        }

        if (!password_verify($password, $user['password'])) {
            $this->addAttempt();
            $return['message'] = $this->__lang('password_incorrect');

            return $return;
        }

        if ($email == $user['email']) {
            $this->addAttempt();
            $return['message'] = $this->__lang('newemail_match');

            return $return;
        }

        $query = "UPDATE {$this->config->table_users} SET email = ? WHERE id = ?";
        $query_prepared = $this->dbh->prepare($query);
        $query_prepared->execute([$email, $uid]);

        if ($query_prepared->rowCount() == 0) {
            $return['message'] = $this->__lang('system_error') . ' #15';

            return $return;
        }

        $return['error'] = false;
        $return['message'] = $this->__lang('email_changed');

        return $return;
    }

    public function isBlocked():string
    {
        $ip = self::getIp();
        $this->deleteAttempts($ip, false);

        $query = "SELECT count(*) FROM {$this->config->table_attempts} WHERE ip = :ip";
        $query_prepared = $this->dbh->prepare($query);
        $query_prepared->execute(['ip' => $ip]);
        $attempts = $query_prepared->fetchColumn();

        if ((int)$this->config->attempts_before_verify > 0 && $attempts < (int)$this->config->attempts_before_verify) {
            return 'allow';
        }

        if ((int)$this->config->attempts_before_ban > 0 && $attempts < (int)$this->config->attempts_before_ban) {
            return 'verify';
        }

        return 'block';
    }

    public function getCurrentSessionHash():string
    {
        if( $this->config->uses_session ) {
            $expire = $_SESSION[$this->config->cookie_name . '_expire'] ?? 0;
            if( $expire > 0 && $expire < time() ) {
                // Session expired, unset the session hash
                unset( $_SESSION[$this->config->cookie_name] );
                unset( $_SESSION[$this->config->cookie_name.'_expire'] );
                return '';
            }
            return $_SESSION[$this->config->cookie_name] ?? '';
        }
        return $_COOKIE[$this->config->cookie_name] ?? '';
    }

    public function isLogged():bool
    {
        if ($this->isAuthenticated === false) {
            $this->isAuthenticated = $this->checkSession($this->getCurrentSessionHash());
        }

        return $this->isAuthenticated;
    }

    public function getCurrentUser(bool $updateSession = false):?array
    {
        $hash = $this->getCurrentSessionHash();

        if ($this->currentuser === null) {
            $uid = $this->getSessionUID($hash);

            if ($uid === 0) {
                return null;
            }

            $this->currentuser = $this->getUser($uid);
        }

        if ($updateSession) {
            $this->renewUserSession($hash);
        }

        return $this->currentuser;
    }

    public function comparePasswords(int $userid, string $password_for_check):bool
    {
        $query = "SELECT password FROM {$this->config->table_users} WHERE id = ?";
        $query_prepared = $this->dbh->prepare($query);
        $query_prepared->execute([$userid]);

        $data = $query_prepared->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return false;
        }

        return password_verify($password_for_check, $data['password']);
    }

    public function password_verify_with_rehash(string $password, string $hash, int $uid):bool
    {
        if (password_verify($password, $hash) !== true) {
            return false;
        }

        if (password_needs_rehash($hash, PASSWORD_DEFAULT, [ 'cost' => $this->config->bcrypt_cost ])) {
            $hash = self::getHash($password, $this->config->bcrypt_cost);

            $query = "UPDATE {$this->config->table_users} SET password = ? WHERE id = ?";
            $query_prepared = $this->dbh->prepare($query);
            $query_prepared->execute([$hash, $uid]);
        }

        return true;
    }

    //@todo: split to two separate methods (needed for custom handler)
    public function do_SendMail(string $email, string $type, string $key)
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

            $mail->CharSet = $this->config->mail_charset;       //@todo: must be ALWAYS 'UTF-8'

            //Content
            $mail->isHTML(true);

            if ($type == 'activation') {
                $mail->Subject = $this->__lang('email_activation_subject', $this->config->site_name);
                if ($this->config->site_activation_page_append_code) {
                    $url = $this->config->site_activation_page . '/' . $key;
                } else {
                    $url = $this->config->site_activation_page;
                }
                $mail->Subject = $this->__lang('email_activation_subject', $this->config->site_name);
                $mail->Body = $this->__lang('email_activation_body', $this->config->site_url, $url, $key);
                $mail->AltBody = $this->__lang('email_activation_altbody', $this->config->site_url, $url, $key);
            } elseif ($type == 'reset') {
                if ($this->config->site_password_reset_page_append_code) {
                    $url = $this->config->site_password_reset_page . '/' . $key;
                } else {
                    $url = $this->config->site_password_reset_page;
                }
                $mail->Subject = $this->__lang('email_reset_subject', $this->config->site_name);
                $mail->Body = $this->__lang('email_reset_body', $this->config->site_url, $url, $key);
                $mail->AltBody = $this->__lang('email_reset_altbody', $this->config->site_url, $url, $key);
            } else {
                return false;
            }

            if (!$mail->send()) {
                throw new Exception($mail->ErrorInfo);
            }

            $return['error'] = false;
        } catch (Exception $e) {
            $return['message'] = $mail->ErrorInfo;
        }

        return $return;
    }

    public function updateUser(int $uid, array $params)
    {
        $setParams = '';

        //@todo: check, is email present at database

        //unset uid which is set in getUser(). array generated in getUser() is now usable as parameter for updateUser()
        unset($params['uid']);

        if (is_array($params) && count($params) > 0) {
            $setParams = implode(', ', array_map(static function ($key, $value) {
                return $key . ' = ?';
            }, array_keys($params), $params));
        }

        $query = "UPDATE {$this->config->table_users} SET {$setParams} WHERE id = ?";

        //NB: There is NO possible SQL-injection here, 'cause $setParams will be like 'name = ?, age = ?'

        $query_prepared = $this->dbh->prepare($query);
        $bindParams = array_values(array_merge($params, [$uid]));

        if (!$query_prepared->execute($bindParams)) {
            $return['message'] = $this->__lang('system_error') . ' #04';
            return $return;
        }

        $return['error'] = false;
        $return['message'] = 'Ok.';

        return $return;
    }

    public function getCurrentUID(): int
    {
        return $this->getSessionUID($this->getCurrentSessionHash());
    }

    public function getCurrentSessionUserInfo(): ?array
    {
        $ts = $this->config->table_sessions;
        $tu = $this->config->table_users;

        $query = "
		SELECT {$ts}.uid, {$ts}.expiredate, {$ts}.ip, {$tu}.email
		FROM {$ts}, {$tu}
		WHERE hash = :hash AND {$ts}.uid = {$tu}.id";

        $query = $this->dbh->prepare($query);
        $query->execute([
            'hash' => $this->getCurrentSessionHash()
        ]);

        if ($query->rowCount() == 0) {
            return null;
        }

        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteExpiredData()
    {
        $this->deleteExpiredAttempts();
        $this->deleteExpiredSessions();
        $this->deleteExpiredRequests();
    }

    public function cron()
    {
        $this->deleteExpiredData();
    }

    /* ============================================================================================================= */
    /* ============================================= PROTECTED METHODS ============================================= */
    /* ============================================================================================================= */

    /**
     * Creates a session for a specified user id
     *
     * @param int $uid
     * @param boolean $remember
     * @return array | false $data
     */
    protected function addSession(int $uid, bool $remember)
    {
        $ip = self::getIp();
        $user = $this->getBaseUser($uid);

        if (!$user) {
            return false;
        }

        $data['hash'] = sha1($this->config->site_key . microtime());
        $agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        if (!$this->config->allow_concurrent_sessions) {
            $this->deleteExistingSessions($uid);
        }

        $data['expire']
            = $remember
            ? strtotime($this->config->cookie_remember)
            : strtotime($this->config->cookie_forget);

        $data['cookie_crc'] = sha1($data['hash'] . $this->config->site_key);

        // don't use INET_ATON(:ip), use ip2long(), 'cause SQLite or PosgreSQL does not have INET_ATON() function
        $query = "
            INSERT INTO {$this->config->table_sessions}
            (uid, hash, expiredate, ip, agent, cookie_crc)
            VALUES (:uid, :hash, :expiredate, :ip, :agent, :cookie_crc)
            ";
        $query_prepared = $this->dbh->prepare($query);
        $query_params = [
            'uid'           =>  $uid,
            'hash'          =>  $data['hash'],
            'expiredate'    =>  date('Y-m-d H:i:s', $data['expire']),
            'ip'            =>  $ip,
            'agent'         =>  $agent,
            'cookie_crc'    =>  $data['cookie_crc']
        ];

        if (!$query_prepared->execute($query_params)) {
            return false;
        }

        $cookie_options = [
            'expires'   =>  $data['expire'],
            'path'      =>  $this->config->cookie_path,
            'domain'    =>  $this->config->cookie_domain,
            'secure'    =>  $this->config->cookie_secure,
            'httponly'  =>  $this->config->cookie_http,
            'samesite'  =>  $this->config->cookie_samesite ?? 'Lax' // None || Lax  || Strict
        ];

        // When config uses session
        if( $this->config->uses_session ) {
            $_SESSION[$this->config->cookie_name] = $data['hash'];
            $_SESSION[$this->config->cookie_name . '_expire'] = $data['expire'];
        } else {
            if (!setcookie($this->config->cookie_name, $data['hash'], $cookie_options)) {
                return false;
            }
        }

        return $data;
    }

    /**
     * Removes all existing sessions for a given UID
     *
     * @param int $uid
     * @return boolean
     */
    protected function deleteExistingSessions(int $uid):bool
    {
        $query = "DELETE FROM {$this->config->table_sessions} WHERE uid = :uid";
        $query_prepared = $this->dbh->prepare($query);
        $query_prepared->execute(['uid' => $uid]);
        $this->removeCookie();

        return $query_prepared->rowCount() > 0;
    }

    /**
     * Removes a session based on hash
     *
     * @param string $hash
     *
     * @return boolean
     */
    protected function deleteSession(string $hash):bool
    {
        $query = "DELETE FROM {$this->config->table_sessions} WHERE hash = :hash";
        $query_prepared = $this->dbh->prepare($query);
        $query_prepared->execute(['hash' => $hash]);
        $this->removeCookie();

        return $query_prepared->rowCount() == 1;
    }

    /**
     * Clear user cookie
     */
    protected function removeCookie():bool
    {
        // Execute this if config uses session
        if( $this->config->uses_session ) {
            // Unset session
            if( isset( $_SESSION[$this->config->cookie_name] ) ) {
                unset($_SESSION[$this->config->cookie_name]);
            }
        } else {
             // Remove cookie
             if(isset($_COOKIE[$this->config->cookie_name])) {
                unset($_COOKIE[$this->config->cookie_name]);
            }
            if (!setcookie($this->config->cookie_name, '', -1, '/')) {
                return false;
            }
        }

        return true;
    }

    /**
     * Adds a new user to database
     *
     * @param string $email -- email
     * @param string $password -- password
     * @param array $params -- additional params
     * @param boolean $use_email_activation -- activate email confirm or not
     * @return array
     */
    protected function addUser(string $email, string $password, array $params = [], bool $use_email_activation = false):array
    {
        $return['error'] = true;

        $query = "INSERT INTO {$this->config->table_users} (isactive) VALUES (0)";
        $query_prepared = $this->dbh->prepare($query);

        if (!$query_prepared->execute()) {
            $return['message'] = $this->__lang('system_error') . ' #03';
            return $return;
        }

        $uid = $this->dbh->lastInsertId("{$this->config->table_users}_id_seq");
        $email = htmlentities(strtolower($email));

        $token = '';
        if ($use_email_activation) {
            $addRequest = $this->addRequest($uid, $email, 'activation', $use_email_activation);
            $token = $addRequest['token'];

            if ($addRequest['error'] == 1) {
                $query = "DELETE FROM {$this->config->table_users} WHERE id = :id";
                $query_prepared = $this->dbh->prepare($query);
                $query_params = [
                    'id' => $uid
                ];
                $query_prepared->execute($query_params);

                $return['message'] = $addRequest['message'];
                return $return;
            }

            $isactive = 0;
        } else {
            $isactive = 1;
        }

        $password = self::getHash($password, $this->config->bcrypt_cost);

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

        $query = "UPDATE {$this->config->table_users} SET email = ?, password = ?, isactive = ? {$setParams} WHERE id = ?";
        $query_prepared = $this->dbh->prepare($query);

        $bindParams = array_values(array_merge([$email, $password, $isactive], $params, [$uid]));

        if (!$query_prepared->execute($bindParams)) {
            $query = "DELETE FROM {$this->config->table_users} WHERE id = ?";
            $query_prepared = $this->dbh->prepare($query);

            $query_prepared->execute([$uid]);
            $return['message'] = $this->__lang('system_error') . ' #04';

            return $return;
        }

        $return['uid'] = $uid;
        $return['error'] = false;
        $return['token'] = $token;
        return $return;
    }

    /**
     * Gets basic user data for a given UID and returns an array
     *
     * @param int $uid
     * @return array|bool $data
     */
    protected function getBaseUser(int $uid)
    {
        $query = "SELECT email, password, isactive FROM {$this->config->table_users} WHERE id = :id";
        $query_prepared = $this->dbh->prepare($query);
        $query_prepared->execute(['id' => $uid]);

        $data = $query_prepared->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return false;
        }

        $data['uid'] = $uid;

        return $data;
    }

    /**
     * Creates an activation entry and sends email to user
     *
     * @param int $uid
     * @param string $email
     * @param string $type
     * @param boolean $use_email_activation
     * @return array
     */
    protected function addRequest(int $uid, string $email, string $type, bool $use_email_activation = false): array
    {
        $return = [];
        $return['error'] = true;

        if ($type == 'activation') {
            $dictionary_key__request_exists = 'activation_exists';
        } elseif ($type == 'reset') {
            $dictionary_key__request_exists = 'reset_exists';
        } else {
            $return['message'] = $this->__lang('system_error') . ' #08';

            return $return;
        }

        $send_email = true;

        // if not set up manually, check config data
        if (false !== $use_email_activation) {
            $use_email_activation = true;

            if ($type == 'reset' && !!$this->config->emailmessage_suppress_reset) {
                $send_email = false;
            }

            if ($type == 'activation' && !!$this->config->emailmessage_suppress_activation) {
                $send_email = false;
            }
        }

        $query = "SELECT id, expire FROM {$this->config->table_requests} WHERE uid = :uid AND type = :type";
        $query_prepared = $this->dbh->prepare($query);
        $query_prepared->execute(['uid' => $uid, 'type' => $type]);

        $row_count = $query_prepared->rowCount();

        if ($row_count > 0) {
            $row = $query_prepared->fetch(PDO::FETCH_ASSOC);

            $expiredate = strtotime($row['expire']);
            $currentdate = strtotime(date('Y-m-d H:i:s'));

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

        $token = self::getRandomKey(self::TOKEN_LENGTH); // use GUID for tokens?
        $expire = date('Y-m-d H:i:s', strtotime($this->config->request_key_expiration));

        $query = "INSERT INTO {$this->config->table_requests} (uid, token, expire, type) VALUES (:uid, :token, :expire, :type)";
        $query_prepared = $this->dbh->prepare($query);

        $query_params = [
            'uid' => $uid,
            'token' => $token,
            'expire' => $expire,
            'type' => $type
        ];

        if (!$query_prepared->execute($query_params)) {
            $return['message'] = $this->__lang('system_error') . ' #09';

            return $return;
        }

        $request_id = $this->dbh->lastInsertId();

        if ($use_email_activation === true && $send_email) {
            $sendmail_status = $this->do_SendMail($email, $type, $token);

            if ($sendmail_status['error']) {
                $this->deleteRequest($request_id);

                $return['message'] = $this->__lang('system_error') . ' ' . $sendmail_status['message'] . ' #10';
                return $return;
            }
        }

        $return['error'] = false;
        $return['token'] = $token;
        $return['expire'] = $expire;

        return $return;
    }

    /**
     * Deletes request from database
     *
     * @param int $id
     *
     * @return boolean
     */
    protected function deleteRequest(int $id):bool
    {
        $query = "DELETE FROM {$this->config->table_requests} WHERE id = :id";
        $query_prepared = $this->dbh->prepare($query);
        return $query_prepared->execute(['id' => $id]);
    }

    /**
     * Verifies that an email is valid
     *
     * @param string $email
     *
     * @return array $return
     *
     * @todo: return Result instance
     */
    protected function validateEmail(string $email):array
    {
        $state['error'] = true;

        if ((int)$this->config->verify_email_min_length > 0 && strlen($email) < (int)$this->config->verify_email_min_length) {
            // min email length = 5 : `a@b.c`, is this really required check ?
            $state['message'] = $this->__lang('email.address_too_short', (int)$this->config->verify_email_min_length);

            return $state;
        } elseif ((int)$this->config->verify_email_max_length > 0 && strlen($email) > (int)$this->config->verify_email_max_length) {
            // is this really required check?
            $state['message'] = $this->__lang('email.address_too_long', (int)$this->config->verify_email_max_length);

            return $state;
        } elseif ($this->config->verify_email_valid && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $state['message'] = $this->__lang('email.address_incorrect', $email);

            return $state;
        }

        //@todo: use custom validator
        /*
        if (is_callable($this->emailValidator) && call_user_func_array($this->emailValidator, [ $email ])) {
            $this->addAttempt();
            $state['message'] = $this->__lang('email.address_in_banlist');

            return $state;
        }
        */
        // instead of:

        if ((int)$this->config->verify_email_use_banlist && $this->isEmailBanned($email)) {
            $this->addAttempt();
            $state['message'] = $this->__lang('email.address_in_banlist');

            return $state;
        }

        $state['error'] = false;

        return $state;
    }

    /**
     * Verifies a captcha code
     *
     * @param string $captcha
     *
     * @return boolean
     */
    protected function checkCaptcha(string $captcha):bool
    {
        return true;
    }


    /**
     * Check Google Recaptcha code.
     * If reCaptcha disabled in config or config not defined - return TRUE (captcha passed)
     *
     * @param string $captcha_response
     *
     * @return boolean
     */
    protected function checkReCaptcha(string $captcha_response):bool
    {
        if (empty($this->recaptcha_config)) {
            return true;
        }

        if ($this->recaptcha_config['recaptcha_enabled']) {
            if (empty($this->recaptcha_config['recaptcha_secret_key'])) {
                throw new RuntimeException('No secret provided');
            }
            if (!is_string($this->recaptcha_config['recaptcha_secret_key'])) {
                throw new RuntimeException('The provided secret must be a string');
            }

            $recaptcha = new ReCaptcha($this->recaptcha_config['recaptcha_secret_key']);
            $checkout = $recaptcha->verify($captcha_response, self::getIp());

            if (!$checkout->isSuccess()) {
                return false;
            }
        }

        return true;
    }


    /**
     * Adds an attempt to database
     *
     * @return boolean
     */
    protected function addAttempt():bool
    {
        $ip = self::getIp();
        $attempt_expiredate = date('Y-m-d H:i:s', strtotime($this->config->attack_mitigation_time));

        $query = "INSERT INTO {$this->config->table_attempts} (ip, expiredate) VALUES (:ip, :expiredate)";
        $query_prepared = $this->dbh->prepare($query);
        return $query_prepared->execute([
            'ip' => $ip,
            'expiredate' => $attempt_expiredate
        ]);
    }

    /**
     * Deletes all attempts for a given IP from database
     *
     * @param string $ip
     * @param boolean|false $all
     * @return boolean
     */
    protected function deleteAttempts(string $ip, bool $all = false):bool
    {
        $query = ($all)
            ? "DELETE FROM {$this->config->table_attempts} WHERE ip = :ip"
            : "DELETE FROM {$this->config->table_attempts} WHERE ip = :ip AND NOW() > expiredate ";

        $sth = $this->dbh->prepare($query);
        return $sth->execute([
            'ip' => $ip
        ]);
    }

    /**
     * Verifies that a password lenght is greater than minimal length
     *
     * @param string $password
     *
     * @return array $return ['error', 'message']
     */
    protected function validatePasswordLength(string $password):array
    {
        $state['error'] = true;

        $password_length = strlen($password);

        if ((int)$this->config->verify_password_min_length > 0 && $password_length < (int)$this->config->verify_password_min_length) {
            $state['message'] = $this->__lang('password.too_short', $password_length);

            return $state;
        }

        $state['error'] = false;

        return $state;
    }

    /* ============================================================================================================= */
    /* ============================================== PRIVATE METHIDS ============================================== */
    /* ============================================================================================================= */

    /**
     * Update user session expire time using either session hash or uid
     *
     * @param string $hash
     * @param int $uid = null
     *
     * @return boolean
     */
    private function renewUserSession(string $hash, int $uid = 0):bool
    {
        $expire = date('Y-m-d H:i:s', strtotime($this->config->cookie_remember));

        $where = (is_null(($uid))) ? 'hash' : 'uid';
        $arr = (is_null($uid)) ? $hash : $uid;

        $STH = $this->dbh->prepare("UPDATE {$this->config->table_sessions} SET expiredate = ? WHERE {$where} = ?");

        return $STH->execute([$expire, $arr]);
    }

    /**
     * Deletes expired attempts from the database
     *
     * @return void
     */
    private function deleteExpiredAttempts()
    {
        $this->dbh->exec("DELETE FROM {$this->config->table_attempts} WHERE NOW() > expiredate");
    }

    /**
     * Deletes expired sessions from the database
     *
     * @return void
     */
    private function deleteExpiredSessions()
    {
        $this->dbh->exec("DELETE FROM {$this->config->table_sessions} WHERE NOW() > expiredate");
    }

    /**
     * Deletes expired requests from the database
     *
     * @return void
     */
    private function deleteExpiredRequests()
    {
        $this->dbh->exec("DELETE FROM {$this->config->table_requests} WHERE NOW() > expire");
    }

    /**
     * Check password strength using custom validator callback function
     *
     * @param string $password
     * @return bool
     */
    private function isPasswordStrong(string $password):bool
    {
        if (is_callable($this->passwordValidator)) {
            return ($this->passwordValidator)($password, $this->config);
        }

        // call_user_func_array($this->passwordValidator, [ $password, $this->config ])

        return true;
    }
}
