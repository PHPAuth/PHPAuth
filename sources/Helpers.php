<?php

namespace PHPAuth;

use RuntimeException;

trait Helpers
{
    /**
     * Returns default (EN) dictionary
     *
     * This method must return default messages for all cases.
     *
     * @return array
     */
    public static function getForgottenDictionary(): array
    {
        $lang = [];

        $lang['account_activated'] = 'Account activated.';
        $lang['account_deleted'] = 'Account deleted successfully.';
        $lang['account_inactive'] = 'Account has not yet been activated.';
        $lang['email_taken'] = 'The email address is already in use.';
        $lang['activation_exists'] = 'An activation email has already been sent.';
        $lang['activation_sent'] = 'Activation email has been sent.';
        $lang['already_activated'] = 'Account is already activated.';
        $lang['activationkey_expired'] = 'Activation key has expired.';
        $lang['activationkey_incorrect'] = 'Activation key is incorrect.';
        $lang['activationkey_invalid'] = 'Activation key is invalid.';
        $lang['captcha_verify_failed'] = 'Captcha Code was invalid.';
        $lang['user_verify_failed'] = 'Captcha Code was invalid.';
        $lang['email_banned'] = 'This email address is not allowed.';
        $lang['email_changed'] = 'Email address changed successfully.';
        $lang['email_incorrect'] = 'Email address is incorrect.';
        $lang['email_invalid'] = 'Email address is invalid.';
        $lang['email_long'] = 'Email address is too long.';
        $lang['email_short'] = 'Email address is too short.';
        $lang['newemail_match'] = 'New email matches previous email.';
        $lang['email_activation_altbody'] = 'Hello,

To be able to log in to your account you first need to activate your account by visiting the following link :
%1$s/%2$s

You then need to use the following activation key: %3$s

If you did not sign up on %1$s recently then this message was sent in error, please ignore it.';
        $lang['email_activation_body'] = 'Hello,&lt;br/&gt;&lt;br/&gt; To be able to log in to your account you first need to activate your account by clicking on the following link : &lt;strong&gt;&lt;a href="%1$s/%2$s"&gt;%1$s/%2$s&lt;/a&gt;&lt;/strong&gt;&lt;br/&gt;&lt;br/&gt; You then need to use the following activation key: &lt;strong&gt;%3$s&lt;/strong&gt;&lt;br/&gt;&lt;br/&gt; If you did not sign up on %1$s recently then this message was sent in error, please ignore it.';
        $lang['email_activation_subject'] = '%s - Activate account';
        $lang['email_reset_altbody'] = 'Hello,

To reset your password please visiting the following link :
%1$s/%2$s

You then need to use the following password reset key: %3$s

If you did not request a password reset key on %1$s recently then this message was sent in error, please ignore it.';
        $lang['email_reset_body'] = 'Hello,&lt;br/&gt;&lt;br/&gt;To reset your password click the following link :&lt;br/&gt;&lt;br/&gt;&lt;strong&gt;&lt;a href="%1$s/%2$s"&gt;%1$s/%2$s&lt;/a&gt;&lt;/strong&gt;&lt;br/&gt;&lt;br/&gt;You then need to use the following password reset key: &lt;strong&gt;%3$s&lt;/strong&gt;&lt;br/&gt;&lt;br/&gt;If you did not request a password reset key on %1$s recently then this message was sent in error, please ignore it.';
        $lang['email_reset_subject'] = '%s - Password reset request';
        $lang['logged_in'] = 'You are now logged in.';
        $lang['logged_out'] = 'You are now logged out.';
        $lang['newpassword_invalid'] = 'New password must contain at least one uppercase and lowercase character, and at least one digit.';
        $lang['newpassword_long'] = 'New password is too long.';
        $lang['newpassword_match'] = 'New password is the same as the old password.';
        $lang['newpassword_nomatch'] = 'New passwords do not match.';
        $lang['newpassword_short'] = 'New password is too short.';
        $lang['password_changed'] = 'Password changed successfully.';
        $lang['password_incorrect'] = 'Current password is incorrect.';
        $lang['password_nomatch'] = 'Passwords do not match.';
        $lang['password_notvalid'] = 'Password is invalid.';
        $lang['password_short'] = 'Password is too short.';
        $lang['password_weak'] = 'Password is too weak.';
        $lang['register_success'] = 'Account created. Activation email sent to email.';
        $lang['register_success_emailmessage_suppressed'] = 'Account created.';
        $lang['password_reset'] = 'Password reset successfully.';
        $lang['resetkey_expired'] = 'Reset key has expired.';
        $lang['resetkey_incorrect'] = 'Reset key is incorrect.';
        $lang['resetkey_invalid'] = 'Reset key is invalid.';
        $lang['reset_exists'] = 'A reset request already exists.';
        $lang['reset_requested'] = 'Password reset request sent to email address.';
        $lang['reset_requested_emailmessage_suppressed'] = 'Password reset request is created.';
        $lang['function_disabled'] = 'This function has been disabled.';
        $lang['system_error'] = 'A system error has been encountered. Please try again.';
        $lang['user_blocked'] = 'You are currently locked out of the system.';
        $lang['user_login_account_inactive'] = 'The account isn\'t activated yet. ';
        $lang['user_login_incorrect_password'] = 'Incorrect Password.';
        $lang['user_register_email_taken'] = 'E-mail already in use.';
        $lang['user_register_success'] = 'The account has been created. Activation instructions sent to the provided e-mail.';
        $lang['user_validate_email_incorrect'] = 'Incorrect email format.';
        $lang['user_validate_password_incorrect'] = 'Password too short, too long or otherwise doesn\'t match the requirements.';
        $lang['user_validate_remember_me_invalid'] = 'Unacceptable &amp;ldquo;remember user&amp;rdquo; field value.';
        $lang['user_validate_user_not_found'] = 'This e-mail is not registered.';
        $lang['account_not_found'] = 'Email address / password is incorrect.';
        $lang['email_password_incorrect'] = 'Email address / password is incorrect.';
        $lang['email_password_invalid'] = 'Email address / password is invalid.';
        $lang['remember_me_invalid'] = 'The remember me field is invalid.';

        $lang['php_version_required'] = 'PHPAuth engine requires PHP version %s+!';

        return $lang;
    }

    /**
     * Returns a random string of a specified length
     *
     * @param int $length
     * @return string $key
     */
    public static function getRandomKey(int $length = AuthInterface::TOKEN_LENGTH):string
    {
        $dictionary = 'A1B2C3D4E5F6G7H8I9J0K1L2M3N4O5P6Q7R8S9T0U1V2W3X4Y5Z6a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0u1v2w3x4y5z6';
        $dictionary_length = strlen($dictionary);
        $key = '';

        for ($i = 0; $i < $length; $i++) {
            $key .= $dictionary[mt_rand(0, $dictionary_length - 1)];
        }

        return $key;
    }

    /**
     * Returns IP address of client
     *
     * @return string $ip
     */
    public static function getIp():string
    {
        if (getenv('REMOTE_ADDR')) {
            $ipAddress = getenv('REMOTE_ADDR');
        } elseif (getenv('HTTP_CLIENT_IP')) {
            $ipAddress = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ipAddress = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $ipAddress = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $ipAddress = getenv('HTTP_FORWARDED_FOR');
        } elseif (getenv('HTTP_FORWARDED')) {
            $ipAddress = getenv('HTTP_FORWARDED');
        } else {
            $ipAddress = '127.0.0.1';
        }

        $ipAddress = explode(',', $ipAddress)[0];

        return $ipAddress;
    }

    /**
     * Hashes provided password with BCrypt
     *
     * With zero cost will be used default cost value (10)
     *
     * @param string $string
     * @param int $cost
     * @throws RuntimeException
     *
     * @return string
     */
    public static function getHash(string $string, int $cost = 0):string
    {
        $hash_options = ($cost > 0) ? [ 'cost' => $cost ] : [];

        $hash = password_hash($string, PASSWORD_BCRYPT, $hash_options);

        if ($hash === null) {
            throw new RuntimeException("[PHPAuth] Hashing algorithm is invalid. Blowfish not supported? ");
        }

        if ($hash === false) {
            throw new RuntimeException("[PHPAuth] Generate blowfish hash failed");
        }

        return $hash;
    }

    public function __lang(string $key, ...$args): string
    {
        /**
         * NEW lang => LEGACY lang by key
         */
        $lang_new_to_legacy = [
            'system.error'                      =>  'system_error',

            'captcha.verify_code_invalid'       =>  'user_verify_failed',

            'user.temporary_banned'             =>  'user_blocked',

            'login.remember_me_invalid_value'   =>  'remember_me_invalid',

            'account.no_pair_user_and_password' =>  'email_password_incorrect',
            'account.not_activated'             =>  'account_inactive',
            'account.not_found'                 =>  'account_not_found',

            'email.address_too_short'           =>  'email_short',
            'email.address_too_long'            =>  'email_long',
            'email.address_incorrect'           =>  'email_invalid',
            'email.address_in_banlist'          =>  'email_banned',

            'password.too_short'                =>  'password_short',
            'password.not_equal'                =>  'password_nomatch',
            'password.too_weak'                 =>  'password_weak',
            'password.incorrect'                =>  'password_incorrect'

        ];

        if (array_key_exists($key, $lang_new_to_legacy)) {
            $key = $lang_new_to_legacy[ $key ];
        }

        $string = array_key_exists($key, $this->messages_dictionary) ? $this->messages_dictionary[$key] : $key;
        return (func_num_args() > 1) ? vsprintf($string, $args) : $string;
    }

}
