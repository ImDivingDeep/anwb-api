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
Apache can also be installed without WAMPServer from here:
https://www.apachelounge.com/download/

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

# Installing PHP
Download the latest PHP version here:
https://windows.php.net/download

# Installing MySQL
Download MySQL from here:
https://dev.mysql.com/downloads/installer/

# Installing PhpMyAdmin (optional)
If you want to interact with the MySQL database, install PhpMyAdmin from:
https://www.phpmyadmin.net/

# Load data every 5 minutes

Add a scheduled task for LoadTrafficData.php for every 5 minutes.

Using Windows Task Scheduler:
- Create a new task
- Add a new trigger
    - Select Daily with the Start Time being 00:00:00
    - Select Advanced Settings, Repeat task every 5 minutes
- Add a new Action
    - Select Start a program
    - Select Browse, find the PHP installation folder and select php.exe
    - In Add arguments, add the full file path to LoadTrafficData.php (for example C:\anwb-api\LoadTrafficData.php)



