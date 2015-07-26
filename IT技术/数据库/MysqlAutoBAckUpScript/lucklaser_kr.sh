filename=`date +%Y%m%d`
/usr/local/mysql/bin/mysqldump --opt lucklaser_kr -u root -pp@ssword0571 | gzip > /home/www/default/DBbackup/lucklaser_kr$filename.gz
