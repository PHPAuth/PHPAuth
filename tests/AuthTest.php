<?php

// phpunit backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase') && class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}

class AuthTest extends \PHPUnit\Framework\TestCase
{
    public static $auth;
    public static $config;
    public static $dbh;

    public static function setUpBeforeClass()
    {
        require_once __DIR__ . '/../vendor/autoload.php';
        require_once __DIR__ . '/../Auth.php';
        require_once __DIR__ . '/../Config.php';

        self::$dbh = new PDO("mysql:host=127.0.0.1;dbname=phpauthtest", "root", "");
        self::$config = new PHPAuth\Config(self::$dbh);
        self::$auth   = new PHPAuth\Auth(self::$dbh, self::$config);

        // Clean up the database
        self::$dbh->exec("DELETE FROM attempts;");
        self::$dbh->exec("DELETE FROM users;");
        self::$dbh->exec("DELETE FROM sessions;");
        self::$dbh->exec("DELETE FROM requests;");
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
        self::$dbh->exec("DELETE FROM attempts;");

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
        $hash = self::$dbh->query("SELECT hash FROM sessions WHERE uid = (SELECT id FROM users WHERE email = 'test@email.com');", PDO::FETCH_ASSOC)->fetch()['hash'];

        // Successful checkSession
        $this->assertTrue(self::$auth->checkSession($hash));

        // Failed checkSession: invalid session hash
        $this->assertFalse(self::$auth->checkSession("invalidhash"));

        // Failed checkSession: inexistant session hash
        $this->assertFalse(self::$auth->checkSession("aaafda8ea2c65a596c7e089f256b1534f2298000"));
    }

    /**
     * @depends testLogin
     */
    public function testGetSessionUID()
    {
        $uid = self::$dbh->query("SELECT id FROM users WHERE email = 'test@email.com';", PDO::FETCH_ASSOC)->fetch()['id'];
        $hash = self::$dbh->query("SELECT hash FROM sessions WHERE uid = {$uid};", PDO::FETCH_ASSOC)->fetch()['hash'];

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
        $uid = self::$dbh->query("SELECT id FROM users WHERE email = 'test@email.com';", PDO::FETCH_ASSOC)->fetch()['id'];

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
        $uid = self::$dbh->query("SELECT id FROM users WHERE email = 'test@email.com';", PDO::FETCH_ASSOC)->fetch()['id'];

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
        $uid = self::$dbh->query("SELECT id FROM users WHERE email = 'test@email.com';", PDO::FETCH_ASSOC)->fetch()['id'];

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
     * @depends testCheckSession
     */
    public function testLogout()
    {
        // Get the user's (created and logged in as earlier) session hash
        $hash = self::$dbh->query("SELECT hash FROM sessions WHERE uid = (SELECT id FROM users WHERE email = 'test2@email.com');", PDO::FETCH_ASSOC)->fetch()['hash'];

        // Successful logout
        $this->assertTrue(self::$auth->logout($hash));

        // Failed logout: invalid session hash
        $this->assertFalse(self::$auth->logout("invalidhash"));

        // Failed logout: inexistant session hash
        $this->assertFalse(self::$auth->logout("aaafda8ea2c65a596c7e089f256b1534f2298000"));
    }

    /**
     * @depends testLogout
     * @depends testChangePassword
     * @depends testChangeEmail
     */
    public function testDeleteUser()
    {
        // Empty attempts table
        self::$dbh->exec("DELETE FROM attempts;");

        $uid = self::$dbh->query("SELECT id FROM users WHERE email = 'test2@email.com';", PDO::FETCH_ASSOC)->fetch()['id'];

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
        include __DIR__ . '/../languages/en_GB.php';

        $baseLang = $lang;

        $languageFiles = glob(__DIR__ . '/../languages/*.php');

        foreach($languageFiles as $languageFile) {
            $languageFile = basename($languageFile);

            include __DIR__ . "/../languages/{$languageFile}";
            $this->assertEquals(0, count(array_diff_key($baseLang, $lang)));
        }
    }
}
