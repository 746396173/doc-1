filename=`date +%Y%m%d`
/usr/local/mysql/bin/mysqldump --opt yijinhome -u root -pp@ssword0571 | gzip > /home/www/default/DBbackup/yijinhome$filename.gz
