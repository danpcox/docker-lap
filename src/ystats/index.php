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
echo getenv("MYSQL_PASS");
include_once("Database.php");

$db = Database::getDatabase();
$todayName = date("l");
$sql = "select distinct SVP from EMPLOYEE_COUNT order by SVP";
$result = $db->Query($sql);
while($row = mysqli_fetch_assoc($result)) {
  $svp = $row["SVP"];
  echo "<img src=\"/genStatImage.php?svp=$svp&w=800&h=400\"><br>\n";
}
?>
</body>
</html>

