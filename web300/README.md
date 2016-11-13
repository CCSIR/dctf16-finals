# WEB 300: Stubborn memory - PHP APC challenge

## Requirements:
- PHP 5.3 (default for Ubuntu 12.04)
- PHP APC

## Setup (as root):

```bash
apt-get update
apt-get install -y lamp-server^ php-apc
a2dismod status
sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/sites-available/default

git clone https://github.com/shark0der/dctf16-finals.git
cd dctf16-finals/web300

rm /var/www/index.html
cp -R www/{*,.htaccess} /var/www/

sed -i '/^disable_functions/d' /etc/php5/apache2/php.ini
sed -i '/^disable_classes/d' /etc/php5/apache2/php.ini
cat php.ini >> /etc/php5/apache2/php.ini
cp apc.ini /etc/php5/conf.d/apc.ini

cp setup.sh /root/setup.sh
cp cron.d_setup /etc/cron.d/setup
chmod +x /etc/cron.d/setup

poweroff
```

After powering off the machine, take a snapshot and then start it.

## Startup script explained

In my setup I have used lxc containers and created a snapshot. When I needed to restore the challenge to the initial state I was stopping the container, restoring it from the snapshot, starting it again. After starting it, the startup script would set up the cache and would then remove the .htaccess file. You'll have to modify the startup script to place the original `flag.php` file and `.htaccess` back to the www directory if you are not using snapshots.


