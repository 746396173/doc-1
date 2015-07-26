filename=`date +%Y%m%d`
/usr/local/mysql/bin/mysqldump --opt canetalaser -u root -pp@ssword0571 | gzip > /home/www/default/DBbackup/canetalaser$filename.gz
