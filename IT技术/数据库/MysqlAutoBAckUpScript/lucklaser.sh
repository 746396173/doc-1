filename=`date +%Y%m%d`
/usr/local/mysql/bin/mysqldump --opt lucklaser -u root -pp@ssword0571 | gzip > /home/www/default/DBbackup/lucklaser$filename.gz
