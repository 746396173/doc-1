filename=`date +%Y%m%d`
/usr/local/mysql/bin/mysqldump --opt vincentlist -u root -pp@ssword0571 | gzip > /home/www/default/DBbackup/vincentlist$filename.gz
