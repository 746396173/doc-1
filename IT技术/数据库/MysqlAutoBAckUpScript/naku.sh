filename=`date +%Y%m%d`
/usr/local/mysql/bin/mysqldump --opt naku -u root -pp@ssword0571 | gzip > /home/www/default/DBbackup/naku$filename.gz
