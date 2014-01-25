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
namespace cuonic\PHPAuth2;

require_once ("config.php");
require_once ("Localization/Handler.php");
require_once ("classes/tracking.php");
class Auth
{
    private $dbh;
    private $config;

    /**
     * Initiates database connection
     */
    public function __construct(\PDO $dbh)
    {
        $this->config = new Config();
        $cookie_domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
        $this->config->cookie_domain = $cookie_domain;

        $this->dbh = $dbh;

        $this->trackingObj = new tracking($this->dbh, $this->config->table_tracking);
        $this->trackingObj->storeInfo();
    }

    /**
     * Logs a user in
     * @param string $username
     * @param string $password (MUST be already twice hashed with SHA1 : Ideally client side with JS)
     * @param bool $rememberme
     * @return array $return
     */
    public function login($username, $password, $rememberme = 0)
    {
        $return = array();

        $ip = $this->getIp();

        if ($this->isBlocked($ip)) {
            $return['code'] = 0;
            return $return;
        } else {
            if (strlen($username) == 0) {
                $return['code'] = 1;
                $this->addAttempt($ip);
                return $return;
            } elseif (strlen($username) > 30) {
                $return['code'] = 1;
                $this->addAttempt($ip);
                return $return;
            } elseif (strlen($username) < 3) {
                $return['code'] = 1;
                $this->addAttempt($ip);
                return $return;
            } elseif (strlen($password) == 0) {
                $return['code'] = 1;
                $this->addAttempt($ip);
                return $return;
            } elseif (strlen($password) != 40) {
                $return['code'] = 1;
                $this->addAttempt($ip);
                return $return;
            } else {
                $plainpass = $password;
                $password = $this->getHash($password);

                if ($userdata = $this->getUserData($username)) {
                    if ($password === $userdata['password']) {
                        if ($userdata['isactive'] == 1) {

                            if ($rememberme == 1) {
                                $sessiondata = $this->addNewSession($userdata['uid'], $this->config->
                                    session_duration);
                            } elseif ($rememberme == 0) {
                                $sessiondata = $this->addNewSession($userdata['uid'], "+1 hour");
                            } else {
                                $return['code'] = 1;
                                $this->addAttempt($ip);
                                return $return;
                            }

                            $return['code'] = 4;
                            $return['session_hash'] = $sessiondata['hash'];
                            $return['expire'] = $sessiondata['expire'];

                            $this->addNewLog($userdata['uid'], "LOGIN_SUCCESS",
                                "User logged in. Session hash: {$sessiondata['hash']}");

                            return $return;
                        } else {
                            $this->addAttempt($ip);

                            $this->addNewLog($userdata['uid'], "LOGIN_FAIL_NONACTIVE", "Account inactive");

                            $return['code'] = 3;

                            return $return;
                        }
                    } else {
                        $this->addAttempt($ip);

                        $this->addNewLog($userdata['uid'], "LOGIN_FAIL_PASSWORD", "Password incorrect: {$plainpass}");

                        $return['code'] = 2;

                        return $return;
                    }
                } else {
                    $this->addAttempt($ip);

                    $this->addNewLog("", "LOGIN_FAIL_USERNAME", "Attempted login with the username: {$username} -> Username doesn't exist in DB");

                    $return['code'] = 2;

                    return $return;
                }
            }
        }
    }

    /**
     * Creates a new user, adds them to database
     * @param string $email
     * @param string $username
     * @param string $password (MUST be already twice hashed with SHA1 : Ideally client side with JS)
     * @return array $return
     */
    public function register($email, $username, $password)
    {
        $return = array();

        $ip = $this->getIp();

        if ($this->isBlocked($ip)) {
            $return['code'] = 0;
            return $return;
        } else {
            if (strlen($email) == 0) {
                $return['code'] = 1;
                $this->addAttempt($ip);
                return $return;
            } elseif (strlen($email) > 100) {
                $return['code'] = 1;
                $this->addAttempt($ip);
                return $return;
            } elseif (strlen($email) < 3) {
                $return['code'] = 1;
                $this->addAttempt($ip);
                return $return;
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $return['code'] = 1;
                $this->addAttempt($ip);
                return $return;
            } elseif (strlen($username) == 0) {
                $return['code'] = 1;
                $this->addAttempt($ip);
                return $return;
            } elseif (strlen($username) > 30) {
                $return['code'] = 1;
                $this->addAttempt($ip);
                return $return;
            } elseif (strlen($username) < 3) {
                $return['code'] = 1;
                $this->addAttempt($ip);
                return $return;
            } elseif (strlen($password) != 40) {
                $return['code'] = 1;
                $this->addAttempt($ip);
                return $return;
            } else {
                $password = $this->getHash($password);

                if (!$this->isEmailTaken($email)) {
                    if (!$this->isUsernameTaken($username)) {
                        $uid = $this->addUser($email, $username, $password);

                        $this->addNewLog($uid, "REGISTER_SUCCESS",
                            "Account created successfully, activation email sent.");

                        $return['code'] = 4;
                        $return['email'] = $email;
                        return $return;

                    } else {
                        $this->addAttempt($ip);

                        $this->addNewLog("", "REGISTER_FAIL_USERNAME",
                            "User attempted to register new account with the username: {$username} -> Username already in use");

                        $return['code'] = 3;
                        return $return;
                    }
                } else {
                    $this->addAttempt($ip);

                    $this->addNewLog("", "REGISTER_FAIL_EMAIL",
                        "User attempted to register new account with the email: {$email} -> Email already in use");

                    $return['code'] = 2;
                    return $return;
                }
            }
        }
    }

    /**
     * Activates a user's account
     * @param string $activekey
     * @return array $return
     */
    public function activate($activekey)
    {
        $return = array();

        $ip = $this->getIp();

        if ($this->isBlocked($ip)) {
            $return['code'] = 0;
            return $return;
        } else {
            if (strlen($activekey) > 20) {
                $return['code'] = 1;
                $this->addAttempt($ip);
                return $return;
            } elseif (strlen($activekey) < 20) {
                $return['code'] = 1;
                $this->addAttempt($ip);
                return $return;
            } else {
                $query = $this->dbh->prepare("SELECT uid, expiredate FROM {$this->config->table_activations} WHERE activekey = ?");
                $query->execute(array($activekey));
                $row = $query->fetch(\PDO::FETCH_ASSOC);

                if (!$row) {
                    $this->addAttempt($ip);

                    $this->addNewLog("", "ACTIVATE_FAIL_ACTIVEKEY",
                        "User attempted to activate an account with the key: {$activekey} -> Activekey not found in database");

                    $return['code'] = 2;
                    return $return;
                } else {
                    if (!$this->isUserActivated($row['uid'])) {
                        $expiredate = strtotime($row['expiredate']);
                        $currentdate = strtotime(date("Y-m-d H:i:s"));

                        if ($currentdate < $expiredate) {
                            $isactive = 1;

                            $query = $this->dbh->prepare("UPDATE {$this->config->table_users} SET isactive = ? WHERE id = ?");
                            $query->execute(array($isactive, $row['uid']));

                            $this->deleteUserActivations($row['uid']);

                            $this->addNewLog($row['uid'], "ACTIVATE_SUCCESS",
                                "Account activated -> Isactive: 1");

                            $return['code'] = 5;
                            return $return;
                        } else {
                            $this->addAttempt($ip);

                            $this->addNewLog($row['uid'], "ACTIVATE_FAIL_EXPIRED",
                                "User attempted to activate account with key: {$activekey} -> Key expired");

                            $this->deleteUserActivations($row['uid']);

                            $return['code'] = 4;
                            return $return;
                        }
                    } else {
                        $this->addAttempt($ip);

                        $this->deleteUserActivations($row['uid']);

                        $this->addNewLog($row['uid'], "ACTIVATE_FAIL_ALREADYACTIVE",
                            "User attempted to activate an account with the key : {$activekey} -> Account already active. Set activekey: 0");

                        $return['code'] = 3;
                        return $return;
                    }
                }
            }
        }
    }

    /**
     * Creates a reset key for an email address and sends email
     * @param string $email
     * @return array $return
     */
    public function requestReset($email)
    {
        $return = array();

        $ip = $this->getIp();

        if ($this->isBlocked($ip)) {
            $return['code'] = 0;
            return $return;
        } else {
            if (strlen($email) == 0) {
                $return['code'] = 1;
                $this->addAttempt($ip);
                return $return;
            } elseif (strlen($email) > 100) {
                $return['code'] = 1;
                $this->addAttempt($ip);
                return $return;
            } elseif (strlen($email) < 3) {
                $return['code'] = 1;
                $this->addAttempt($ip);
                return $return;
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $return['code'] = 1;
                $this->addAttempt($ip);
                return $return;
            } else {
                $query = $this->dbh->prepare("SELECT id FROM {$this->config->table_users} WHERE email = ?");
                $query->execute(array($email));
                $row = $query->fetch(\PDO::FETCH_ASSOC);

                if (!$row) {
                    $this->addAttempt($ip);

                    $this->addNewLog("", "REQUESTRESET_FAIL_EMAIL",
                        "User attempted to reset the password for the email : {$email} -> Email doesn't exist in DB");

                    $return['code'] = 2;
                    return $return;
                } else {
                    if ($this->addReset($row['id'], $email)) {
                        $this->addNewLog($row['id'], "REQUESTRESET_SUCCESS",
                            "A reset request was sent to the email : {$email}");

                        $return['code'] = 4;
                        $return['email'] = $email;

                        return $return;
                    } else {
                        $this->addAttempt($ip);

                        $this->addNewLog($row['id'], "REQUESTRESET_FAIL_EXIST",
                            "User attempted to reset the password for the email : {$email} -> A reset request already exists.");

                        $return['code'] = 3;
                        return $return;
                    }
                }
            }
        }
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

        $return = $this->deleteSession($hash);

        if ($return) {
            setcookie($this->config->cookie_auth, $hash, time() - 3600, $this->config->
                cookie_path, $this->config->cookie_domain, false, true);
        }
        return $return;
    }

    /**
     * Hashes string using multiple hashing methods, for enhanced security
     * @param string $string
     * @return string $enc
     */
    public function getHash($string)
    {
        if (strnatcmp(phpversion(), '5.5.0') >= 0) {
            $enc = hash_pbkdf2("SHA512", base64_encode(str_rot13(hash("SHA512", str_rot13($this->
                config->salt_1 . $string . $this->config->salt_2)))), $this->config->salt_3,
                50000, 128);
        } else {
            $enc = hash("SHA512", base64_encode(str_rot13(hash("SHA512", str_rot13($this->
                config->salt_1 . $string . $this->config->salt_2)))));
        }
        return $enc;
    }

    /**
     * Gets user data for a given username and returns an array
     * @param string $username
     * @return array $data
     */
    public function getUserData($username)
    {
        $data = array();

        $query = $this->dbh->prepare("SELECT id, password, email, salt, lang, isactive FROM {$this->config->table_users} WHERE username = ?");
        $query->execute(array($username));
        $data = $query->fetch(\PDO::FETCH_ASSOC);

        if (!$data) {
            return false;
        } else {
            $data['username'] = $username;
            $data['uid'] = $data['id'];

            return $data;
        }
    }

    /**
     * Creates a session for a specified user id
     * @param int $uid
     * @param string $expire
     * @return array $data
     */
    private function addNewSession($uid, $expire)
    {
        $query = $this->dbh->prepare("SELECT salt, lang FROM {$this->config->table_users} WHERE id = ?");
        $query->execute(array($uid));
        $data = $query->fetch(\PDO::FETCH_ASSOC);
        $data['hash'] = sha1($data['salt'] . microtime());

        $agent = $_SERVER['HTTP_USER_AGENT'];

        $this->deleteExistingSessions($uid);

        $ip = $this->getIp();

        $data['expire'] = date("Y-m-d H:i:s", strtotime($expire));
        $data['cookie_crc'] = sha1($data['hash'] . $this->config->sitekey);

        $query = $this->dbh->prepare("INSERT INTO {$this->config->table_sessions} (uid, hash, expiredate, ip, agent, cookie_crc, lang) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $query->execute(array(
            $uid,
            $data['hash'],
            $data['expire'],
            $ip,
            $agent,
            $data['cookie_crc'],
            $data['lang']));

        return $data;
    }

    /**
     * Removes all existing sessions for a given UID
     * @param int $uid
     * @return boolean
     */
    private function deleteExistingSessions($uid)
    {
        $query = $this->dbh->prepare("DELETE FROM {$this->config->table_sessions} WHERE uid = ?");
        $return = $query->execute(array($uid));

        return $return;
    }

    /**
     * Removes a session based on hash
     * @param string $hash
     * @return boolean
     */
    private function deleteSession($hash)
    {
        $query = $this->dbh->prepare("DELETE FROM {$this->config->table_sessions} WHERE hash = ?");
        $return = $query->execute(array($hash));

        return $return;
    }

    /**
     * Returns username based on session hash
     * @param string $hash
     * @return string $username
     */
    public function getUsername($hash)
    {
        $query = $this->dbh->prepare("SELECT uid FROM {$this->config->table_sessions} WHERE hash = ?");
        $query->execute(array($hash));
        $row = $query->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return false;
        } else {
            $query = $this->dbh->prepare("SELECT username FROM {$this->config->table_users} WHERE id = ?");
            $query->execute(array($row['uid']));
            $row = $query->fetch(\PDO::FETCH_ASSOC);

            if (!$row) {
                return false;
            } else {
                return $row['username'];
            }
        }
    }

    /**
     * Function to add data to log table
     * @param string $uid
     * @param string $action
     * @param string $info
     * @param return boolean
     */
    private function addNewLog($uid = 'UNKNOWN', $action, $info)
    {
        if (strlen($uid) == 0) {
            $uid = "UNKNOWN";
        } elseif (strlen($action) == 0) {
            return false;
        } elseif (strlen($action) > 100) {
            return false;
        } elseif (strlen($info) == 0) {
            return false;
        } elseif (strlen($info) > 1000) {
            return false;
        } else {
            $ip = $this->getIp();

            $query = $this->dbh->prepare("INSERT INTO {$this->config->table_log} (username, action, info, ip) VALUES (?, ?, ?, ?)");
            $return = $query->execute(array(
                $uid,
                $action,
                $info,
                $ip));

            return $return;
        }
    }

    /**
     * Function to check if a session is valid
     * @param string $hash
     * @return boolean
     */
    public function checkSession($hash)
    {
        $ip = $this->getIp();

        if ($this->isBlocked($ip)) {
            return false;
        } else {
            if (strlen($hash) != 40) {
                setcookie($this->config->cookie_auth, $hash, time() - 3600, $this->config->
                    cookie_path, $this->config->cookie_domain, false, true);
                return false;
            }

            $query = $this->dbh->prepare("SELECT id, uid, expiredate, ip, agent, cookie_crc FROM {$this->config->table_sessions} WHERE hash = ?");
            $query->execute(array($hash));
            $row = $query->fetch(\PDO::FETCH_ASSOC);

            if (!$row) {
                setcookie($this->config->cookie_auth, $hash, time() - 3600, $this->config->
                    cookie_path, $this->config->cookie_domain, false, true);

                $this->addNewLog($row['uid'], "CHECKSESSION_FAIL_NOEXIST", "Hash ({$hash}) doesn't exist in DB -> Cookie deleted");

                return false;
            } else {
                $sid = $row['id'];
                $uid = $row['uid'];
                $expiredate = $row['expiredate'];
                $db_ip = $row['ip'];
                $db_agent = $row['agent'];
                $db_cookie = $row['cookie_crc'];

                if ($ip != $db_ip) {
                    if ($_SERVER['HTTP_USER_AGENT'] != $db_agent) {
                        $this->deleteExistingSessions($uid);

                        setcookie($this->config->cookie_auth, $hash, time() - 3600, $this->config->
                            cookie_path, $this->config->cookie_domain, false, true);

                        $this->addNewLog($uid, "CHECKSESSION_FAIL_DIFF",
                            "IP and User Agent Different (DB : {$db_ip} / Current : {$ip}) -> UID sessions deleted, cookie deleted");

                        return false;
                    } else {
                        $expiredate = strtotime($expiredate);
                        $currentdate = strtotime(date("Y-m-d H:i:s"));

                        if ($currentdate > $expiredate) {
                            $this->deleteExistingSessions($uid);

                            setcookie($this->config->cookie_auth, $hash, time() - 3600, $this->config->
                                cookie_path, $this->config->cookie_domain, false, true);

                            $this->addNewLog($uid, "CHECKSESSION_FAIL_EXPIRE",
                                "Session expired (Expire date : {$row['expiredate']}) -> UID sessions deleted, cookie deleted");

                            return false;
                        } else {
                            return $this->updateSessionIp($sid, $ip);
                        }
                    }
                } else {
                    $expiredate = strtotime($expiredate);
                    $currentdate = strtotime(date("Y-m-d H:i:s"));

                    if ($currentdate > $expiredate) {
                        $this->deleteExistingSessions($uid);

                        setcookie($this->config->cookie_auth, $hash, time() - 3600, $this->config->
                            cookie_path, $this->config->cookie_domain, false, true);

                        $this->addNewLog($uid, "AUTH_CHECKSESSION_FAIL_EXPIRE",
                            "Session expired (Expire date : {$row['expiredate']}) -> UID sessions deleted, cookie deleted");

                        return false;
                    } else {
                        $cookie_crc = sha1($hash . $this->config->sitekey);

                        if ($db_cookie == $cookie_crc) {
                            return true;
                        } else {
                            $this->addNewLog($uid, "AUTH_COOKIE_FAIL_BADCRC", "Cookie Integrity failed");

                            return false;
                        }
                    }
                }
            }
        }
    }

    /**
     * Updates the IP of a session (used if IP has changed, but agent has remained unchanged)
     * @param int $sid
     * @param string $ip
     * @return boolean
     */
    private function updateSessionIp($sid, $ip)
    {
        $query = $this->dbh->prepare("UPDATE {$this->config->table_sessions} SET ip = ? WHERE id = ?");
        $return = $query->execute(array($ip, $sid));

        return $return;
    }

    /**
     * Checks if an email is already in use
     * @param string $email
     * @return boolean
     */
    private function isEmailTaken($email)
    {
        $query = $this->dbh->prepare("SELECT * FROM {$this->config->table_users} WHERE email = ?");
        $query->execute(array($email));

        if ($query->rowCount() == 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Checks if a username is already in use
     * @param string $username
     * @return boolean
     */
    private function isUsernameTaken($username)
    {
        $query = $this->dbh->prepare("SELECT * FROM {$this->config->table_users} WHERE username = ?");
        $query->execute(array($username));

        if ($query->rowCount() == 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Adds a new user to database
     * @param string $email
     * @param string $username
     * @param string $password
     * @return int $uid
     */
    private function addUser($email, $username, $password)
    {
        $username = htmlentities($username);
        $email = htmlentities($email);

        $salt = $this->getRandomKey(20);

        $lang = $this->detectLang();

        $query = $this->dbh->prepare("INSERT INTO {$this->config->table_users} (username, password, email, salt, lang) VALUES (?, ?, ?, ?, ?)");
        $query->execute(array(
            $username,
            $password,
            $email,
            $salt,
            $lang));
        $user = $this->getUserData($username);

        $this->addActivation($user['id'], $email);

        return $user['id'];
    }

    /**
     * Creates an activation entry and sends email to user
     * @param int $uid
     * @param string $email
     * @return boolean
     */
    private function addActivation($uid, $email)
    {
        $activekey = $this->getRandomKey(20);

        if ($this->isUserActivated($uid)) {
            return false;
        } else {
            $query = $this->dbh->prepare("SELECT expiredate FROM {$this->config->table_activations} WHERE uid = ?");
            $query->execute(array($uid));
            $row = $query->fetch(\PDO::FETCH_ASSOC);
            $expiredate = $row['expiredate'];

            if (count($expiredate) > 0) {
                $expiredate = strtotime($expiredate);
                $currentdate = strtotime(date("Y-m-d H:i:s"));

                if ($currentdate < $expiredate) {
                    return false;
                } else {
                    $this->deleteUserActivations($uid);
                }
            }

            $expiredate = date("Y-m-d H:i:s", strtotime("+1 day"));

            $query = $this->dbh->prepare("INSERT INTO {$this->config->table_activations} (uid, activekey, expiredate) VALUES (?, ?, ?)");
            $return = $query->execute(array(
                $uid,
                $activekey,
                $expiredate));

            if ($return) {
                //Initialize Handler which loads language
                $emailTemplate = new Localization\Handler(array('base_url' => $this->config->
                        base_url, 'key' => $activekey), $this->config->lang);
                //Get the language template
                $emailTemplate = $emailTemplate->getLocale();
                //Get array with body, head, and subject
                $emailTemplate = $emailTemplate->getActivationEmail();

                @mail($email, $emailTemplate['subject'], $emailTemplate['body'], $emailTemplate['head']);
            }

            return $return;
        }
    }

    /**
     * Deletes all activation entries for a user
     * @param int $uid
     * @return boolean
     */
    private function deleteUserActivations($uid)
    {
        $query = $this->dbh->prepare("DELETE FROM {$this->config->table_activations} WHERE uid = ?");
        $return = $query->execute(array($uid));

        return $return;
    }

    /**
     * Checks if a user account is activated based on uid
     * @param int $uid
     * @return boolean
     */
    private function isUserActivated($uid)
    {
        $query = $this->dbh->prepare("SELECT isactive FROM {$this->config->table_users} WHERE id = ?");
        $query->execute(array($uid));
        $row = $query->fetch(\PDO::FETCH_ASSOC);

        if (!$row || $row['isactive'] == 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Creates a reset entry and sends email to user
     * @param int $uid
     * @param string $email
     * @return boolean
     */
    private function addReset($uid, $email)
    {
        $resetkey = $this->getRandomKey(20);

        $query = $this->dbh->prepare("SELECT expiredate FROM {$this->config->table_resets} WHERE uid = ?");
        $query->execute(array($uid));
        $row = $query->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            $expiredate = date("Y-m-d H:i:s", strtotime("+1 day"));

            $query = $this->dbh->prepare("INSERT INTO {$this->config->table_resets} (uid, resetkey, expiredate) VALUES (?, ?, ?)");
            $return = $query->execute(array(
                $uid,
                $resetkey,
                $expiredate));

            if ($return) {
                $emailTemplate = new Localization\Handler(array('base_url' => $this->config->
                        base_url, 'key' => $resetkey), $this->config->lang);
                $emailTemplate = $emailTemplate->getLocale();
                $emailTemplate = $emailTemplate->getResetEmail();

                @mail($email, $emailTemplate['subject'], $emailTemplate['body'], $emailTemplate['head']);
            }

            return $return;
        } else {
            $expiredate = strtotime($row['expiredate']);
            $currentdate = strtotime(date("Y-m-d H:i:s"));

            if ($currentdate < $expiredate) {
                return false;
            } else {
                $this->deleteUserResets($uid);
            }
            $expiredate = date("Y-m-d H:i:s", strtotime("+1 day"));

            $query = $this->dbh->prepare("INSERT INTO {$this->config->table_resets} (uid, resetkey, expiredate) VALUES (?, ?, ?)");
            $return = $query->execute(array(
                $uid,
                $resetkey,
                $expiredate));

            if ($return) {
                $emailTemplate = new Localization\Handler(array('base_url' => $this->config->
                        base_url, 'key' => $resetkey), $this->config->lang);
                $emailTemplate = $emailTemplate->getLocale();
                $emailTemplate = $emailTemplate->getResetEmail();

                @mail($email, $emailTemplate['subject'], $emailTemplate['body'], $emailTemplate['head']);
            }
            return $return;
        }
    }

    /**
     * Deletes all reset entries for a user
     * @param int $uid
     * @return boolean
     */
    private function deleteUserResets($uid)
    {
        $query = $this->dbh->prepare("DELETE FROM {$this->config->table_resets} WHERE uid = ?");
        $return = $query->execute(array($uid));

        return $return;
    }

    /**
     * Checks if a reset key is valid
     * @param string $key
     * @return array $return
     */
    public function isResetValid($key)
    {
        $return = array();

        $ip = $this->getIp();

        if ($this->isBlocked($ip)) {
            $return['code'] = 0;
            return $return;
        } else {
            if (strlen($key) > 20) {
                $return['code'] = 1;
                $this->addAttempt($ip);
                return $return;
            } elseif (strlen($key) < 20) {
                $return['code'] = 1;
                $this->addAttempt($ip);
                return $return;
            } else {
                $query = $this->dbh->prepare("SELECT uid, expiredate FROM {$this->config->table_resets} WHERE resetkey = ?");
                $query->execute(array($key));
                $row = $query->fetch(\PDO::FETCH_ASSOC);

                if (!$row) {
                    $this->addAttempt($ip);

                    $return['code'] = 2;
                    return $return;
                } else {
                    $expiredate = strtotime($row['expiredate']);
                    $currentdate = strtotime(date("Y-m-d H:i:s"));

                    if ($currentdate > $expiredate) {
                        $this->addAttempt($ip);

                        $this->deleteUserResets($row['uid']);

                        $return['code'] = 3;
                        return $return;
                    } else {
                        $return['code'] = 4;
                        $return['uid'] = $row['uid'];
                        return $return;
                    }
                }
            }
        }
    }

    /**
     * After verifying key validity, changes user's password
     * @param string $key
     * @param string $password (Must be already twice hashed with SHA1 : Ideally client side with JS)
     * @return array $return
     */
    public function resetPass($key, $password)
    {
        $return = array();

        $ip = $this->getIp();

        if ($this->isBlocked($ip)) {
            $return['code'] = 0;
            return $return;
        } else {
            if (strlen($password) != 40) {
                $return['code'] = 1;
                $this->addAttempt($ip);
                return $return;
            }

            $data = $this->isResetValid($key);

            if ($data['code'] = 4) {
                $password = $this->getHash($password);

                $query = $this->dbh->prepare("SELECT password FROM {$this->config->table_users} WHERE id = ?");
                $query->execute(array($data['uid']));
                $row = $query->fetch(\PDO::FETCH_ASSOC);

                if (!$row) {
                    $this->addAttempt($ip);

                    $this->deleteUserResets($data['uid']);

                    $this->addNewLog($data['uid'], "RESETPASS_FAIL_UID",
                        "User attempted to reset password with key : {$key} -> User doesn't exist!");

                    $return['code'] = 3;
                    return $return;
                } else {
                    if ($row['password'] == $password) {
                        $this->addAttempt($ip);

                        $this->addNewLog($data['uid'], "RESETPASS_FAIL_SAMEPASS",
                            "User attempted to reset password with key : {$key} -> New password matches previous password!");

                        $this->deleteUserResets($data['uid']);

                        $return['code'] = 4;
                        return $return;
                    } else {
                        $query = $this->dbh->prepare("UPDATE {$this->config->table_users} SET password = ? WHERE id = ?");
                        $return = $query->execute(array($password, $data['uid']));

                        if (!$return) {
                            return false;
                        }

                        $this->addNewLog($data['uid'], "RESETPASS_SUCCESS",
                            "User attempted to reset password with key : {$key} -> Password changed, reset keys deleted!");

                        $this->deleteUserResets($data['uid']);

                        $return['code'] = 5;
                        return $return;
                    }
                }
            } else {
                $this->addNewLog($data['uid'], "RESETPASS_FAIL_KEY",
                    "User attempted to reset password with key : {$key} -> Key is invalid / incorrect / expired!");

                $return['code'] = 2;
                return $return;
            }
        }
    }

    /**
     * Recreates activation email for a given email and sends
     * @param string $email
     * @return array $return
     */
    public function resendActivation($email)
    {
        $return = array();

        $ip = $this->getIp();

        if ($this->isBlocked($ip)) {
            $return['code'] = 0;
            return $return;
        } else {
            if (strlen($email) == 0) {
                $return['code'] = 1;
                $this->addAttempt($ip);
                return $return;
            } elseif (strlen($email) > 100) {
                $return['code'] = 1;
                $this->addAttempt($ip);
                return $return;
            } elseif (strlen($email) < 3) {
                $return['code'] = 1;
                $this->addAttempt($ip);
                return $return;
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $return['code'] = 1;
                $this->addAttempt($ip);
                return $return;
            } else {
                $query = $this->dbh->prepare("SELECT id FROM {$this->config->table_users} WHERE email = ?");
                $query->execute(array($email));
                $row = $query->fetch(\PDO::FETCH_ASSOC);

                if (!$row) {
                    $this->addAttempt($ip);

                    $this->addNewLog("", "RESENDACTIVATION_FAIL_EMAIL",
                        "User attempted to resend activation email for the email : {$email} -> Email doesn't exist in DB!");

                    $return['code'] = 2;
                    return $return;
                } else {
                    if ($this->isUserActivated($row['uid'])) {
                        $this->addAttempt($ip);

                        $this->addNewLog($row['uid'], "RESENDACTIVATION_FAIL_ACTIVATED",
                            "User attempted to resend activation email for the email : {$email} -> Account is already activated!");

                        $return['code'] = 3;
                        return $return;
                    } else {
                        if ($this->addActivation($row['uid'], $email)) {
                            $this->addNewLog($row['uid'], "RESENDACTIVATION_SUCCESS",
                                "Activation email was resent to the email : {$email}");

                            $return['code'] = 5;
                            return $return;
                        } else {
                            $this->addAttempt($ip);

                            $this->addNewLog($row['uid'], "RESENDACTIVATION_FAIL_EXIST",
                                "User attempted to resend activation email for the email : {$email} -> Activation request already exists. 24 hour expire wait required!");

                            $return['code'] = 4;
                            return $return;
                        }
                    }
                }
            }
        }
    }

    /**
     * Gets UID from Session hash
     * @param string $hash
     * @return int $uid
     */
    public function sessionUID($hash)
    {
        if (strlen($hash) != 40) {
            return false;
        } else {
            $query = $this->dbh->prepare("SELECT uid FROM {$this->config->table_sessions} WHERE hash = ?");
            $query->execute(array($hash));
            $row = $query->fetch(\PDO::FETCH_ASSOC);

            if (!$row) {
                return false;
            } else {
                return $row['uid'];
            }
        }
    }

    /**
     * Changes a user's password
     * @param int $uid
     * @param string $currpass
     * @param string $newpass
     * @return array $return
     */
    public function changePassword($uid, $currpass, $newpass)
    {
        $return = array();

        $ip = $this->getIp();

        if ($this->isBlocked($ip)) {
            $return['code'] = 0;
            return $return;
        } else {
            if (strlen($currpass) != 40) {
                $return['code'] = 1;
                $this->addAttempt($ip);
                return $return;
            } elseif (strlen($newpass) != 40) {
                $return['code'] = 1;
                $this->addAttempt($ip);
                return $return;
            } else {
                $currpass = $this->getHash($currpass);
                $newpass = $this->getHash($newpass);

                $query = $this->dbh->prepare("SELECT password FROM {$this->config->table_users} WHERE id = ?");
                $query->execute(array($uid));
                $row = $query->fetch(\PDO::FETCH_ASSOC);

                if (!$row) {
                    $this->addAttempt($ip);

                    $this->addNewLog($uid, "CHANGEPASS_FAIL_UID",
                        "User attempted to change password for the UID : {$uid} -> UID doesn't exist!");

                    $return['code'] = 2;
                    return $return;
                } else {
                    if ($currpass != $newpass) {
                        if ($currpass == $row['password']) {
                            $query = $this->dbh->prepare("UPDATE {$this->config->table_users} SET password = ? WHERE id = ?");
                            $query->execute(array($newpass, $uid));

                            $this->addNewLog($uid, "CHANGEPASS_SUCCESS",
                                "User changed the password for the UID : {$uid}");

                            $return['code'] = 5;
                            return $return;
                        } else {
                            $this->addAttempt($ip);

                            $this->addNewLog($uid, "CHANGEPASS_FAIL_PASSWRONG",
                                "User attempted to change password for the UID : {$uid} -> Current password incorrect!");

                            $return['code'] = 4;
                            return $return;
                        }
                    } else {
                        $this->addAttempt($ip);

                        $this->addNewLog($uid, "CHANGEPASS_FAIL_PASSMATCH",
                            "User attempted to change password for the UID : {$uid} -> New password matches current password!");

                        $return['code'] = 3;
                        return $return;
                    }
                }
            }
        }
    }

    /**
     * Gets a user's email address by UID
     * @param int $uid
     * @return string $email
     */
    public function getEmail($uid)
    {
        $query = $this->dbh->prepare("SELECT email FROM {$this->config->table_users} WHERE id = ?");
        $query->execute(array($uid));
        $row = $query->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return false;
        } else {
            return $row['email'];
        }
    }

    /**
     * Changes a user's email
     * @param int $uid
     * @param string $email
     * @param string $password
     * @return array $return
     */
    public function changeEmail($uid, $email, $password)
    {
        $return = array();

        $ip = $this->getIp();

        if ($this->isBlocked($ip)) {
            $return['code'] = 0;
            return $return;
        } else {
            if (strlen($email) == 0) {
                $return['code'] = 1;
                $this->addAttempt($ip);
                return $return;
            } elseif (strlen($email) > 100) {
                $return['code'] = 1;
                $this->addAttempt($ip);
                return $return;
            } elseif (strlen($email) < 3) {
                $return['code'] = 1;
                $this->addAttempt($ip);
                return $return;
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $return['code'] = 1;
                $this->addAttempt($ip);
                return $return;
            } elseif (strlen($password) != 40) {
                $return['code'] = 1;
                $this->addAttempt($ip);
                return $return;
            } else {
                $password = $this->getHash($password);

                $query = $this->dbh->prepare("SELECT password, email FROM {$this->config->table_users} WHERE id = ?");
                $query->execute(array($uid));
                $row = $query->fetch(\PDO::FETCH_ASSOC);

                if (!$row) {
                    $this->addAttempt($ip);

                    $this->addNewLog($uid, "CHANGEEMAIL_FAIL_UID",
                        "User attempted to change email for the UID : {$uid} -> UID doesn't exist!");

                    $return['code'] = 2;
                    return $return;
                } else {
                    if ($password == $row['password']) {
                        if ($email == $row['email']) {
                            $this->addAttempt($ip);

                            $this->addNewLog($uid, "CHANGEEMAIL_FAIL_EMAILMATCH",
                                "User attempted to change email for the UID : {$uid} -> New Email address matches current email!");

                            $return['code'] = 4;
                            return $return;
                        } else {
                            $query = $this->dbh->prepare("UPDATE {$this->config->table_users} SET email = ? WHERE id = ?");
                            $row = $query->execute(array($email, $uid));

                            if (!$row) {
                                return false;
                            }

                            $this->addNewLog($uid, "CHANGEEMAIL_SUCCESS",
                                "User changed email address for UID : {$uid}");

                            $return['code'] = 5;
                            return $return;
                        }
                    } else {
                        $this->addAttempt($ip);

                        $this->addNewLog($uid, "CHANGEEMAIL_FAIL_PASS",
                            "User attempted to change email for the UID : {$uid} -> Password is incorrect!");

                        $return['code'] = 3;
                        return $return;
                    }
                }
            }
        }
    }

    /**
     * Informs if a user is locked out
     * @param string $ip
     * @return boolean
     */
    public function isBlocked($ip)
    {
        $query = $this->dbh->prepare("SELECT count, expiredate FROM {$this->config->table_attempts} WHERE ip = ?");
        $query->execute(array($ip));
        $row = $query->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return false;
        } else {
            if ($row['count'] == 5) {
                $expiredate = strtotime($row['expiredate']);
                $currentdate = strtotime(date("Y-m-d H:i:s"));

                if ($currentdate < $expiredate) {
                    return true;
                } else {
                    $this->deleteAttempts($ip);
                    return false;
                }
            } else {
                $expiredate = strtotime($row['expiredate']);
                $currentdate = strtotime(date("Y-m-d H:i:s"));

                if ($currentdate < $expiredate) {
                    return false;
                } else {
                    $this->deleteAttempts($ip);
                    return false;
                }
                return false;
            }
        }
    }

    /**
     * Deletes all attempts for a given IP from database
     * @param string $ip
     * @return boolean
     */
    private function deleteAttempts($ip)
    {
        $query = $this->dbh->prepare("DELETE FROM {$this->config->table_attempts} WHERE ip = ?");
        $return = $query->execute(array($ip));

        return $return;
    }

    /**
     * Adds an attempt to database for given IP
     * @param string $ip
     * @return boolean
     */
    private function addAttempt($ip)
    {
        $query = $this->dbh->prepare("SELECT count FROM {$this->config->table_attempts} WHERE ip = ?");
        $query->execute(array($ip));
        $row = $query->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            $attempt_expiredate = date("Y-m-d H:i:s", strtotime("+30 minutes"));
            $attempt_count = 1;

            $query = $this->dbh->prepare("INSERT INTO {$this->config->table_attempts} (ip, count, expiredate) VALUES (?, ?, ?)");
            $return = $query->execute(array(
                $ip,
                $attempt_count,
                $attempt_expiredate));

            return $return;
        } else {
            $attempt_expiredate = date("Y-m-d H:i:s", strtotime("+30 minutes"));
            $attempt_count = $row['count'] + 1;

            $query = $this->dbh->prepare("UPDATE {$this->config->table_attempts} SET count=?, expiredate=? WHERE ip=?");
            $return = $query->execute(array(
                $attempt_count,
                $attempt_expiredate,
                $ip));

            return $return;
        }
    }

    /**
     * Returns a random string, length can be modified
     * @param int $length
     * @return string $key
     */
    public function getRandomKey($length = 20)
    {
        $chars = "_A1B2C3D4E5F6G7H8I9J0K1L2M3N4O5P6Q7R8S9T0U1V2W3X4Y5Z6_a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0u1v2w3x4y5z6_";
        $key = "";

        for ($i = 0; $i < $length; $i++) {
            $key .= $chars{mt_rand(0, strlen($chars) - 1)};
        }

        return $key;
    }

    /**
     * Returns ip address
     * @return string $ip
     * @source http://stackoverflow.com/questions/1634782/what-is-the-most-accurate-way-to-retrieve-a-users-correct-ip-address-in-php?rq=1
     */
    private function getIp()
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED']) && $this->validate_ip($_SERVER['HTTP_X_FORWARDED']))
            return $_SERVER['HTTP_X_FORWARDED'];
        if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && $this->validate_ip($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
            return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && $this->validate_ip($_SERVER['HTTP_FORWARDED_FOR']))
            return $_SERVER['HTTP_FORWARDED_FOR'];
        if (!empty($_SERVER['HTTP_FORWARDED']) && $this->validate_ip($_SERVER['HTTP_FORWARDED']))
            return $_SERVER['HTTP_FORWARDED'];

        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Validates a given IP Address
     * @param string $ip
     * @return boolean
     */
    function validate_ip($ip)
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 |
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return false;
        }
        self::$ip = $ip;

        return true;
    }

    /**
     * Gets a user's level by UID
     * @param int $uid
     * @return int $level
     */
    public function getLevel($uid)
    {
        $query = $this->dbh->prepare("SELECT level FROM {$this->config->table_users} WHERE id = ?");
        $query->execute(array($uid));
        $row = $query->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return false;
        } else {
            return $row['level'];
        }
    }

    /**
     * Puts a user's level by UID
     * @param string $hash
     * @param int $uid
     * @param int $uid
     * @return boolean
     */
    public function putLevel($hash, $uid, $level)
    {
        $admin_uid = $this->sessionUID($hash);
        $admin_level = $this->getLevel($admin_uid);

        if ($admin_level >= $this->config->admin_level) {
            return false;
        } else {
            $query = $this->dbh->prepare("UPDATE {$this->config->table_users} SET level = ? WHERE id = ?");
            $query->execute(array($level, $uid));

            if ($query->rowCount() == 0) {
                return false;
            } else {
                return true;
            }
        }
    }

    /**
     * Returns language based on session hash
     * @param string $hash
     * @return string $language
     */
    public function getLang($hash)
    {
        $query = $this->dbh->prepare("SELECT lang FROM {$this->config->table_sessions} WHERE hash = ?");
        $query->execute(array($hash));
        $row = $query->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return "en";
        } else {
            return $row['lang'];
        }
    }

    /**
     * Puts a user's language based on session hash
     * @param string $hash
     * @param string $lang
     * @return string $language
     */
    public function putLang($hash, $lang)
    {
        $query = $this->dbh->prepare("UPDATE {$this->config->table_sessions} SET lang = ? WHERE hash = ?");
        $query->execute(array($lang, $hash));

        if ($query->rowCount() == 0) {
            return false;
        } else {
            $uid = $this->sessionUID($hash);
            $query = $this->dbh->prepare("UPDATE {$this->config->table_users} SET lang = ? WHERE id = ?");
            $query->execute(array($lang, $uid));

            if ($query->rowCount() == 0) {
                return false;
            } else {
                return true;
            }
        }
    }

    /**
     * Detects language based on HTTP_ACCEPT_LANGUAGE
     * @return string $language
     */
    public function detectLang()
    {
        $language_accepted = $this->config->lang_list;

        $accept_lang = preg_split('",|;"', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

        if (in_array($accept_lang[0], $language_accepted)) {
            $this->config->lang = $accept_lang[0];
        }

        return $this->config->lang;
    }
}
?>