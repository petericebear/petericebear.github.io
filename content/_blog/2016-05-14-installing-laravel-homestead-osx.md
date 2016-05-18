---
view::extends: _includes.blog_post_base
view::yields: post_body
post::title: Installing Laravel Homestead on OSX
post::brief: From time to time I need to reinstall my Mac, and this guide helps setting op Laravel Homestead for local PHP development. But how did I setup those PATH settings again? Lets begin..
pageTitle: Starting with Laravel Homestead on OSX -
---------------------------------------------------

From time to time I need to reinstall my Mac, and this guide helps setting op Laravel Homestead for local PHP development. But how did I setup those PATH settings again? 
I sometimes forget these simple steps. Thats why I setup this quick guide as my own memory but maybe it helps some other people aswell.

The following Software will be installed on your system. 

- Installing Composer
- Setting global PATH for composer
- Installing VirtualBox
- Installing Vagrant
- Installing Laravel Homestead
- Installing Laravel Installer

## Installing Composer

Run the following commands in your terminal.

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

You can now run composer globally in your terminal by running:
```bash
composer
```

## Setting global PATH for composer

If you want to use composer applications globally you need to setup the correct PATH variable to the global vendor bin directory.
From OSX Leopard and higher this is an easy process by running the following command in your terminal.

```bash
sudo -s 'echo "~/.composer/vendor/bin" > /etc/paths.d/40-composer'
```

After running this command, you need to reboot the system. Alternatively, you can close and reopen the Terminal app to see new $PATH changes.

## Installing VirtualBox on OSX

To Install Virtualbox on your OSX machine simply go to the website of https://www.virtualbox.org and press the big button in the middle off the screen.

## Installing Vagrant

To install Vagrant on your OSX machine simply go to the website of https://www.vagrantup.com and press the big blue button 'Download'.

## Installing Laravel Homestead

```bash
composer global require "laravel/homestead=~2.0"
```

If you have setup the PATH correctly you now have access to the command 'homestead' within your terminal.
Now its time to initialize homestead by running init in your terminal.

```bash
homestead init
```

This will create a homestead.yaml file for configuring homestead. To view and edit this configuration you can run.

```bash
homestead edit
```

## Installing Laravel Installer

For quick creation of new Laravel projects using the Laravel Installer is a real time saver.
You can install it with this command.

```bash
composer global require "laravel/installer=~1.1"
```

## Usage of Laravel Homestead with the Laravel Installer

Laravel homestead defaults maps to a Code folder of the logged in user. So make sure that folder exists.
You can run this command in your terminal to create it.

```bash
mkdir ~/Code
```

You go to the folder ~/Code

```bash
cd ~/Code
```

Lets create a new Laravel project by running the command

```bash
laravel new homestead
```

This will download and install a fresh laravel installation in the folder called homestead.
The default configuration of Laravel homestead includes a site and path as homestead.app.

Lets add that site in our local hosts file to access it.

```bash
sudo nano /etc/hosts
```

Add this line to it:

```
192.168.10.10  homestead.app
```

Now its time to check the working of the stack. By starting up the homestead machine.

```bash
homestead up
```

Now its time to open up your favourite web browser and go to the page:

```
http://homestead.app
```

Et Voilà, a Running Laravel running website on your started Virtual Machine.
Check the following links for their original and more complete guides.

- [Laravel Homestead](https://laravel.com/docs/5.2/homestead)
- [Laravel Installation docs](https://laravel.com/docs/5.2/installation)
- [Vagrant](https://www.vagrantup.com)
- [VirtualBox](https://www.virtualbox.org)