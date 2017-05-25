<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1" /> 
<style>
.alert {background-color:teal;color:white;width:100%;margin-bottom:10px}
body {padding:10px; font-size:1.2em; margin: auto;}
@media screen and (min-width: 600px) {
.container {
  margin: auto;
}
}
</style>
</head>
<body>
<?
error_reporting(E_ALL);
ini_set("display_errors", 1);
include_once("Database.php");
$db = Database::getDatabase();
$todayName = date("l");
$dateSQL = "select max(THE_DATE) as the_max from EMPLOYEE_COUNT";
$result = $db->Query($dateSQL);
$row = mysqli_fetch_assoc($result);
$theDate = $row["the_max"];
$sql = "select distinct(SVP) from EMPLOYEE_COUNT where THE_DATE = '$theDate'";
//DATE_FORMAT(now(), '%Y-%m-%d');";
echo $sql;
$result = $db->Query($sql);
while($row = mysqli_fetch_assoc($result)) {
  $svp = $row["SVP"];
  echo "<img src=\"/genStatImage.php?svp=$svp&w=800&h=400&daysBack=180\"><br>\n";
}
?>
</body>
</html>

