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

require_once 'en.php';
require_once 'es.php';
require_once 'fr.php';

class Handler
{
    private $locale;

    public function __construct($data, $locale = 'en')
    {
        $locale = '\cuonic\PHPAuth2\Localization\\' . $locale;
        if (class_exists($locale)) {
            $this->locale = new $locale($data);
        } else {
            throw new \Exception('Language template does not exist.');
        }
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function getActivationEmail()
    {
        throw new \Exception('This function is not implemented.');
    }

    public function getResetEmail()
    {
        throw new \Exception('This function is not implemented.');
    }
}
