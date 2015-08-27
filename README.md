# synx
debian package management

This is an app developed by a sysadmin, Im not a php developer at all and has been put together from various sources and help
from various people who probably have no idea what they where looking at.

Goal is
I wanted to create something to ease the task of having to manually go through mutiple servers and verify patches or security updates without a huge cost involved or trying to use something that was designed for RedHat/Centos etc eg spacewalk.

This is still very much in early stages and doesnt work properly as I envisage it as of this point

Install:
Requirements:
webserver (nginx/apache)
php5 (php5-fpm or mod-php5 for apache)
php5-mysqlnd
php5-ssh2

mysqladmin create synx
echo "GRANT ALL PRIVILEGES ON synx.* to 'youruser'@'localhost' IDENTIFIED BY 'YourPassword';" |mysql
echo "FLUSH PRIVILEGES;" |mysql

cd /your/webroot/
git clone https://github.com/shadowhome/synx.git
cd synx/inc
edit upconfig.php to suit you db needs using your dbname, user and password from above

then go to http://yourwebserver/synx


