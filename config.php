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
namespace cuonic\PHPAuth2;

class Config
{
    private $lang = 'en';
    private $lang_list = array(
        'en',
        'fr',
        'es',
        'nl');
    private $base_url = 'http://example.com/phpauth2.0/';
    private $salt_1 = 'us_1dUDN4N-53/dkf7Sd?vbc_due1d?df!feg';
    private $salt_2 = 'Yu23ds09*d?u8SDv6sd?usi$_YSdsa24fd+83';
    private $salt_3 = '63fds.dfhsAdyISs_?&jdUsydbv92bf54ggvc';
    private $cookie_domain;
    private $cookie_path = '/';
    private $cookie_auth = 'auth_session';
    private $sitekey = 'dk;l189654è(tyhj§!dfgdfàzgq_f4fá.';
    private $admin_level = 99;
    private $table_activations = 'activations';
    private $table_attempts = 'attempts';
    private $table_log = 'log';
    private $table_resets = 'resets';
    private $table_sessions = 'sessions';
    private $table_users = 'users';
    private $table_tracking = 'tracking';
    private $session_duration = "+1 month";

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }
}
