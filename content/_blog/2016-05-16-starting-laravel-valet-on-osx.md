---
view::extends: _includes.blog_post_base
view::yields: post_body
post::title: Starting with Laravel Valet on OSX
post::brief: Laravel Valet is for local development on you OSX machine. I came across some issues getting started and wanted to share that with you.  
pageTitle: Starting with Laravel Valet on OSX - 
-----------------------------------------------

Laravel Valet is a Laravel development environment for Mac minimalists. No Vagrant, No Apache, No Nginx, No /etc/hosts file. You can even share your sites publicly using local tunnels.
Valet runs best with PHP 7 for speedy performances. I came across some issues getting it running on my system and wanted to share them with you. You can find those issues and the solutions to them below the installation of PHP/Valet. 

## Installation of Laravel Valet on OSX

First update the Homebrew and get required formulas – formulas are just packages, like those in aptitude – (If you don’t have Homebrew yet, visit this page for installation and detailed usage: [http://brew.sh](http://brew.sh))

```bash
brew update && brew tap homebrew/php
```

With the new tap in place with the PHP formula recipes, we can now install PHP version 7. Currently I don't use PostgreSQL yet, but maybe someday I will so therefor I always include the extension.
 
```bash
brew install php70 --with-postgresql --with-homebrew-curl
```

After installing PHP v7 we can add some extra extensions to it.

```bash
brew install php70-opcache php70-mcrypt php70-xdebug --build-from-source
brew install php70-memcached --build-from-source --HEAD
```

You can always add more extensions if you want if you search within brew like so "brew search php70-".
Now its time to configure some settings of PHP to use locally.

First find the correct configuration file (default is: /private/etc/apache2/httpd.conf):
```bash
httpd -V | grep SERVER_CONFIG_FILE
```

Now edit the file with sudo rights with nano:
```bash
sudo nano YOUR_PATH_TO_CONFIG
```

Add the following rows to the end of the file.
```bash
LoadModule php7_module /usr/local/opt/php70/libexec/apache2/libphp7.so
 
<FilesMatch .php$>
    SetHandler application/x-httpd-php
</FilesMatch>
```

Now press CTRL + W and search for the line with "DirectoryIndex index.html" and change it to "DirectoryIndex index.php index.html".
After changing this line you can close and save the contents with CTRL + X.

Now we can change the default php settings in php.ini.
```bash
nano /usr/local/etc/php/7.0/php.ini
```
Find “;date.timezone =“ and set your timezone. Mine is date.timezone = “Europe/Amsterdam”. Find ;opcache.enable, and ;opcache.enable_cli and set them both to 1, also delete the ;(semicolon) prefixes.
After changing these settings you can close and save the contents with CTRL + X.

Now run these commands:
```bash
sudo httpd -k restart
php -v
```
If all is correctly setup you should see this output.
```bash
PHP 7.0.6 (cli) (built: Apr 28 2016 20:23:54) ( NTS )
Copyright (c) 1997-2016 The PHP Group
Zend Engine v3.0.0, Copyright (c) 1998-2016 Zend Technologies
    with Zend OPcache v7.0.6-dev, Copyright (c) 1999-2016, by Zend Technologies
```

Now that you have setup PHP, its time for the magic of Laravel Valet.
```bash
brew install mariadb # If you want to use databases
composer global require laravel/valet # This is the Magic
```

If you haven't have ~/.composer/vendor/bin in your systems PATH, you can do this by this command:
```bash
sudo -s 'echo "~/.composer/vendor/bin" > /etc/paths.d/40-composer'
```
Sometimes you may need to restart your system if you are using the paths.d kind of implementation.

Now install the Magic.
```bash
valet install
```
This will configure and install Valet and DnsMasq, and register Valet's daemon to launch when your system starts.
You can verify to ping to something.dev, it should be linked to 127.0.0.1 your local machine IP.

You can now CD into your Code folder and run the command:
```bash
valet park
```

Now every folder is accessible by http://FOLDER_NAME.dev.
For more information about Laravel Valet please check the official page: https://laravel.com/docs/5.2/valet. 

## Issues I came across

Not every OSX system is equal and everyone has customizations on his setup. The issues I came across are the following. 

### Connection Refused on port 80

For my OSX installation port 80 was not open by default. To make sure port 80 is open for TCP on all interfaces, I added

```bash
pass in proto tcp from any to any port 80
```
to /etc/pf.conf. Reloading pfctl didn't quite do the trick, but a reboot did. Now, the port shows up as open in port scan, and my virtual hosts are served as they should.

### Autostart the PHP and Database services after reboot

Brew has a good implementation to start services on your system after reboot. 

```bash
brew services # this will initialize the services deamon
brew services php70 start
brew services mariadb start
brew services list # this command list all (un)active services
```

### PHP Deprecation messages

After running the command 'valet park' the first time I got a PHP deprecation message for the memcached extension.

PHP Deprecated:  PHP Startup: memcached.sess_lock_wait and memcached.sess_lock_max_wait are deprecated. Please update your configuration to use memcached.sess_lock_wait_min, memcached.sess_lock_wait_max and memcached.sess_lock_retries in Unknown on line 0
Deprecated: PHP Startup: memcached.sess_lock_wait and memcached.sess_lock_max_wait are deprecated. Please update your configuration to use memcached.sess_lock_wait_min, memcached.sess_lock_wait_max and memcached.sess_lock_retries in Unknown on line 0

There is an easy fix for this.

```bash
nano /usr/local/etc/php/7.0/conf.d/ext-memcached.ini
```

And change the following words of the variables:
- change memcached.sess_lock_wait to memcached.sess_lock_wait_min
- change memcached.sess_lock_max_wait to memcached.sess_lock_wait_max

Save the file and run:

```bash
brew services php70 restart
```