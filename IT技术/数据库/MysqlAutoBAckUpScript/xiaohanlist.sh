filename=`date +%Y%m%d`
/usr/local/mysql/bin/mysqldump --opt xiaohanlist -u root -pp@ssword0571 | gzip > /home/www/default/DBbackup/xiaohanlist$filename.gz
