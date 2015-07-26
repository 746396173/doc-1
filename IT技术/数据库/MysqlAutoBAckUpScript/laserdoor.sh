filename=`date +%Y%m%d`
/usr/local/mysql/bin/mysqldump --opt laserdoor -u root -pp@ssword0571 | gzip > /home/www/default/DBbackup/laserdoor$filename.gz
