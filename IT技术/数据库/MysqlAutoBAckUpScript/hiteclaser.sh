filename=`date +%Y%m%d`
/usr/local/mysql/bin/mysqldump --opt hiteclaser -u root -pp@ssword0571 | gzip > /home/www/default/DBbackup/hiteclaser$filename.gz
