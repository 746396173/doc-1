filename=`date +%Y%m%d`
/usr/local/mysql/bin/mysqldump --opt gome007 -u root -pp@ssword0571 | gzip > /home/www/default/DBbackup/gome007$filename.gz
