filename=`date +%Y%m%d`
/usr/local/mysql/bin/mysqldump --opt aliarm -u root -pp@ssword0571 | gzip > /home/www/default/DBbackup/aliarm$filename.gz
