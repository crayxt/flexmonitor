This is version 2.0 of Flexmonitor

To install, simply copy content of 'web' folder in your web server directory.

Install the database structure by running licenses.sql script

You can configure your installation by editing file config.php
- Edit the path to your database
- Edit the path to lmutil.exe and lmxendutil.exe files (these will be used to query your license server)

You will have to connect to the admin.php page to start create sites for monitoring.


You will need to create scheduled tasks or cron on your server to run periodical queries on your licenses servers.
- create a task that will run license_cache.php once a day (around midnight). It will store licenses available for each feature.
- create a task that will run license_util.php every 15 minutes. It will store licenses used for each feature.take care, if you are monitoring lots of feature the table where usage is store will very soon contain lots of records.