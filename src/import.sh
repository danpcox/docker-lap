#/bin/sh

echo $MYSQL_PASS

echo mysql -uroot -p$MYSQL_PASS -h 192.168.99.100 dan \< import.sql
