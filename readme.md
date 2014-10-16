PHPAuth Group
=======

What is it
---------------

PHPAuth is a secure user authentication class for PHP websites, using a powerful password hashing system and attack blocking to keep your website and users secure.
PHPAuth Group adds support of groups and administration rules in hierarchical logic levels on groups (currently in development)

Features
---------------
* Uses PHPAuth basics and add a checking group function
* add group support
* each user in group have an 'level' allows access/restrictions inside groups(in development)
* default groups (super admin, admins, moderators) have rules over others(not implemented yet)
* create delete modify groups, add and remove users from group(s)


Requirements
---------------
requires PHPAuth.

Configuration
---------------

The configuration file from PHPAuth (config.class.php) need to be modified to add tables name.
find 
```private $table_users = 'users';```
add after 
``` 
private $table_groups = 'groups';
private $table_usergroups = 'usergroups';
```


How to use
---------------

Restricting a page to a user from a group is really simple. 
You need to have properly set Auth identification and class.
$gid is the group ID. 
the page:

```php
if($auth->checkAuthGroup($gid)===true) { // logged in user is in group, he can access }
?>
```

License
---------------

Copyright (C) 2014 - 2014 PHPAuth

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program. If not, see http://www.gnu.org/licenses/

Contributing
---------------

Anyone can contribute to improve or fix PHPAuth, to do so you can either report an issue (a bug, an idea...) or fork the repository, perform modifications to your fork then request a merge.

