filename=`date +%Y%m%d`
/usr/local/mysql/bin/mysqldump --opt dmicroscope -u root -pp@ssword0571 | gzip > /home/www/default/DBbackup/dmicroscope$filename.gz
