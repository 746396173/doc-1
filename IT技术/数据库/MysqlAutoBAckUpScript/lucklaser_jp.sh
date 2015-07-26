filename=`date +%Y%m%d`
/usr/local/mysql/bin/mysqldump --opt lucklaser_jp -u root -pp@ssword0571 | gzip > /home/www/default/DBbackup/lucklaser_jp$filename.gz
