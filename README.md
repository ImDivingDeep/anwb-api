# anwb-api

Tested with:
- Windows 10
- Apache httpd version 2.4.51
- Php 8.1.0
- MySQL 8.0.27
- PhpMyAdmin 5.1.1

I used WAMPServer for local development, this installed all the dependencies I needed.
When installing WAMPServer, do note the version numbers mentioned earlier.
For PHP, make sure to enable the pdo_mysql extension.

If you do not want to use WAMPServer, follow the following steps.

# Installing Apache
Apache can also be installed from here:
https://www.apachelounge.com/download/

Install it to C:\apache64

Apache httpd-vhost.conf:
```
<VirtualHost *:8001>
	ServerName localhost
	DocumentRoot "C:/wamp64/www/testproject"
	<Directory  "C:/wamp64/www/testproject/">
		Options +Indexes +Includes +FollowSymLinks +MultiViews
		AllowOverride All
	</Directory>
</VirtualHost>
```

Modify the DocumentRoot and Directory to where this directory is located

Modify httpd.conf
- Uncomment `Include conf/extra/httpd-vhosts.conf`
- Add `Listen 8001`
- Uncomment `LoadModule rewrite_module modules/mod_rewrite.so`

# Installing PHP
Download the latest PHP version here:
https://windows.php.net/download

Install to C:\php

Add the following lines to Apaches httpd.conf
```
LoadModule php_module "C:\php\php8apache2_4.dll"
AddHandler application/x-httpd-php  .php
PHPIniDir "C:\php"
```

In C:\php copy the `php.ini-development` file and rename it to php.ini.
Uncomment the `extension=curl` and `extension=pdo_mysql` lines

# Installing MySQL
Download MySQL from here:
https://dev.mysql.com/downloads/installer/

During the installation choose either Developer Default or Server only

When setting a root password, make sure to change line 17 in `database.php`
`$this->pdo = new PDO($dsn, "root", "password", $options);`

Change line 18 of TrafficModel.php to point to the correct directory

# Installing PhpMyAdmin (optional)
If you want to interact with the MySQL database, install PhpMyAdmin from:
https://www.phpmyadmin.net/

# Load data every 5 minutes

Add a scheduled task for LoadTrafficData.php for every 5 minutes.

Using Windows Task Scheduler:
- Create a new task
	- Select `Run with highest privileges`
- Add a new trigger
    - Select Daily with the Start Time being 00:00:00
    - Select Advanced Settings, Repeat task every 5 minutes
- Add a new Action
    - Select Start a program
    - Select Browse, find the PHP installation folder and select php.exe
    - In Add arguments, add the full file path to LoadTrafficData.php (for example C:\anwb-api\LoadTrafficData.php)



