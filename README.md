# Woody-CMS
Woody CMS is a cache reduction oriented CMS. 

REQUIREMENTS
============

You need a web Server with PHP5.4 installed on it
You need a MySQLv5.5 database reachable from web server


INSTALL
=======

Unzip all source Files on our server, where it can be acceded ("/www" for Apache)

Import "woodyCMS.sql" file into your database

Edit file : "configuration/configuration.ini"

    In the [database] part, fill the fields :
    - [DB_HOST] must be replace by the hostname of your database server;
    - [DB_NAME] must be replace by the name of your database; 
    - [DB_USER] must be replace by the username of your database user; 
    - [DB_PASSWORD] must be replace by the password associated with the username; 
    - Optional : You can set a specific port for your database access. 

    In the [admin] and [site-demo] parts, replace [HOST] by your hostname

    Optionnal : you can add your IP in the [global] part in "debug[]" var, 
    this will display debug data only on your station

Go on your browser and get to "your host/admin" : for administration you can log in with : admin/admin
You can access a little demo site on "your host/demo".
    