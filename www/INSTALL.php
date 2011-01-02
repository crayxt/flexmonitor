REQUIREMENTS
------------

First you must have the base environment for Flexmonitor.
We have tested Flexmonitor! on: Linux and Windows 2003.
Linux or one of the BSD's are recommended, but anything else that can run the
3 pieces of software listed below should do it.

Apache	-> http://www.apache.org
MySQL	-> http://www.mysql.com
PHP	-> http://www.php.net


SERVER CONFIGURATION
--------------------

You MUST ensure that PHP has been compiled with support for MySQL and Zlib
in order to successfully run Flexmonitor.

While Flexmonitor works on IIS server we recommend Apache
for running Flexmonitor on Windows.


MANDATORY COMPONENTS
-------------------

Flexmonitor uses SEF (Search Engine Friendly) URLs, you'll need mod_rewrite and the ability to
use local .htaccess files to make Flexmonitor work. If you don't have this configuration you
will receive an error 500 when you try to access the application. If you use IIS make sure to setup
an url rewriter component also.


INSTALLATION
------------

1. DOWNLOAD Flexmonitor

	You can obtain the latest Joomla! release from:
		http://flexmonitor.sourceforge.net

	Copy the zip file into a working directory e.g.

	$ cp Flexmonitor_X.X.zip /tmp/Flexmonitor

	Change to the working directory e.g.

	$ cd /tmp/Joomla

	Extract the files e.g.

	$ tar -zxvf Flexmonitor_X.X.zip

	This will extract all Flexmonitor files and directories.  Move the contents
	of that directory into a directory within your web server's document
	root or your public HTML directory e.g.

	$ mv /tmp/Flexmonitor/* /var/www/html

	Alternatively if you downloaded the file to your computer and unpacked
	it locally use a FTP program to upload all files to your server.
	Make sure all PHP, HTML, CSS and JS files are sent in ASCII mode and
	image files (GIF, JPG, PNG) in BINARY mode.


2. CREATE THE Flexmonitor DATABASE

	Joomla! will currently only work with MySQL.  In the following examples,
	"db_user" is an example MySQL user which has the CREATE and GRANT
	privileges.  You will need to use the appropriate user name for your
	system.

	First, you must create a new database for your Flexmonitor site e.g.

	$ mysqladmin -u db_user -p create licenses

	MySQL will prompt for the 'db_user' database password and then create
	the initial database files.  Next you must login and set the access
	database rights e.g.

	$ mysql -u db_user -p

	Again, you will be asked for the 'db_user' database password.  At the
	MySQL prompt, enter following command:

	GRANT ALL PRIVILEGES ON licenses.*
		TO nobody@localhost IDENTIFIED BY 'password';

	where:

	'licenses' is the name of your database
	'nobody@localhost' is the userid of your webserver MySQL account
	'password' is the password required to log in as the MySQL user

	If successful, MySQL will reply with

	Query OK, 0 rows affected

	to activate the new permissions you must enter the command

	flush privileges;

	and then enter '\q' to exit MySQL.

	Alternatively you can use your web control panel or phpMyAdmin to
	create a database for Flexmonitor.


3. WEB INSTALLER

Finally point your web browser to http://www.mysite.com where the Flexmonitor web
based installer will guide you through the rest of the installation.


4. CONFIGURE Flexmonitor

You can now launch your browser and point it to your Flexmonitor site e.g.

	http://www.mysite.com -> Main Site
	http://www.mysite.com/admin -> Admin

Flexmonitor ADMINISTRATION
----------------------

Upon a new installation, your Flexmonitor website defaults to a very basic
configuration with no active license monitoring.

Use Admin to create new monitoring sites. You will then be able to configure the
licenses to monitor for each site.

Additionnaly you will need to create cron tab or schedule tasks to start license
monitoring.
In the cron or schedule task launch wget http://www.mysite.com/licenses.
Run the cron every 15 minutes to start monitoring.

