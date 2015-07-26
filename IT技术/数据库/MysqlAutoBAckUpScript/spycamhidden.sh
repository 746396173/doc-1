filename=`date +%Y%m%d`
/usr/local/mysql/bin/mysqldump --opt spycamhidden -u root -pp@ssword0571 | gzip > /home/www/default/DBbackup/spycamhidden$filename.gz
