<?php

// phpunit backward compatibility
use PHPUnit\Framework\TestCase;

if (!class_exists('\PHPUnit\Framework\TestCase') && class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}

class AuthTest extends TestCase
{
    /**
     * @var PHPAuth\Auth
     */
    public static $auth;

    /**
     * @var PHPAuth\Config;
     */
    public static $config;

    /**
     * @var PDO
     */
    public static $dbh;

    public static function setUpBeforeClass()
    {
        require_once __DIR__ . '/../vendor/autoload.php';
        require_once __DIR__ . '/../Auth.php';
        require_once __DIR__ . '/../Config.php';

        self::$dbh = new PDO("mysql:host=127.0.0.1;dbname=phpauth_test_table", "phpauth_test_user", "");
        self::$config = new PHPAuth\Config(self::$dbh);
        self::$auth   = new PHPAuth\Auth(self::$dbh, self::$config);

        // Clean up the database
        self::$dbh->exec("DELETE FROM phpauth_attempts;");
        self::$dbh->exec("DELETE FROM phpauth_users;");
        self::$dbh->exec("DELETE FROM phpauth_sessions;");
        self::$dbh->exec("DELETE FROM phpauth_requests;");
    }

    public function testRegister()
    {
        // Successful registration
        $this->assertFalse(self::$auth->register('test@email.com', 'T3H-1337-P@$$', 'T3H-1337-P@$$')['error']);

        // Failed registration: same email
        $this->assertTrue(self::$auth->register('test@email.com', 'T3H-1337-P@$$', 'T3H-1337-P@$$')['error']);

        // Failed registration: invalid email address
        $this->assertTrue(self::$auth->register('InvalidEmail', 'T3H-1337-P@$$', 'T3H-1337-P@$$')['error']);

        // Failed registration: invalid password
        $this->assertTrue(self::$auth->register('test2@email.com', 'lamepass', 'lamepass')['error']);
    }

    /**
     * @depends testRegister
     */
    public function testLogin()
    {
        // Empty attempts table
        self::$dbh->exec("DELETE FROM phpauth_attempts;");

        // Successful login
        $this->assertFalse(self::$auth->login("test@email.com", 'T3H-1337-P@$$')['error']);

        // Failed login: incorrect email
        $this->assertTrue(self::$auth->login("incorrect@email.com", "IncorrectPassword1")['error']);

        // Failed login: incorrect password
        $this->assertTrue(self::$auth->login("test@email.com", "IncorrectPassword1")['error']);
    }

    /**
     * @depends testLogin
     */
    public function testCheckSession()
    {
        // Get the user's (created and logged in as earlier) session hash
        $hash = self::$dbh->query("SELECT hash FROM phpauth_sessions WHERE uid = (SELECT id FROM phpauth_users WHERE email = 'test@email.com');", PDO::FETCH_ASSOC)->fetch()['hash'];

        // Successful checkSession, outside of renew time
        self::$auth->config->cookie_renew = "+0 minutes";
        $this->assertTrue(self::$auth->checkSession($hash));
        $this->assertTrue(self::$auth->checkSession($hash)); // still valid

        // Successful checkSession, inside renew time
        self::$auth->config->cookie_renew = "+60 minutes";
        $this->assertTrue(self::$auth->checkSession($hash)); // will rotate hash
        $this->assertFalse(self::$auth->checkSession($hash));
        // A new valid hash
        $sessions = self::$dbh->query("SELECT * FROM phpauth_sessions WHERE uid = (SELECT id FROM phpauth_users WHERE email = 'test@email.com');", PDO::FETCH_ASSOC)->fetchAll();
        $this->assertEquals(count($sessions), 1);

        // Failed checkSession: invalid session hash
        $this->assertFalse(self::$auth->checkSession("invalidhash"));

        // Failed checkSession: inexistant session hash
        $this->assertFalse(self::$auth->checkSession("aaafda8ea2c65a596c7e089f256b1534f2298000"));

        // Failed checkSession: IP mismatch
        $hash = $sessions[0]['hash'];
        $ip = $sessions[0]['ip'];
        self::$dbh->exec("UPDATE phpauth_sessions SET ip = '1.2.3.4' WHERE hash='".$hash."';");
        $this->assertFalse(self::$auth->checkSession($hash));
        self::$dbh->exec("UPDATE phpauth_sessions SET ip = '".$ip."' WHERE hash='".$hash."';");  // reset

        // Failed checkSession: expired session
        self::$dbh->exec("UPDATE phpauth_sessions SET expiredate = '2000-01-01 00:00:00' WHERE hash='".$hash."';");
        $this->assertFalse(self::$auth->checkSession($hash));
        // Check that session was removed
        $sessions = self::$dbh->query("SELECT * FROM phpauth_sessions WHERE uid = (SELECT id FROM phpauth_users WHERE email = 'test@email.com');", PDO::FETCH_ASSOC)->fetchAll();
        $this->assertEquals(count($sessions), 0);
    }

    /**
     * @depends testLogin
     */
    public function testConcurrentSessions()
    {
        // Get the current session
        $uid = self::$dbh->query("SELECT id FROM phpauth_users WHERE email = 'test@email.com';", PDO::FETCH_ASSOC)->fetch()['id'];
        $hash = self::$dbh->query("SELECT hash FROM phpauth_sessions WHERE uid = ".$uid.";", PDO::FETCH_ASSOC)->fetch()['hash'];
        // Add a new session
        self::$auth->config->allow_concurrent_sessions = false;
        $hash2 = self::$auth->login("test@email.com", 'T3H-1337-P@$$')['hash'];
        // Verify that the existing session was replaced
        self::$auth->config->cookie_renew = "+0 minutes";  // Don't rotate hashes during test
        $this->assertFalse(self::$auth->checkSession($hash));
        $this->assertTrue(self::$auth->checkSession($hash2));
        // Try again with allow_concurrent_sessions
        self::$auth->config->allow_concurrent_sessions = true;
        $hash3 = self::$auth->login("test@email.com", 'T3H-1337-P@$$')['hash'];
        // Verify both sessions active
        $this->assertTrue(self::$auth->checkSession($hash2));
        $this->assertTrue(self::$auth->checkSession($hash3));

        // Test one session expiring while other still active
        self::$dbh->exec("UPDATE phpauth_sessions SET expiredate = '2000-01-01 00:00:00' WHERE hash='".$hash2."';");
        $this->assertFalse(self::$auth->checkSession($hash2));
        $this->assertTrue(self::$auth->checkSession($hash3));

        $sessions = self::$dbh->query("SELECT * FROM phpauth_sessions WHERE uid = ".$uid.";", PDO::FETCH_ASSOC)->fetchAll();
        $this->assertTrue(count($sessions) == 1);

        // Reset to starting state
        self::$auth->config->allow_concurrent_sessions = false;
        self::$auth->config->cookie_renew = "+60 minutes";
    }

    /**
     * @depends testLogin
     */
    public function testGetSessionUID()
    {
        $uid = self::$dbh->query("SELECT id FROM phpauth_users WHERE email = 'test@email.com';", PDO::FETCH_ASSOC)->fetch()['id'];
        $hash = self::$dbh->query("SELECT hash FROM phpauth_sessions WHERE uid = {$uid};", PDO::FETCH_ASSOC)->fetch()['hash'];

        // Successful getSessionUID
        $this->assertEquals($uid, self::$auth->getSessionUID($hash));

        // Failed getSessionUID: invalid session hash
        $this->assertFalse(self::$auth->getSessionUID("invalidhash"));

        // Failed getSessionUID: inexistant session hash
        $this->assertFalse(self::$auth->getSessionUID("aaafda8ea2c65a596c7e089f256b1534f2298000"));
    }

    /**
     * @depends testRegister
     */
    public function testIsEmailTaken()
    {
        // Successful isEmailTaken
        $this->assertTrue(self::$auth->isEmailTaken("test@email.com"));

        // Failed isEmailTaken: unused email
        $this->assertFalse(self::$auth->isEmailTaken("unused@email.com"));
    }

    /**
     * @depends testRegister
     */
    public function testGetUser()
    {
        $uid = self::$dbh->query("SELECT id FROM phpauth_users WHERE email = 'test@email.com';", PDO::FETCH_ASSOC)->fetch()['id'];

        // Successful getUser
        $this->assertEquals("test@email.com", self::$auth->getUser($uid)['email']);

        // Failed getUser: inexistant UID
        $this->assertFalse(self::$auth->getUser(9999999));
    }

    /**
     * @depends testRegister
     */
    public function testChangePassword()
    {
        $uid = self::$dbh->query("SELECT id FROM phpauth_users WHERE email = 'test@email.com';", PDO::FETCH_ASSOC)->fetch()['id'];

        // Successful changePassword
        $this->assertFalse(self::$auth->changePassword($uid, 'T3H-1337-P@$$', 'T3H-1337-P@$$2', 'T3H-1337-P@$$2')['error']);

        // Failed changePassword: invalid current password
        $this->assertTrue(self::$auth->changePassword($uid, "invalid", 'T3H-1337-P@$$2', 'T3H-1337-P@$$2')['error']);

        // Failed changePassword: incorrect current password
        $this->assertTrue(self::$auth->changePassword($uid, "IncorrectPassword1", 'T3H-1337-P@$$2', 'T3H-1337-P@$$2')['error']);

        // Failed changePassword: invalid new password
        $this->assertTrue(self::$auth->changePassword($uid, 'T3H-1337-P@$$2', "lamepass", "lamepass")['error']);

        // Failed changePassword: new password and confirmation do not match
        $this->assertTrue(self::$auth->changePassword($uid, 'T3H-1337-P@$$2', 'T3H-1337-P@$$3', 'T3H-1337-P@$$4')['error']);

        // Failed changePassword: incorrect UID
        $this->assertTrue(self::$auth->changePassword(9999999, 'T3H-1337-P@$$2', 'T3H-1337-P@$$3', 'T3H-1337-P@$$3')['error']);
    }

    /**
     * @depends testChangePassword
     */
    public function testChangeEmail()
    {
        $uid = self::$dbh->query("SELECT id FROM phpauth_users WHERE email = 'test@email.com';", PDO::FETCH_ASSOC)->fetch()['id'];

        // Successful changeEmail
        $this->assertFalse(self::$auth->changeEmail($uid, "test2@email.com", 'T3H-1337-P@$$2')['error']);

        // Failed changeEmail: invalid email
        $this->assertTrue(self::$auth->changeEmail($uid, "invalid.email", 'T3H-1337-P@$$2')['error']);

        // Failed changeEmail: new email is the same as current email
        $this->assertTrue(self::$auth->changeEmail($uid, "test2@email.com", 'T3H-1337-P@$$2')['error']);

        // Failed changeEmail: password is invalid
        $this->assertTrue(self::$auth->changeEmail($uid, "test3@email.com", "invalid")['error']);

        // Failed changeEmail: password is incorrect
        $this->assertTrue(self::$auth->changeEmail($uid, "test3@email.com", "IncorrectPassword1")['error']);

        // Failed changeEmail: UID is incorrect
        $this->assertTrue(self::$auth->changeEmail(9999999, "test2@email.com", "IncorrectPassword1")['error']);
    }

    /**
     * @depends testChangeEmail
     */
    public function testLogout()
    {
        // Get the user's (created and logged in as earlier) session hash
        $hash = self::$dbh->query("SELECT hash FROM phpauth_sessions WHERE uid = (SELECT id FROM phpauth_users WHERE email = 'test2@email.com');", PDO::FETCH_ASSOC)->fetch()['hash'];

        // Successful logout
        $this->assertTrue(self::$auth->logout($hash));

        // Failed logout: invalid session hash
        $this->assertFalse(self::$auth->logout("invalidhash"));

        // Failed logout: inexistant session hash
        $this->assertFalse(self::$auth->logout("aaafda8ea2c65a596c7e089f256b1534f2298000"));
    }

    /**
     * @depends testLogout
     */
    public function testLogoutAll()
    {
        $uid = self::$dbh->query("SELECT id FROM phpauth_users WHERE email = 'test2@email.com';", PDO::FETCH_ASSOC)->fetch()['id'];
        // Clear sessions
        self::$auth->logoutAll($uid);
        // Check no sessions
        $sessions = self::$dbh->query("SELECT hash FROM phpauth_sessions WHERE uid = (SELECT id FROM phpauth_users WHERE email = 'test2@email.com');", PDO::FETCH_ASSOC)->fetchAll();
        $this->assertSame(count($sessions), 0);
        // Allow concurrent sessions
        self::$auth->config->allow_concurrent_sessions = true;
        self::$auth->config->cookie_renew = "+0 minutes";  // Don't rotate hashes during test
        // Add a trio of sessions
        $hash1 = self::$auth->login("test2@email.com", 'T3H-1337-P@$$2')['hash'];
        $hash2 = self::$auth->login("test2@email.com", 'T3H-1337-P@$$2')['hash'];
        $hash3 = self::$auth->login("test2@email.com", 'T3H-1337-P@$$2')['hash'];
        $sessions = self::$dbh->query("SELECT hash FROM phpauth_sessions WHERE uid = (SELECT id FROM phpauth_users WHERE email = 'test2@email.com');", PDO::FETCH_ASSOC)->fetchAll();
        $this->assertSame(count($sessions), 3);
        // Logout one
        $this->assertTrue(self::$auth->logout($hash1));
        $this->assertFalse(self::$auth->checkSession($hash1));
        $this->assertTrue(self::$auth->checkSession($hash2));
        $this->assertTrue(self::$auth->checkSession($hash3));
        // Logout all
        $this->assertTrue(self::$auth->logoutAll($uid));
        $this->assertFalse(self::$auth->checkSession($hash2));
        $this->assertFalse(self::$auth->checkSession($hash3));
        $this->assertFalse(self::$auth->isLogged());
        $this->assertSame(self::$auth->getCurrentUser(), false);
        // Check no sessions
        $sessions = self::$dbh->query("SELECT hash FROM phpauth_sessions WHERE uid = (SELECT id FROM phpauth_users WHERE email = 'test2@email.com');", PDO::FETCH_ASSOC)->fetchAll();
        $this->assertSame(count($sessions), 0);
        // logoutAll will now return False since we have no active sessions
        $this->assertFalse(self::$auth->logoutAll($uid));
        // Fails with non-existent uid
        $this->assertFalse(self::$auth->logoutAll(111));
        // reset
        self::$auth->config->allow_concurrent_sessions = false;
        self::$auth->config->cookie_renew = "+60 minutes";
    }

    /**
     * @depends testLogoutAll
     * @depends testChangePassword
     * @depends testChangeEmail
     */
    public function testDeleteUser()
    {
        // Empty attempts table
        self::$dbh->exec("DELETE FROM phpauth_attempts;");

        $uid = self::$dbh->query("SELECT id FROM phpauth_users WHERE email = 'test2@email.com';", PDO::FETCH_ASSOC)->fetch()['id'];

        // Failed deleteUser: invalid password
        $this->assertTrue(self::$auth->deleteUser($uid, "lamepass")['error']);

        // Failed deleteUser: incorrect password
        $this->assertTrue(self::$auth->deleteUser($uid, "IncorrectPassword1")['error']);

        // Successful deleteUser
        $this->assertFalse(self::$auth->deleteUser($uid, 'T3H-1337-P@$$2')['error']);

        // Failed deleteUser: incorrect UID
        $this->assertTrue(self::$auth->deleteUser(9999999, "IncorrectPassword1")['error']);
    }

    public function testLanguageFiles()
    {
        // Use the english language file as main reference
        $baseLang = $lang = (include __DIR__ . '/../languages/en_GB.php');

        $languageFiles = glob(__DIR__ . '/../languages/*.php');

        foreach($languageFiles as $languageFile) {
            $languageFile = basename($languageFile);

            include __DIR__ . "/../languages/{$languageFile}";
            $this->assertEquals(0, count(array_diff_key($baseLang, $lang)));
        }
    }

}
