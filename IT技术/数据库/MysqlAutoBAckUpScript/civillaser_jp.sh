filename=`date +%Y%m%d`
/usr/local/mysql/bin/mysqldump --opt civillaser_jp -u root -pp@ssword0571 | gzip > /home/www/default/DBbackup/civillaser_jp$filename.gz
