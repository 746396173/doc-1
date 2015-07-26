filename=`date +%Y%m%d`
/usr/local/mysql/bin/mysqldump --opt tccymm_phpwind -u root -pp@ssword0571 | gzip > /home/www/default/DBbackup/tccymm$filename.gz
