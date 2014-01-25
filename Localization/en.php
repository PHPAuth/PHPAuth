<?php
/** PHPAuth-2.0
 * @author PHPAuth
 * @version 2.0
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
namespace cuonic\PHPAuth2\Localization;

class en extends Handler
{
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getActivationEmail()
    {
        $email = array();

        $email['subject'] = 'PHPAuth 2.0 : Account Activation required';

        $email['head'] = 'MIME-Version: 1.0' . "\r\n";
        $email['head'] .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $email['head'] .= 'From: PHPAuth 2.0 <no-reply@phpauth.cuonic.com>' . "\r\n";

        $email['body'] = 'Hello,' . "\r\n";
        $email['body'] .= '<br/><br/>' . "\r\n";
        $email['body'] .= 'You recently created an account on PHPAuth 2.0.' . "\r\n";
        $email['body'] .= '<br/>' . "\r\n";
        $email['body'] .= 'To be able to use your account, you need to activate it by clicking the following link : <strong><a href="' .
            $this->data['base_url'] . '?page=activate&key=' . $this->data['key'] .
            '" target="_blank">Activate my account</a></strong>' . "\r\n";
        $email['body'] .= '<br/><br/>' . "\r\n";
        $email['body'] .= 'Or alternatively, go to <a href="' . $this->data['base_url'] .
            '?page=activate" target="_blank">this page</a> and paste the following code : <strong>' .
            $this->data['key'] . '</strong>' . "\r\n";
        $email['body'] .= '<br/><br/>' . "\r\n";
        $email['body'] .= 'Reminder : This unique activation key will expire within 24 hours of account creation.' .
            "\r\n";

        return $email;
    }

    public function getResetEmail()
    {
        $email = array();

        $email['subject'] = 'PHPAuth 2.0 : Password reset request';

        $email['head'] = 'MIME-Version: 1.0' . "\r\n";
        $email['head'] .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $email['head'] .= 'From: PHPAuth 2.0 <no-reply@phpauth.cuonic.com>' . "\r\n";

        $email['body'] = 'Hello,' . "\r\n";
        $email['body'] .= '<br/><br/>' . "\r\n";
        $email['body'] .= 'You recently requested a password reset request at PHPAuth 2.0.' .
            "\r\n";
        $email['body'] .= '<br/>' . "\r\n";
        $email['body'] .= 'To proceed with resetting your password, click the following link : <strong><a href="' .
            $this->data['base_url'] . '?page=reset&step=2&key=' . $this->data['key'] .
            '" target="_blank">Reset my password</a></strong>' . "\r\n";
        $email['body'] .= '<br/><br/>' . "\r\n";
        $email['body'] .= 'Or alternatively, go to <a href="' . $this->data['base_url'] .
            '?page=reset&step=2" target="_blank">this page</a> and paste the following code : <strong>' .
            $this->data['key'] . '</strong>' . "\r\n";
        $email['body'] .= '<br/><br/>' . "\r\n";
        $email['body'] .= 'Reminder : This unique reset key will expire within 24 hours of the password reset request.' .
            "\r\n";

        return $email;
    }
}
