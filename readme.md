PHPAuth
====================

PHPAuth is a secure authentication class for PHP. 
It features improved passwordhashing using the hash_pbkdf2 function and 3 different salts, as well as improved coding layout, the use of private and public functions, seperating the most decisive functions from the frontend. 
There is also enhanced logging functionality, logging all attempts at logging in, creating accounts etc, including an IP and a timestamp.
With PHPAuth, you'll also get a tracking system with it, so you'll be able to track your visitors and do site analytics

Release Information
---------------
This repo contains in-development code for future releases. To download the
latest stable release please visit the [release](https://github.com/PHPAuth/PHPAuth/releases) page.

Features
---------------

* Uses prepared statements everywhere to mitigate SQL injection
* User possibilites : Login, register, activate account, resend activation email, reset password, change password, change email address, logout
* Thorough activity logging meaning all attempts at using the functions above are logged (including IP and timestamp) to check for hacking attempts, bugs in the system etc...
* Uses the hash_pbkdf2 function, with 3 different salts, resulting in extremely secure password storage
* Never sends a plaintext password, anywhere (Which is very bad practice BTW)
* Page protection (from non logged-in users) requires 5 lines of code, and that's with generous spacing
* Locks an IP out of system for 30 minutes after 5 failed attempts at any of the functions of the PHPAuth class
* No way for an attacker to "block" accounts by forcing them to be locked out
* All main functions (login, register, activate...) send back a return code ($return['code']), each one has a specific meaning so you can set custom error / success messages.
* Tracking system
* OTP (One-Time Auth)


A full feature list is available here at the wiki: https://github.com/PHPAuth/PHPAuth/wiki/Feature-list

Documentation
---------------
There is also full usage documentation, function descriptions and an FAQ available at the Wiki: https://github.com/PHPAuth/PHPAuth/wiki

Todo
---------------
add TOS -cookie use, include tos.txt- - Optimal option
add documentation and function descriptions for OTP
add OTP cookie

Contributing
---------------

You can contribute to this project in different ways:

* Report outstanding issues and bugs by creating an [Issue](https://github.com/PHPAuth/PHPAuth/issues/new)
* Fork the project, create a branch and file a pull request to improve the code itself


LICENSE
---------------
Copyright (C) 2014 - 2014  PHPAuth

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>

