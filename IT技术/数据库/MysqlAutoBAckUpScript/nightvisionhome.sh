filename=`date +%Y%m%d`
/usr/local/mysql/bin/mysqldump --opt nightvisionhome -u root -pp@ssword0571 | gzip > /home/www/default/DBbackup/nightvisionhome$filename.gz
