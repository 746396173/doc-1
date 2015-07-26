#! /bin/bash
find /usr/local/apache2/logs/access* -mtime +2 -exec rm -rf '{}' \;
find /usr/local/apache2/logs/error*  -mtime +2 -exec rm -rf '{}' \;
/usr/local/apache2/bin/apachectl graceful