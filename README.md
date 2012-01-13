#Silex Example
Started as a hackday.. ended as something like a Cookbook for Silex.

## Setup

### Clone 
    git clone git://github.com/podlebar/Silex-Test-Case.git

and
    cd Silex_Test_Case
    
### Init the Submodules 
    git submodule update --init --recursive

### Create VHost
    <VirtualHost *:80>
            ServerName www.hackday.dev
            DocumentRoot "/Library/WebServer/Documents/Silex-Test-Case/web"
            <Directory "/Library/WebServer/Documents/Silex-Test-Case/web">
                    Options -Indexes FollowSymLinks Includes
                    AllowOverride All
                    Order allow,deny
                    Allow from All
            </Directory>
    </VirtualHost>
        
### Misc
    mkdir log
    
..and make sure it's writable

    touch development.log
    
Create the folder for uploads

    mkdir web/uploads
    
..and make sure it's writable

### Create MySQL DB
name it "silex_test_case" for example

### Create the Table
    CREATE TABLE IF NOT EXISTS `user` (
      `user_id` int(11) NOT NULL AUTO_INCREMENT,
      `username` varchar(200) NOT NULL,
      `userhash` varchar(255) NOT NULL,
      `email` varchar(200) NOT NULL,
      `password` varchar(255) NOT NULL,
      `birthdate` date NOT NULL,
      `language` char(4) NOT NULL,
      `validated` tinyint(1) NOT NULL,
      PRIMARY KEY (`user_id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

## Config
Edit YAML Config-Files in /config and rename ist to .yml 
