<?php

namespace PHPAuth;

use PDO;
use PHPAuth\Core\Result;

interface AuthInterface
{
    public const HASH_LENGTH = 40;
    public const TOKEN_LENGTH = 20;

    /**
     * Initiates Auth class
     *
     * @param PDO $dbh
     * @param Config $config
     */
    public function __construct($dbh, Config $config);

    /**
     * Logs a user in
     *
     * @param string $email
     * @param string $password
     * @param int $remember
     * @param string $captcha_response
     *
     * @return array|Result $return
     * @todo: => loginUser
     */
    public function login(string $email, string $password, int $remember = 0, string $captcha_response = ''):array;

    /**
     * Creates a new user, adds them to database
     * @param string $email
     * @param string $password
     * @param string $repeat_password
     * @param array $params
     * @param string $captcha_response = ""
     * @param boolean $use_email_activation = false
     * @return array $return
     *
     * //@todo: => registerUserAccount
     */
    public function register(string $email, string $password, string $repeat_password, array $params = [], string $captcha_response = '', bool $use_email_activation = false):array;

    /**
     * Activates a user's account
     * @param string $activate_token
     * @return array $return
     *
     * //@todo: rename to 'activateUserAccount'
     */
    public function activate(string $activate_token):array;

    /**
     * Creates a reset key for an email address and sends email
     *
     * @param string $email
     * @param boolean $use_email_activation
     * @return array $return
     */
    public function requestReset(string $email, bool $use_email_activation = false):array;

    /**
     * Logs out the session, identified by hash
     *
     * @param string $hash
     * @return boolean
     */
    public function logout(string $hash):bool;

    /**
     * Logs out of all sessions for specified uid
     *
     * @param int $uid
     * @return boolean
     */
    public function logoutAll(int $uid):bool;

    /**
     * Gets UID for a given email address or zero if email not found

     * @param string $email
     * @return int $uid
     */
    public function getUID(string $email):int;

    /**
     * Function to check if a session is valid
     * @param string $hash
     * @param null|string $device_id
     *
     * @return boolean
     */
    public function checkSession(string $hash, ?string $device_id = null):bool;

    /**
     * Retrieves the UID associated with a given session hash
     *
     * @param string $hash
     * @return int|null $uid
     */
    public function getSessionUID(string $hash):int;

    /**
     * Checks if an email is already in use
     *
     * @param string $email
     * @return boolean
     */
    public function isEmailTaken(string $email):bool;

    /**
     * Checks if an email is banned
     *
     * @param string $email
     * @return boolean
     */
    public function isEmailBanned(string $email):bool;

    /**
     * Gets public user data for a given UID and returns an array, password will be returned if param $withpassword is TRUE
     *
     * @param int $uid
     * @param bool $with_password
     * @return array|null $data
     */
    public function getUser(int $uid, bool $with_password = false):?array;

    /**
     * Allows a user to delete their account
     * @param int $uid
     * @param string $password
     * @param string $captcha_response
     * @return array $return
     */
    public function deleteUser(int $uid, string $password, string $captcha_response = ''):array;

    /**
     * Force delete user without password or captcha verification.
     *
     * @param int $uid
     * @return array
     */
    public function deleteUserForced(int $uid):array;

    /**
     * Returns request data if key is valid
     *
     * @param string $key
     * @param string $type
     * @return array $return
     */
    public function getRequest(string $key, string $type):array;

    /**
     * Allows a user to reset their password after requesting a reset key.
     *
     * @param string $key
     * @param string $password
     * @param string $repeatpassword
     * @param string $captcha_response = null
     * @return array $return
     */
    public function resetPass(string $key, string $password, string $repeatpassword, string $captcha_response = '');

    /**
     * Recreates activation email for a given email and sends
     *
     * @param string $email
     * @param boolean $use_email_activation
     *
     * @return array
     */
    public function resendActivation(string $email, bool $use_email_activation = false):array;

    /**
     * Changes a user's password
     *
     * @param int $uid
     * @param string $currpass
     * @param string $newpass
     * @param string $repeatnewpass
     * @param string $captcha_response = ""
     * @return array $return
     */
    public function changePassword(int $uid, string $currpass, string $newpass, string $repeatnewpass, string $captcha_response = ''):array;

    /**
     * Changes a user's email
     *
     * @param int $uid
     * @param string $email
     * @param string $password
     * @param string $captcha = null
     * @return array
     */
    public function changeEmail(int $uid, string $email, string $password, string $captcha = ''):array;

    /**
     * Informs if a user is locked out
     *
     * @return string
     */
    public function isBlocked():string;

    /**
     * Returns current session hash
     *
     * @return string
     */
    public function getCurrentSessionHash():string;

    /**
     * Returns is user logged in
     *
     * @return boolean
     */
    public function isLogged():bool;

    /**
     * Gets user data for current user (from cookie/session_hash) and returns an array, password is not returned
     *
     * @param boolean $updateSession = false
     * @return array $data
     * @return boolean false if no current user
     */
    public function getCurrentUser(bool $updateSession = false):?array;

    /**
     * Compare user's password with given password
     *
     * @param int $userid
     * @param string $password_for_check
     *
     * @return boolean
     */
    public function comparePasswords(int $userid, string $password_for_check):bool;

    /**
     * Check if users password needs to be rehashed
     *
     * @param string $password
     * @param string $hash
     * @param int $uid
     * @return boolean
     */
    public function password_verify_with_rehash(string $password, string $hash, int $uid):bool;

    /**
     * Translates key-message to defined language using substitutional params
     *
     * @param string $key
     * @param mixed ...$args
     * @return string
     */
    public function __lang(string $key, ...$args): string;

    /**
     * Send E-Mail using PHPMailer
     *
     * @param string $email
     * @param string $type
     * @param string $key
     * @return array|false $return (contains error code and error message)
     */
    public function do_SendMail(string $email, string $type, string $key);

    /**
     * Update userinfo for user with given id = $uid
     *
     * @param int $uid
     * @param array $params
     * @return array $return[error/message]
     */
    public function updateUser(int $uid, array $params);

    /**
     * Returns current user UID if logged or 0 otherwise.
     *
     * @return int
     */
    public function getCurrentUID(): int;

    /**
     * Return current user sesssion info
     *
     * @return null|array
     */
    public function getCurrentSessionUserInfo(): ?array;

    /**
     * Deletes expired requests, sessions and attempts from database
     * Alias of cron(), usable for daily cron job
     *
     * @return void
     */
    public function deleteExpiredData();

    /**
     * Daily cron job to remove expired data from the database
     * Deprecated
     *
     * @return void
     */
    public function cron();


}
