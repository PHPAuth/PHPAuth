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

class fr extends Handler
{
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getActivationEmail()
    {
        $email = array();

        $email['subject'] = 'PHPAuth 2.0 : Activation du compte n&eacute;cessaire';

        $email['head'] = 'MIME-Version: 1.0' . "\r\n";
        $email['head'] .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $email['head'] .= 'From: PHPAuth 2.0 <no-reply@phpauth.cuonic.com>' . "\r\n";

        $email['body'] = 'Bonjour,' . "\r\n";
        $email['body'] .= '<br/><br/>' . "\r\n";
        $email['body'] .= 'Vous avez r&eacute;cemment cr&eacute;e un compte sur PHPAuth 2.0.' .
            "\r\n";
        $email['body'] .= '<br/>' . "\r\n";
        $email['body'] .= 'Pour pouvoir utiliser votre compte, vous devez d\'abord l\'activer avec le lien suivant : <strong><a href="' .
            $this->data['base_url'] . '?page=activate&key=' . $this->data['key'] .
            '" target="_blank">Activer mon compte</a></strong>' . "\r\n";
        $email['body'] .= '<br/><br/>' . "\r\n";
        $email['body'] .= 'Ou sinon, cliquez <a href="' . $this->data['base_url'] .
            '?page=activate" target="_blank">ici</a> et copiez / collez la cl&eacute; suivante : <strong>' .
            $this->data['key'] . '</strong>' . "\r\n";
        $email['body'] .= '<br/><br/>' . "\r\n";
        $email['body'] .= 'Rappel : Cette cl&eacute; unique d\'activation expirera dans les 24 heures suivant la cr&eacute;ation du compte.' .
            "\r\n";

        return $email;
    }

    public function getResetEmail()
    {
        $email = array();

        $email['subject'] = 'PHPAuth 2.0 : Demande de r&eacute;initialisation du mot de passe';

        $email['head'] = 'MIME-Version: 1.0' . "\r\n";
        $email['head'] .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $email['head'] .= 'From: PHPAuth 2.0 <no-reply@phpauth.cuonic.com>' . "\r\n";

        $email['body'] = 'Bonjour,' . "\r\n";
        $email['body'] .= '<br/><br/>' . "\r\n";
        $email['body'] .= 'Vous avez demand&eacute; la r&eacute;initialisation du mot de passe sur PHPAuth 2.0' .
            "\r\n";
        $email['body'] .= '<br/>' . "\r\n";
        $email['body'] .= 'Pour continuer avec la r&eacute;initialisation du mot de passe, utilisez le lien suivant : <strong><a href="' .
            $this->data['base_url'] . '?page=reset&step=2&key=' . $this->data['key'] .
            '" target="_blank">R&eacute;initialiser mon mot de passe</a></strong>' . "\r\n";
        $email['body'] .= '<br/><br/>' . "\r\n";
        $email['body'] .= 'Ou sinon, cliquez <a href="' . $this->data['base_url'] .
            '?page=reset&step=2" target="_blank">ici</a> et copiez / collez la cl&eacute; suivante : <strong>' .
            $this->data['key'] . '</strong>' . "\r\n";
        $email['body'] .= '<br/><br/>' . "\r\n";
        $email['body'] .= 'Rappel : Cette cl&eacute; unique de r&eacute;initialisation expirera dans les 24 heures suivant la demande de r&eacute;initialisation.' .
            "\r\n";

        return $email;
    }
}
