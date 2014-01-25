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
abstract class trackingDB
{
    private $geoXML;
    private $countryName;
    private $continentName;
    private $regionName;

    abstract public function storeInfo();

    abstract public function retrieveInfo();

    public function __construct()
    {
        $realIP = $this->getIP();

        $this->geoXML = simplexml_load_file("http://www.geoplugin.net/xml.gp?ip={$realIP}");
    }

    private function getIP()
    {
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $_realIP = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $_realIP = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $_realIP = $_SERVER['REMOTE_ADDR'];
            }
        } else {
            if (getenv($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $_realIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (getenv($_SERVER['HTTP_CLIENT_IP'])) {
                $_realIP = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $_realIP = $_SERVER['REMOTE_ADDR'];
            }
        }
        return $_realIP;
    }

    private function getOS()
    {
        $os_platform = "Unknown OS Platform";
        $os_array = array(
            '/windows nt 6.3/i' => 'Windows 8.1',
            '/windows nt 6.2/i' => 'Windows 8',
            '/windows nt 6.1/i' => 'Windows 7',
            '/windows nt 6.0/i' => 'Windows Vista',
            '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
            '/windows nt 5.1/i' => 'Windows XP',
            '/windows xp/i' => 'Windows XP',
            '/windows nt 5.0/i' => 'Windows 2000',
            '/windows me/i' => 'Windows ME',
            '/win98/i' => 'Windows 98',
            '/win95/i' => 'Windows 95',
            '/win16/i' => 'Windows 3.11',
            '/macintosh|mac os x/i' => 'Mac OS X',
            '/mac_powerpc/i' => 'Mac OS 9',
            '/linux/i' => 'Linux',
            '/ubuntu/i' => 'Ubuntu',
            '/iphone/i' => 'iPhone',
            '/ipod/i' => 'iPod',
            '/ipad/i' => 'iPad',
            '/android/i' => 'Android',
            '/blackberry/i' => 'BlackBerry',
            '/webos/i' => 'Mobile');

        foreach ($os_array as $regex => $value) {

            if (preg_match($regex, $_SERVER['HTTP_USER_AGENT'])) {
                $os_platform = $value;
            }
        }
        return $os_platform;
    }

    private function getBrowser()
    {
        $agent = $_SERVER['HTTP_USER_AGENT'];

        if (stripos($agent, 'Firefox') !== false) {
            $agent = 'Firefox';
        } elseif (stripos($agent, 'MSIE') !== false) {
            $agent = 'Internet Explorer';
        } elseif (stripos($agent, 'iPad') !== false) {
            $agent = 'IPAD';
        } elseif (stripos($agent, 'Android') !== false) {
            $agent = 'Android';
        } elseif (stripos($agent, 'Chrome') !== false) {
            $agent = 'Chrome';
        } elseif (stripos($agent, 'Safari') !== false) {
            $agent = 'Safari';
        } elseif (stripos($agent, 'AIR') !== false) {
            $agent = 'AIR';
        } elseif (stripos($agent, 'Fluid') !== false) {
            $agent = 'Fluid';
        }
        return $agent;
    }

    private function getDNT()
    {

        $phpHeader = "HTTP_" . strtoupper(str_replace("-", "_", "DNT"));

        if ((array_key_exists($phpHeader, $_SERVER)) and ($_SERVER[$phpHeader] == 1)) {
            return 1;
        } else {
            return 0;
        }
    }

    function trackDetails()
    {
        if ($this->getDNT()) {
            $dateTime = date('d-m-Y h:i:s A');


            $referrer = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] :
                'Direct Link';

            $ipAddress = $this->getIP();

            $this->countryName = ($this->geoXML->geoplugin_countryName == '') ?
                'Unknown Country' : $this->geoXML->geoplugin_countryName;

            if ($this->geoXML->geoplugin_continentCode == '') {
                $this->continentName = 'Unknown Continent';
            } else {
                $continent_array = array(
                    'AF' => 'Africa',
                    'AN' => 'Antarctica',
                    'AS' => 'Asia',
                    'EU' => 'Europe',
                    'NA' => 'North America',
                    'SA' => 'South America',
                    'OC' => 'Oceania');

                $continentCode = $this->geoXML->geoplugin_continentCode;

                foreach ($continent_array as $code => $continentName) {
                    if ($continentCode == $code) {
                        $continent = $continentName;
                    }
                }
                $this->continentName = $continent;
            }

            $this->regionName = ($this->geoXML->geoplugin_regionName == '') ?
                'Unknown Region' : $this->geoXML->geoplugin_regionName;

            $hostName = (gethostbyaddr($this->getIP()) == '') ? 'Unknown Host' :
                gethostbyaddr($this->getIP());

            $cookies = serialize($_SERVER['HTTP_COOKIE']);

            $landingPage = $_SERVER['REQUEST_URI'];

            if ($landingPage == "/") {
                $landingPage = "/home";
            }

            $landingHost = $_SERVER['HTTP_HOST'];

            $requestmethod = $_SERVER['REQUEST_METHOD'];

            $DNT = $this->getDNT();


            $uid = (isset($_COOKIE['uID'])) ? $_COOKIE['uID'] : 0;

            return array(
                'IP' => $this->getIP(),
                'uid' => $_COOKIE['uID'],
                'DNT' => $DNT,
                'Continent' => $this->continentName,
                'Region' => $this->regionName,
                'Country' => $this->countryName,
                'landingpage' => $landingPage,
                'landinghost' => $landingHost,
                'Referrer' => $referrer,
                'requestmethod' => $requestmethod,
                'OS' => $this->getOS(),
                'Host' => $hostName,
                'Browser' => $this->getBrowser(),
                'cookies' => $cookies,
                'DateTime' => $dateTime);
        } else {
            return false;
        }
    }
}

class tracking extends trackingDB
{
    function __construct($dbh, $dbTable)
    {

        parent::__construct();

        $this->dbh = $dbh;
        $this->dbTable = $dbTable;
    }

    public function storeInfo()
    {
        $trackDetails = $this->trackDetails();
        if (is_array($trackDetails)) {

            $data = array(
                $trackDetails['IP'],
                $trackDetails['uid'],
                $trackDetails['DNT'],
                $trackDetails['Continent'],
                $trackDetails['Region'],
                $trackDetails['Country'],
                $trackDetails['landingpage'],
                $trackDetails['landinghost'],
                $trackDetails['Referrer'],
                $trackDetails['requestmethod'],
                $trackDetails['OS'],
                $trackDetails['Host'],
                $trackDetails['Browser'],
                $trackDetails['cookies'],
                $trackDetails['DateTime']);

            $STH = $this->dbh->prepare("INSERT INTO {$this->dbTable} (ip,uid,DNT,continent,region,country,landingpage,landinghost,referrer,requestmethod,os,host,browser,cookies,datetime) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $STH->execute($data);

        }
        return true;
    }

    function retrieveInfo()
    {
        $STH = $DBH->query("SELECT * FROM {$this->tableName}");

        $STH->setFetchMode(PDO::FETCH_ASSOC);

        while ($row = $STH->fetch()) {
            $allDetails[] = $row;
        }

        if ($STH->rowCount() < 1) {
            return "No Records Found";
        } else {
            while ($row = $STH->fetch()) {
                $allDetails[] = $row;
            }
        }
        return $allDetails;
    }
}
?>