<?
$MYSQL_PASS = getenv("MYSQL_PASS");
ini_set('display_errors', 1);
require_once("Database.php");
$backupFileName = "backup-" . date('m-d-Y') . ".sql";

exec("mysqldump -uroot -p$MYSQL_PASS -h 192.168.99.100 employees --add-drop-table > /www/ystats/data/$backupFileName")

?>