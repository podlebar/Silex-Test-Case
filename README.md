#Silex Example
Started as a hackday.. ended as something like a Cookbook for Silex.

## Setup

### Clone 
    git clone git://github.com/podlebar/Silex-Test-Case.git

and

    cd Silex-Test-Case
    
then

    sh vendors.sh
    
### Init the Submodules 

    cd vendor/silex
    git submodule update --init --recursive

### Create VHost
    <VirtualHost *:80>
            ServerName www.silex-test-case.local
            DocumentRoot "/Library/WebServer/Documents/Silex-Test-Case/web"
            <Directory "/Library/WebServer/Documents/Silex-Test-Case/web">
                    Options -Indexes FollowSymLinks Includes
                    AllowOverride All
                    Order allow,deny
                    Allow from All
            </Directory>
    </VirtualHost>
    
Don't forget to add the url (silex-test-case.local) to your hosts file 

## Config
Edit YAML Config-Files in /config and copy and rename ist to .yml
        
### Misc
    mkdir log
    
..and make sure it's writable

    touch log/development.log
    
Create the folder for uploads

    mkdir web/uploads
    
..and make sure it's writable

### Create MySQL DB
name it "silex_test_case" or what you defined it in your app.yml

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

