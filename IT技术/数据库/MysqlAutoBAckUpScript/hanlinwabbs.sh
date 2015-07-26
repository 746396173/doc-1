filename=`date +%Y%m%d`
/usr/local/mysql/bin/mysqldump --opt hanlinwabbs -u root -pp@ssword0571 | gzip > /home/www/default/DBbackup/hanlinwabbs$filename.gz
