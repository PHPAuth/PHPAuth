<?php

class AuthTest extends PHPUnit_Framework_TestCase
{
    public $auth;
    public $config;
    public $dbh;

    private $hash;

    public function __construct()
    {
        require_once __DIR__ . '/../vendor/autoload.php';
        require_once __DIR__ . '/../Auth.php';
        require_once __DIR__ . '/../Config.php';

        $this->dbh = new PDO("mysql:host=127.0.0.1;dbname=phpauthtest", "root", "");
        $this->config = new PHPAuth\Config($this->dbh);
        $this->auth   = new PHPAuth\Auth($this->dbh, $this->config);

        // Clean up the database
        $this->dbh->exec("DELETE FROM attempts;");
        $this->dbh->exec("DELETE FROM users;");
        $this->dbh->exec("DELETE FROM sessions;");
        $this->dbh->exec("DELETE FROM requests;");
    }

    public function testRegister()
    {
        // Successful registration
        $this->assertFalse($this->auth->register('test@email.com', 'T3H-1337-P@$$', 'T3H-1337-P@$$')['error']);

        // Failed registration: same email
        $this->assertTrue($this->auth->register('test@email.com', 'T3H-1337-P@$$', 'T3H-1337-P@$$')['error']);

        // Failed registration: invalid email address
        $this->assertTrue($this->auth->register('InvalidEmail', 'T3H-1337-P@$$', 'T3H-1337-P@$$')['error']);

        // Failed registration: invalid password
        $this->assertTrue($this->auth->register('test2@email.com', 'lamepass', 'lamepass')['error']);
    }

    /**
     * @depends testRegister
     */
    public function testLogin()
    {
        // Empty attempts table
        $this->dbh->exec("DELETE FROM attempts;");

        // Successful login
        $this->assertFalse($this->auth->login("test@email.com", 'T3H-1337-P@$$')['error']);

        // Failed login: incorrect email
        $this->assertTrue($this->auth->login("incorrect@email.com", "IncorrectPassword1")['error']);

        // Failed login: incorrect password
        $this->assertTrue($this->auth->login("test@email.com", "IncorrectPassword1")['error']);
    }

    /**
     * @depends testLogin
     */
    public function testCheckSession()
    {
        // Get the user's (created and logged in as earlier) session hash
        $hash = $this->dbh->query("SELECT hash FROM sessions WHERE uid = (SELECT id FROM users WHERE email = 'test@email.com');", PDO::FETCH_ASSOC)->fetch()['hash'];

        // Successful checkSession
        $this->assertTrue($this->auth->checkSession($hash));

        // Failed checkSession: invalid session hash
        $this->assertFalse($this->auth->checkSession("invalidhash"));

        // Failed checkSession: inexistant session hash
        $this->assertFalse($this->auth->checkSession("aaafda8ea2c65a596c7e089f256b1534f2298000"));
    }

    /**
     * @depends testLogin
     */
    public function testGetSessionUID()
    {
        $uid = $this->dbh->query("SELECT id FROM users WHERE email = 'test@email.com';", PDO::FETCH_ASSOC)->fetch()['id'];
        $hash = $this->dbh->query("SELECT hash FROM sessions WHERE uid = {$uid};", PDO::FETCH_ASSOC)->fetch()['hash'];

        // Successful getSessionUID
        $this->assertEquals($uid, $this->auth->getSessionUID($hash));

        // Failed getSessionUID: invalid session hash
        $this->assertFalse($this->auth->getSessionUID("invalidhash"));

        // Failed getSessionUID: inexistant session hash
        $this->assertFalse($this->auth->getSessionUID("aaafda8ea2c65a596c7e089f256b1534f2298000"));
    }

    /**
     * @depends testRegister
     */
    public function testIsEmailTaken()
    {
        // Successful isEmailTaken
        $this->assertTrue($this->auth->isEmailTaken("test@email.com"));

        // Failed isEmailTaken: unused email
        $this->assertFalse($this->auth->isEmailTaken("unused@email.com"));
    }

    /**
     * @depends testRegister
     */
    public function testGetUser()
    {
        $uid = $this->dbh->query("SELECT id FROM users WHERE email = 'test@email.com';", PDO::FETCH_ASSOC)->fetch()['id'];

        // Successful getUser
        $this->assertEquals("test@email.com", $this->auth->getUser($uid)['email']);

        // Failed getUser: inexistant UID
        $this->assertFalse($this->auth->getUser(9999999));
    }

    /**
     * @depends testRegister
     */
    public function testChangePassword()
    {
        $uid = $this->dbh->query("SELECT id FROM users WHERE email = 'test@email.com';", PDO::FETCH_ASSOC)->fetch()['id'];

        // Successful changePassword
        $this->assertFalse($this->auth->changePassword($uid, 'T3H-1337-P@$$', 'T3H-1337-P@$$2', 'T3H-1337-P@$$2')['error']);

        // Failed changePassword: invalid current password
        $this->assertTrue($this->auth->changePassword($uid, "invalid", 'T3H-1337-P@$$2', 'T3H-1337-P@$$2')['error']);

        // Failed changePassword: incorrect current password
        $this->assertTrue($this->auth->changePassword($uid, "IncorrectPassword1", 'T3H-1337-P@$$2', 'T3H-1337-P@$$2')['error']);

        // Failed changePassword: invalid new password
        $this->assertTrue($this->auth->changePassword($uid, 'T3H-1337-P@$$2', "lamepass", "lamepass")['error']);

        // Failed changePassword: new password and confirmation do not match
        $this->assertTrue($this->auth->changePassword($uid, 'T3H-1337-P@$$2', 'T3H-1337-P@$$3', 'T3H-1337-P@$$4')['error']);

        // Failed changePassword: incorrect UID
        $this->assertTrue($this->auth->changePassword(9999999, 'T3H-1337-P@$$2', 'T3H-1337-P@$$3', 'T3H-1337-P@$$3')['error']);
    }

    /**
     * @depends testChangePassword
     */
    public function testChangeEmail()
    {
        $uid = $this->dbh->query("SELECT id FROM users WHERE email = 'test@email.com';", PDO::FETCH_ASSOC)->fetch()['id'];

        // Successful changeEmail
        $this->assertFalse($this->auth->changeEmail($uid, "test2@email.com", 'T3H-1337-P@$$2')['error']);

        // Failed changeEmail: invalid email
        $this->assertTrue($this->auth->changeEmail($uid, "invalid.email", 'T3H-1337-P@$$2')['error']);

        // Failed changeEmail: new email is the same as current email
        $this->assertTrue($this->auth->changeEmail($uid, "test2@email.com", 'T3H-1337-P@$$2')['error']);

        // Failed changeEmail: password is invalid
        $this->assertTrue($this->auth->changeEmail($uid, "test3@email.com", "invalid")['error']);

        // Failed changeEmail: password is incorrect
        $this->assertTrue($this->auth->changeEmail($uid, "test3@email.com", "IncorrectPassword1")['error']);

        // Failed changeEmail: UID is incorrect
        $this->assertTrue($this->auth->changeEmail(9999999, "test2@email.com", "IncorrectPassword1")['error']);
    }

    /**
     * @depends testCheckSession
     */
    public function testLogout()
    {
        // Get the user's (created and logged in as earlier) session hash
        $hash = $this->dbh->query("SELECT hash FROM sessions WHERE uid = (SELECT id FROM users WHERE email = 'test2@email.com');", PDO::FETCH_ASSOC)->fetch()['hash'];

        // Successful logout
        $this->assertTrue($this->auth->logout($hash));

        // Failed logout: invalid session hash
        $this->assertFalse($this->auth->logout("invalidhash"));

        // Failed logout: inexistant session hash
        $this->assertFalse($this->auth->logout("aaafda8ea2c65a596c7e089f256b1534f2298000"));
    }

    /**
     * @depends testLogout
     * @depends testChangePassword
     * @depends testChangeEmail
     */
    public function testDeleteUser()
    {
        // Empty attempts table
        $this->dbh->exec("DELETE FROM attempts;");

        $uid = $this->dbh->query("SELECT id FROM users WHERE email = 'test2@email.com';", PDO::FETCH_ASSOC)->fetch()['id'];

        // Failed deleteUser: invalid password
        $this->assertTrue($this->auth->deleteUser($uid, "lamepass")['error']);

        // Failed deleteUser: incorrect password
        $this->assertTrue($this->auth->deleteUser($uid, "IncorrectPassword1")['error']);

        // Successful deleteUser
        $this->assertFalse($this->auth->deleteUser($uid, 'T3H-1337-P@$$2')['error']);

        // Failed deleteUser: incorrect UID
        $this->assertTrue($this->auth->deleteUser(9999999, "IncorrectPassword1")['error']);
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
