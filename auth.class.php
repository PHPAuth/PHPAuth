<?php

/*
* Auth class
* Functions with PHP 5.3.7 and above.
*/

class Auth
{
    private $dbh;
    private $config;

    /*
    * Initiates database connection
    */

    public function __construct(\PDO $dbh, $config)
    {
        $this->config = $config;
        $this->dbh = $dbh;

        if (version_compare(phpversion(), '5.5.0', '<')) {
            require("password.php");
        }
    }

    /*
    * Logs a user in
    * @param string $username
    * @param string $password
    * @param bool $remember
    * @return array $return
    */

    public function login($username, $password, $remember = 0)
    {
        $return = array();
        $return['code'] = 400;

        if ($this->isBlocked()) {
            $return['message'] = "user_blocked";

            return $return;
        } else {
            $validateUsername = $this->validateUsername($username);
            $validatePassword = $this->validatePassword($password);

            if ($validateUsername['error'] == 1) {
                $return['message'] = "username_password_invalid";

                $this->addAttempt();
                return $return;
            } elseif($validatePassword['error'] == 1) {
                $return['message'] = "username_password_invalid";

                $this->addAttempt();
                return $return;
            } elseif($remember != 0 && $remember != 1) {
                $return['message'] = "remember_me_invalid";

                $this->addAttempt();
                return $return;
            } else {
                $username = strtolower($username);
                $uid = $this->getUID($username);

                if($uid) {
                    $user = $this->getUser($uid);
                } else {
                    $user = false;
                }

                if($user) {
                    if (password_verify($password, $user['password'])) {
                        if ($user['isactive'] == 1) {
                            
                            $sessiondata = $this->addSession($user['uid'], $remember);

                            if($sessiondata == false) {
                                $return['message'] = "system_error";
                                return $return;
                            } else {
                                $return['code'] = 200;
                                $return['message'] = "logged_in";

                                $return['hash'] = $sessiondata['hash'];
                                $return['expire'] = $sessiondata['expiretime'];

                                return $return;
                            }

                        } else {
                            $this->addAttempt();

                            $this->addNewLog($uid, "LOGIN_ACCOUNT_INACTIVE", "");

                            $return['message'] = "account_inactive";
                            return $return;
                        }
                    } else {
                        $this->addAttempt();

                        $this->addNewLog(0, "LOGIN_PASSWORD_INCORRECT", "Username : {$username}");

                        $return['message'] = "username_password_incorrect";
                        return $return;
                    }
                } else {
                    $this->addAttempt();

                    $this->addNewLog(0, "LOGIN_USERNAME_INCORRECT", "Username : {$username}");

                    $return['message'] = "username_password_incorrect";
                    return $return;
                }
            }
        }
    }

    /*
    * Creates a new user, adds them to database
    * @param string $email
    * @param string $username
    * @param string $password
    * @param string $repeatpassword
    * @return array $return
    */

    public function register($email, $username, $password, $repeatpassword)
    {
        $return = array();
        $return['code'] = 400;

        if ($this->isBlocked()) {
            $return['code'] = 0;
            return $return;
        } else {
            $validateEmail = $this->validateEmail($email);
            $validateUsername = $this->validateUsername($username);
            $validatePassword = $this->validatePassword($password);

            if ($validateEmail['error'] == 1) {
                $return['message'] = $validateEmail['message'];
                return $return;
            } elseif ($validateUsername['error'] == 1) {
                $return['message'] = $validateUsername['message'];
                return $return;
            } elseif ($validatePassword['error'] == 1) {
                $return['message'] = $validatePassword['message'];
                return $return;
            } elseif($password !== $repeatpassword) {
                $return['message'] = "password_nomatch";
                return $return;
            } else {
                if (!$this->isEmailTaken($email)) {
                    if (!$this->isUsernameTaken($username)) {
                        $addUser = $this->addUser($email, $username, $password);

                        if($addUser['error'] == 0) {
                            $return['code'] = 200;
                            $return['message'] = "register_success";
                            return $return;
                        } else {
                            $return['message'] = $addUser['message'];
                            return $return;
                        }
                    } else {
                        $this->addAttempt();

                        $this->addNewLog("", "REGISTER_FAIL_USERNAME", "User attempted to register new account with the username : {$username} -> Username already in use");

                        $return['message'] = "username_taken";
                        return $return;
                    }
                } else {
                    $this->addAttempt();

                    $this->addNewLog("", "REGISTER_FAIL_EMAIL", "User attempted to register new account with the email : {$email} -> Email already in use");

                    $return['message'] = "email_taken";
                    return $return;
                }
            }
        }
    }

    /*
    * Activates a user's account
    * @param string $key
    * @return array $return
    */

    public function activate($key)
    {
        $return = array();
        $return['code'] = 400;

        if($this->isBlocked()) {
            $return['message'] = "user_blocked";
            return $return;
        } else {
            if(strlen($key) !== 20) {
                $this->addAttempt();

                $return['message'] = "key_invalid";
                return $return;
            } else {
                $getRequest = $this->getRequest($key, "activation");

                if($getRequest['error'] == 0) {
                    if($this->getUser($getRequest['uid'])['isactive'] == 0) {
                        $query = $this->dbh->prepare("UPDATE {$this->config->table_users} SET isactive = ? WHERE id = ?");
                        $query->execute(array(1, $getRequest['uid']));

                        $this->deleteRequest($getRequest['id']);

                        $this->addNewLog($getRequest['uid'], "ACTIVATE_SUCCESS", "");

                        $return['code'] = 200;
                        $return['message'] = "account_activated";
                        return $return;
                    } else {
                        $this->addAttempt();

                        $this->deleteRequest($getRequest['id']);

                        $this->addNewLog($getRequest['uid'], "ACTIVATE_FAIL_ALREADYACTIVE", "Key : {$key}");

                        $return['message'] = "system_error";
                        return $return;
                    }
                } else {
                    $return['message'] = $getRequest['message'];
                    return $return;
                }
            }
        }
    }

    /*
    * Creates a reset key for an email address and sends email
    * @param string $email
    * @return array $return
    */

    public function requestReset($email)
    {
        $return = array();
        $return['code'] = 400;

        if ($this->isBlocked()) {
            $return['message'] = "user_blocked";
            return $return;
        } else {
            $validateEmail = $this->validateEmail($email);

            if ($validateEmail['error'] == 1) {
                $return['message'] = "email_invalid";
                return $return;
            } else {
                $query = $this->dbh->prepare("SELECT id FROM {$this->config->table_users} WHERE email = ?");
                $query->execute(array($email));

                if ($query->rowCount() == 0) {
                    $this->addAttempt();

                    $this->addNewLog("", "REQUESTRESET_FAIL_EMAIL", "User attempted to reset the password for the email : {$email} -> Email doesn't exist in DB");

                    $return['message'] = "email_incorrect";
                    return $return;
                } else {
                    $row = $query->fetch(PDO::FETCH_ASSOC);

                    $addRequest = $this->addRequest($row['id'], $email, "reset");

                    if ($addRequest['error'] == 0) {
                        $this->addNewLog($row['id'], "REQUESTRESET_SUCCESS", "A reset request was sent to the email : {$email}");

                        $return['code'] = 200;
                        $return['message'] = "reset_requested";

                        return $return;
                    } else {
                        $this->addAttempt();

                        $this->addNewLog($row['id'], "REQUESTRESET_FAIL", "");

                        $return['message'] = $addRequest['message'];
                        return $return;
                    }
                }
            }
        }
    }

    /*
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

        return $return;
    }

    /*
    * Hashes string using multiple hashing methods, for enhanced security
    * @param string $string
    * @param string $salt
    * @return string $hash
    */

    public function getHash($string, $salt)
    {
        return password_hash($string, PASSWORD_BCRYPT, ['salt' => $salt, 'cost' => $this->config->bcrypt_cost]);
    }

    /*
    * Gets user data for a given username and returns an array
    * @param string $username
    * @return array $data
    */

    public function getUID($username)
    {
        $query = $this->dbh->prepare("SELECT id FROM {$this->config->table_users} WHERE username = ?");
        $query->execute(array($username));

        if($query->rowCount() == 0) {
            return false;
        } else {
            $row = $query->fetch(PDO::FETCH_ASSOC);

            return $row['id'];
        }
    }

    /*
    * Creates a session for a specified user id
    * @param int $uid
    * @param boolean $remember
    * @return array $data
    */

    private function addSession($uid, $remember)
    {
        $ip = $this->getIp();

        if($user = $this->getUser($uid)) {

            $data['hash'] = sha1($user['salt'] . microtime());
            $agent = $_SERVER['HTTP_USER_AGENT'];

            $this->deleteExistingSessions($uid);

            if($remember == true) {
                $data['expire'] = date("Y-m-d H:i:s", strtotime($this->config->duration_remember));
                $data['expiretime'] = strtotime($data['expire']);
            } else {
                $data['expire'] = date("Y-m-d H:i:s", strtotime($this->config->duration_non_remember));
                $data['expiretime'] = 0;
            }

            $data['cookie_crc'] = sha1($data['hash'] . $this->config->sitekey);

            $query = $this->dbh->prepare("INSERT INTO {$this->config->table_sessions} (uid, hash, expiredate, ip, agent, cookie_crc) VALUES (?, ?, ?, ?, ?, ?)");
            
            if($query->execute(array($uid, $data['hash'], $data['expire'], $ip, $agent, $data['cookie_crc']))) {
                $data['expire'] = strtotime($data['expire']);

                return $data;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /*
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

    /*
    * Removes a session based on hash
    * @param string $hash
    * @return boolean
    */

    private function deleteSession($hash)
    {
        $query = $this->dbh->prepare("DELETE FROM {$this->config->table_sessions} WHERE hash = ?");
        $return = $query->execute(array($hash));

        $this->addNewLog(0, "SESSION_DELETED", "Session deteted : {$hash}");

        return $return;
    }

    /*
    * Returns username based on session hash
    * @param string $hash
    * @return string $username
    */

    public function getSessionUID($hash)
    {
        $query = $this->dbh->prepare("SELECT uid FROM {$this->config->table_sessions} WHERE hash = ?");
        $query->execute(array($hash));

        if ($query->rowCount() == 0) {
            return false;
        } else {
            $row = $query->fetch(PDO::FETCH_ASSOC);

            return $row['uid'];
        }
    }

    /*
    * Function to add data to log table
    * @param string $uid
    * @param string $action
    * @param string $info
    * @param return boolean
    */

    public function addNewLog($uid = 0, $action, $info)
    {
        $ip = $this->getIp();

        if (strlen($action) == 0) {
            return false;
        } elseif (strlen($action) > 100) {
            return false;
        } elseif (strlen($info) == 0) {
            return false;
        } elseif (strlen($info) > 1000) {
            return false;
        } else {
            

            $query = $this->dbh->prepare("INSERT INTO {$this->config->table_log} (uid, action, info, ip) VALUES (?, ?, ?, ?)");
            $return = $query->execute(array(
                $uid,
                $action,
                $info,
                $ip));

            return $return;
        }
    }

    /*
    * Function to check if a session is valid
    * @param string $hash
    * @return boolean
    */

    public function checkSession($hash)
    {
        $ip = $this->getIp();

        if ($this->isBlocked()) {
            return false;
        } else {
            if (strlen($hash) != 40) {
                return false;
            }

            $query = $this->dbh->prepare("SELECT id, uid, expiredate, ip, agent, cookie_crc FROM {$this->config->table_sessions} WHERE hash = ?");
            $query->execute(array($hash));

            if ($query->rowCount() == 0) {
                $this->addNewLog(0, "CHECKSESSION_FAIL_NOEXIST", "Hash ({$hash}) doesn't exist in DB -> Cookie deleted");

                return false;
            } else {
                $row = $query->fetch(\PDO::FETCH_ASSOC);

                $sid = $row['id'];
                $uid = $row['uid'];
                $expiredate = $row['expiredate'];
                $db_ip = $row['ip'];
                $db_agent = $row['agent'];
                $db_cookie = $row['cookie_crc'];

                if ($ip != $db_ip) {
                    if ($_SERVER['HTTP_USER_AGENT'] != $db_agent) {
                        $this->deleteExistingSessions($uid);

                        $this->addNewLog($uid, "CHECKSESSION_FAIL_DIFF", "IP and User Agent Different ( DB : {$db_ip} / Current : " . $ip . " ) -> UID sessions deleted, cookie deleted");

                        return false;
                    } else {
                        $expiredate = strtotime($expiredate);
                        $currentdate = strtotime(date("Y-m-d H:i:s"));

                        if ($currentdate > $expiredate) {
                            $this->deleteExistingSessions($uid);

                            $this->addNewLog($uid, "CHECKSESSION_FAIL_EXPIRE", "Session expired ( Expire date : {$row['expiredate']} ) -> UID sessions deleted, cookie deleted");

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

                        $this->addNewLog($uid, "AUTH_CHECKSESSION_FAIL_EXPIRE", "Session expired ( Expire date : {$row['expiredate']} ) -> UID sessions deleted, cookie deleted");

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

    /*
    * Updates the IP of a session (used if IP has changed, but agent has remained unchanged)
    * @param int $sid
    * @param string $ip
    * @return boolean
    */

    private function updateSessionIp($sid, $ip)
    {
        $query = $this->dbh->prepare("UPDATE {$this->config->table_sessions} SET ip = ? WHERE id = ?");
        return $query->execute(array($ip, $sid));
    }

    /*
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

    /*
    * Checks if a username is already in use
    * @param string $username
    * @return boolean
    */

    private function isUsernameTaken($username)
    {
        if($this->getUID($username)) {
            return true;
        } else {
            return false;
        }
    }

    /*
    * Adds a new user to database
    * @param string $email
    * @param string $username
    * @param string $password
    * @return int $uid
    */

    private function addUser($email, $username, $password)
    {
        $return = array();
        $return['error'] = 1;

        $query = $this->dbh->prepare("INSERT INTO {$this->config->table_users} VALUES ()");

        if($query->execute()) {
            $uid = $this->dbh->lastInsertId();
            $email = htmlentities($email);

            $addRequest = $this->addRequest($uid, $email, "activation");

            if($addRequest['error'] == 0) {
                $salt = substr(strtr(base64_encode(mcrypt_create_iv(22, MCRYPT_DEV_URANDOM)), '+', '.'), 0, 22);

                $username = htmlentities(strtolower($username));
                $password = $this->getHash($password, $salt);

                $query = $this->dbh->prepare("UPDATE {$this->config->table_users} SET username = ?, password = ?, email = ?, salt = ? WHERE id = ?");
                
                if($query->execute(array($username, $password, $email, $salt, $uid))) {
                    $return['error'] = 0;
                    return $return;
                } else {
                    // Database update failed. Delete user row.

                    $query = $this->dbh->prepare("DELETE FROM {$this->config->table_users} WHERE id = ?");
                    $query->execute(array($uid));

                    $return['message'] = "system_error";
                    return $return;
                }
            } else {
                // addRequest failed. Delete user row.

                $query = $this->dbh->prepare("DELETE FROM {$this->config->table_users} WHERE id = ?");
                $query->execute(array($uid));

                $return['message'] = $addRequest['message'];
                return $return;
            }
        } else {
            // Insert failed. Delete user row.

            $return['message'] = "system_error";
            return $return;
        }
    }

    /*
    * Gets user data for a given UID and returns an array
    * @param int $uid
    * @return array $data
    */

    public function getUser($uid)
    {
        $data = array();

        $query = $this->dbh->prepare("SELECT username, password, email, salt, isactive FROM {$this->config->table_users} WHERE id = ?");
        $query->execute(array($uid));

        if ($query->rowCount() == 0) {
            return false;
        } else {
            $data = $query->fetch(PDO::FETCH_ASSOC);

            if (!$data) {
                return false;
            } else {
                $data['uid'] = $uid;

                return $data;
            }
        }
    }

    /*
    * Allows a user to delete their account
    * @param int $uid
    * @param string $password
    * @return array $return
    */

    public function deleteUser($uid, $password) 
    {
        $return = array();
        $return['code'] = 400;

        if ($this->isBlocked()) {
            // User is locked out
            $return['message'] = "user_blocked";
            
            return $return;
        } else {
            $validatePassword = $this->validatePassword($password);
            
            if($validatePassword['error'] == 1) {
                $this->addAttempt();

                $return['message'] = $validatePassword['message'];      
                return $return;
            }
        }

        $getUser = $this->getUser($uid);

        if(password_verify($password, $getUser['password'])) {
            $query = $this->dbh->prepare("DELETE FROM {$this->config->table_users} WHERE id = ?");
            if($query->execute(array($uid))) {
                $query = $this->dbh->prepare("DELETE FROM {$this->config->table_sessions} WHERE uid = ?");
                if($query->execute(array($uid))) {
                    $query = $this->dbh->prepare("DELETE FROM {$this->config->table_requests} WHERE uid = ?");
                    if($query->execute(array($uid))) {
                        $this->addNewLog($uid, "ACCOUNT_DELETED", "Username : {$getUser['username']}");

                        $return['code'] = 200;
                        $return['message'] = "account_deleted";

                        return $return;
                    } else {
                        $return['message'] = "system_error";
                        return $return;
                    }
                } else {
                    $return['message'] = "system_error";
                    return $return;
                }
            } else {
                $return['message'] = "system_error";
                return $return;
            }
        } else {
            $this->addAttempt();

            $return['message'] = "password_incorrect";
            return $return;
        }
    }

    /*
    * Creates an activation entry and sends email to user
    * @param int $uid
    * @param string $email
    * @return boolean
    */

    private function addRequest($uid, $email, $type)
    {
        $return = array();
        $return['error'] = 1;

        if($type != "activation" && $type != "reset") {
            $return['message'] = "system_error";
            return $return;
        }

        $query = $this->dbh->prepare("SELECT id, expire FROM {$this->config->table_requests} WHERE uid = ? AND type = ?");
        $query->execute(array($uid, $type));

        if($query->rowCount() > 0) {
            $row = $query->fetch(PDO::FETCH_ASSOC);

            $expiredate = strtotime($row['expire']);
            $currentdate = strtotime(date("Y-m-d H:i:s"));

            if ($currentdate < $expiredate) {
                $return['message'] = "request_exists";
                return $return;
            } else {
                $this->deleteRequest($row['id']);
            }
        }

        if($type == "activation" && $this->getUser($uid)['isactive'] == 1) {
            $return['message'] = "already_activated";
            return $return;
        }

        $key = $this->getRandomKey(20);
        $expire = date("Y-m-d H:i:s", strtotime("+1 day"));

        $query = $this->dbh->prepare("INSERT INTO {$this->config->table_requests} (uid, rkey, expire, type) VALUES (?, ?, ?, ?)");

        if($query->execute(array($uid, $key, $expire, $type))) {
            if($type == "activation") {
                $message = "Account activation required : <strong><a href=\"{$this->config->authurl}/auth/#/activate/{$key}\">Activate my account</a></strong>";

                $headers  = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headers .= "From: {$this->config->fromemail}" . "\r\n";

                if(@mail($email, "{$this->config->sitename} - Account Activation", $message, $headers)) {
                    $return['error'] = 0;
                    return $return;
                } else {
                    $return['message'] = "system_error";
                }
            } else {
                $message = "Password reset request : <strong><a href=\"{$this->config->authurl}/auth/#/reset/{$key}\">Reset my password</a></strong>";

                $headers  = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headers .= "From: {$this->config->fromemail}" . "\r\n";

                if(@mail($email, "{$this->config->sitename} - Password Reset Request", $message, $headers)) {
                    $return['error'] = 0;
                    return $return;
                } else {
                    $return['message'] = "system_error";
                }
            }
        } else {
            $return['message'] = "system_error";
            return $return;
        }
    }

    /*
    * Returns request data if key is valid
    * @param string $key
    * @param string $type
    * @return array $return
    */

    private function getRequest($key, $type)
    {
        $return = array();
        $return['error'] = 1;

        $query = $this->dbh->prepare("SELECT id, uid, expire FROM {$this->config->table_requests} WHERE rkey = ? AND type = ?");
        $query->execute(array($key, $type));

        if ($query->rowCount() === 0) {
            $this->addAttempt();

            $return['message'] = "key_incorrect";
            return $return;
        }

        $row = $query->fetch();

        $expiredate = strtotime($row['expire']);
        $currentdate = strtotime(date("Y-m-d H:i:s"));

        if ($currentdate > $expiredate) {
            $this->addAttempt();

            $this->deleteRequest($row['id']);

            $return['message'] = "key_expired";
            return $return;
        } else {
            $return['error'] = 0;
            $return['id'] = $row['id'];
            $return['uid'] = $row['uid'];
            return $return;
        }
    }

    /*
    * Deletes request from database
    * @param int $id
    * @return boolean
    */

    private function deleteRequest($id)
    {
        $query = $this->dbh->prepare("DELETE FROM {$this->config->table_requests} WHERE id = ?");
        return $query->execute(array($id));
    }

    /*
    * Verifies that a username is valid
    * @param string $username
    * @return array $return
    */

    public function validateUsername($username) {
        $return = array();
        $return['error'] = 1;

        if (strlen($username) < 3) {
            $return['message'] = "username_short";
            $this->addNewLog(0, "USERNAME_SHORT", "Username : {$username}");
        } elseif (strlen($username) > 30) {
            $return['message'] = "username_long";
            $this->addNewLog(0, "USERNAME_LONG", "Username : {$username}");
        } elseif (!ctype_alnum($username)) {
            $return['message'] = "username_invalid";
            $this->addNewLog(0, "USERNAME_INVALID", "Username : {$username}");
        } else {
            $return['error'] = 0;
        }
    }

    /*
    * Verifies that a password is valid and respects security requirements
    * @param string $password
    * @return array $return
    */

    private function validatePassword($password) {
        $return = array();
        $return['error'] = 1;

        if (strlen($password) < 6) {
            $return['message'] = "password_short";
            $this->addNewLog(0, "PASSWORD_SHORT", "");
        } elseif (strlen($password) > 72) {
            $return['message'] = "password_long";
            $this->addNewLog(0, "PASSWORD_LONG", "");
        } elseif (!preg_match('@[A-Z]@', $password) || !preg_match('@[a-z]@', $password) || !preg_match('@[0-9]@', $password)) {
            $return['message'] = "password_invalid";
            $this->addNewLog(0, "PASSWORD_INVALID", "");
        } else {
            $return['error'] = 0;
        }

        return $return;
    }

    /*
    * Verifies that an email is valid
    * @param string $email
    * @return array $return
    */

    private function validateEmail($email) {
        $return = array();
        $return['error'] = 1;

        if (strlen($email) < 5) {
            $return['message'] = "email_short";
            $this->addNewLog(0, "EMAIL_SHORT", "Email : {$email}");
        } elseif (strlen($email) > 100) {
            $return['message'] = "email_long";
            $this->addNewLog(0, "EMAIL_LONG", "Email : {$email}");
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $return['message'] = "email_invalid";
            $this->addNewLog(0, "EMAIL_INVALID", "Email : {$email}");
        } else {
            $return['error'] = 0;
        }

        return $return;
    }

    /*
    * Allows a user to reset there password after requesting a reset key.
    * @param string $key
    * @param string $password
    * @param string $repeatpassword
    * @return array $return
    */

    public function resetPass($key, $password, $repeatpassword)
    {
        $return = array();
        $return['code'] = 400;

        if ($this->isBlocked()) {
            $return['message'] = "user_blocked";
            return $return;
        }

        if(strlen($key) != 20) {
            $return['message'] = "key_invalid";
            return $return;
        }

        $validatePassword = $this->validatePassword($password);

        if($validatePassword['error'] == 0) {

            if($password !== $repeatpassword) {
                // Passwords don't match
                $return['message'] = "newpassword_nomatch";
                return $return;
            }

            $data = $this->getRequest($key, "reset");

            if ($data['error'] == 0) {
                if ($user = $this->getUser($data['uid'])) {
                    if (password_verify($password, $user['password'])) {
                        
                        $this->addAttempt();
                        $this->addNewLog($data['uid'], "RESETPASS_FAIL_SAMEPASS", "User attempted to reset password with key : {$key} -> New password matches previous password !");
                        $this->deleteRequest($data['id']);

                        // Password matches previous password
                        $return['message'] = "newpassword_match";
                        return $return;
                    } else {
                        $password = $this->getHash($password, $user['salt']);

                        $query = $this->dbh->prepare("UPDATE {$this->config->table_users} SET password = ? WHERE id = ?");
                        $query->execute(array($password, $data['uid']));

                        if ($query->rowCount() == 0) {
                            // Error changing password
                            $return['message'] = "system_error";
                            return $return;
                        } else {
                            $this->addNewLog($data['uid'], "RESETPASS_SUCCESS","User attempted to reset password with key : {$key} -> Password changed, reset keys deleted !");

                            $this->deleteRequest($data['id']);

                            // Password changed successfully
                            $return['code'] = 200;
                            $return['message'] = "password_reset";
                            return $return;
                        }
                    }
                } else {
                    $this->addAttempt();

                    $this->deleteRequest($data['id']);

                    $this->addNewLog($data['uid'], "RESETPASS_FAIL_UID", "User attempted to reset password with key : {$key} -> User doesn't exist !");

                    // User no longer exists
                    $return['message'] = "system_error";
                    return $return;
                }
            } else {
                $return['message'] = $data['message'];
            }
        } else {
            $return['message'] = $validatePassword['message'];
        }
    }

    /*
    * Recreates activation email for a given email and sends
    * @param string $email
    * @return array $return
    */

    public function resendActivation($email)
    {
        $return = array();
        $return['code'] = 400;

        if ($this->isBlocked()) {
            $return['message'] = "user_blocked";
            return $return;
        } else {
            $validateEmail = $this->validateEmail($email);

            if($validateEmail['error'] == 1) {
                $return['message'] = $validateEmail['message'];
                return $return;
            } else {
                $query = $this->dbh->prepare("SELECT id FROM {$this->config->table_users} WHERE email = ?");
                $query->execute(array($email));

                if($query->rowCount() == 0) {
                    $this->addAttempt();

                    $this->addNewLog("", "RESENDACTIVATION_FAIL_EMAIL", "User attempted to resend activation email for the email : {$email} -> Email doesn't exist in DB !");

                    $return['message'] = "email_incorrect";
                    return $return;
                } else {
                    $row = $query->fetch(\PDO::FETCH_ASSOC);

                    if ($this->getUser($row['id'])['isactive'] == 1) {
                        $this->addAttempt();

                        $this->addNewLog($row['id'], "RESENDACTIVATION_FAIL_ACTIVATED","User attempted to resend activation email for the email : {$email} -> Account is already activated !");

                        $return['message'] = "already_activated";
                        return $return;
                    } else {
                        $addRequest = $this->addRequest($row['id'], $email, "activation");

                        if ($addRequest['error'] == 0) {
                            $this->addNewLog($row['id'], "RESENDACTIVATION_SUCCESS","Activation email was resent to the email : {$email}");

                            $return['code'] = 200;
                            $return['message'] = "activation_sent";
                            return $return;
                        } else {
                            $this->addAttempt();

                            $this->addNewLog($row['id'], "RESENDACTIVATION_FAIL_EXIST","User attempted to resend activation email for the email : {$email} -> Activation request already exists. 24 hour expire wait required !");

                            $return['message'] = $addRequest['message'];
                            return $return;
                        }
                    }
                }
            }
        }
    }

    /*
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
            
            if($query->rowCount() == 0) {
                return false;
            } else {
                $row = $query->fetch(\PDO::FETCH_ASSOC);
                return $row['uid'];
            }
        }
    }

    /*
    * Changes a user's password
    * @param int $uid
    * @param string $currpass
    * @param string $newpass
    * @return array $return
    */

    public function changePassword($uid, $currpass, $newpass, $repeatnewpass)
    {
        $return = array();
        $return['code'] = 400;

        if ($this->isBlocked()) {
            // User is locked out
            $return['message'] = "user_blocked";
			
            return $return;
        } else {
            $validatePassword = $this->validatePassword($currpass);
			
            if($validatePassword['error'] == 1) {
				$this->addAttempt();

                $return['message'] = $validatePassword['message'];		
				return $return;
			}

            $validatePassword = $this->validatePassword($newpass);

            if($validatePassword['error'] == 1) {
                $return['message'] = "new" . $validatePassword['message'];      
                return $return;
            } elseif($newpass !== $repeatnewpass) {
                // New passwords don't match
                $return['message'] = "newpassword_nomatch";
                return $return;
            }

			if ($user = $this->getUser($uid)) {
                $newpass = $this->getHash($newpass, $user['salt']);

				if ($currpass != $newpass) {
					if (password_verify($currpass, $user['password'])) {
						$query = $this->dbh->prepare("UPDATE {$this->config->table_users} SET password = ? WHERE id = ?");
						$query->execute(array($newpass, $uid));

						$this->addNewLog($uid, "CHANGEPASS_SUCCESS", "User changed the password for the UID : {$uid}");

						$return['code'] = 200;
                        $return['message'] = "password_changed";
						return $return;
					} else {
						$this->addAttempt();

						$this->addNewLog($uid, "CHANGEPASS_FAIL_PASSWRONG", "User attempted to change password for the UID : {$uid} -> Current password incorrect !");

						$return['message'] = "password_incorrect";
						return $return;
					}
				} else {
					$this->addAttempt();

					$this->addNewLog($uid, "CHANGEPASS_FAIL_PASSMATCH", "User attempted to change password for the UID : {$uid} -> New password matches current password !");

					$return['message'] = "newpassword_match";
					return $return;
				}
            } else {
                $this->addAttempt();

                $this->addNewLog($uid, "CHANGEPASS_FAIL_UID", "User attempted to change password for the UID : {$uid} -> UID doesn't exist !");

                // UID is incorrect
                $return['message'] = "system_error";
                return $return;
            }
		}
    }

    /*
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

    /*
    * Changes a user's email
    * @param int $uid
    * @param string $currpass
    * @param string $newpass
    * @return array $return
    */

    public function changeEmail($uid, $email, $password)
    {
        $return = array();
        $return['code'] = 400;

        if ($this->isBlocked()) {
            // Locked out
            $return['message'] = "user_blocked";
            return $return;
        } else {
            $validateEmail = $this->validateEmail($email);
            $validatePassword = $this->validatePassword($password);

			if($validateEmail['error'] == 1)
			{
				$return['message'] = $validateEmail['message'];
				return $return;
			} elseif ($validatePassword['error'] == 1) {
                $return['message'] = "password_notvalid";
				return $return;
			} else {

				if ($user = $this->getUser($uid)) {
					if (password_verify($password, $user['password'])) {
						if ($email == $user['email']) {
							$this->addAttempt();

							$this->addNewLog($uid, "CHANGEEMAIL_FAIL_EMAILMATCH", "User attempted to change email for the UID : {$uid} -> New Email address matches current email !");

                            // New email matches current email
                            $return['message'] = "newemail_match";
							return $return;
						} else {
							$query = $this->dbh->prepare("UPDATE {$this->config->table_users} SET email = ? WHERE id = ?");
							$query->execute(array($email, $uid));

							if ($query->rowCount() == 0) {
                                $return['message'] = "system_error";
                                return $return;
                            }

							$this->addNewLog($uid, "CHANGEEMAIL_SUCCESS", "User changed email address for UID : {$uid}");

                            // Email changed
							$return['code'] = 200;
                            $return['message'] = "email_changed";
							return $return;
						}
					} else {
						$this->addAttempt();

						$this->addNewLog($uid, "CHANGEEMAIL_FAIL_PASS", "User attempted to change email for the UID : {$uid} -> Password is incorrect !");

                        // Password is incorrect
                        $return['message'] = "password_incorrect";
						return $return;
					}
				} else {
                    $this->addAttempt();

                    $this->addNewLog($uid, "CHANGEEMAIL_FAIL_UID", "User attempted to change email for the UID : {$uid} -> UID doesn't exist !");

                    // UID is incorrect
                    $return['message'] = "system_error";
                    return $return;
                }
			}
        }
    }

    /*
    * Informs if a user is locked out
    * @param string $ip
    * @return boolean
    */

    private function isBlocked()
    {
        $ip = $this->getIp();

        $query = $this->dbh->prepare("SELECT count, expiredate FROM {$this->config->table_attempts} WHERE ip = ?");
        $query->execute(array($ip));

        if($query->rowCount() == 0) {
            return false;
        }

        $row = $query->fetch(\PDO::FETCH_ASSOC);

        $expiredate = strtotime($row['expiredate']);
        $currentdate = strtotime(date("Y-m-d H:i:s"));

        if ($row['count'] == 5) {
            if ($currentdate < $expiredate) {
                return true;
            } else {
                $this->deleteAttempts($ip);
                return false;
            }
        } else {
            if ($currentdate > $expiredate) {
                $this->deleteAttempts($ip);
            }

            return false;
        }
    }

    public function getUsernameFromUID($uid)
    {
        $query = $this->dbh->prepare("SELECT username FROM users WHERE id = ?");
        $query->execute(array($uid));

        if ($query->rowCount() == 0) {
            return false;
        } else {
            $return = $query->fetch();
            return $return['username'];
        }
    }

    /*
    * Adds an attempt to database
    * @return boolean
    */

    private function addAttempt()
    {
        $ip = $this->getIp();

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

    /*
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

    /*
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

    /*
    * Returns IP address
    * @return string $ip
    */

    private function getIp()
    {
        return $_SERVER['REMOTE_ADDR'];
    }
}