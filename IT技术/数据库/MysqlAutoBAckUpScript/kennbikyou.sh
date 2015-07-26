filename=`date +%Y%m%d`
/usr/local/mysql/bin/mysqldump --opt kennbikyou -u root -pp@ssword0571 | gzip > /home/www/default/DBbackup/kennbikyou$filename.gz
