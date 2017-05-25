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
<form method=get action="index.php">
Days Back: <select name=daysBack>
<option>7</option>
<option>14</option>
<option>30</option>
<option>90</option>
<option>180</option>
<option>365</option>
<option>730</option>
</select>
<input type=submit>
</form>
<?
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
$daysBack = 180;
if(isset($_REQUEST["daysBack"])) {
	$daysBack = $_REQUEST["daysBack"];
}
while($row = mysqli_fetch_assoc($result)) {
  $svp = $row["SVP"];
  echo "<img src=\"/genStatImage.php?svp=$svp&w=800&h=400&daysBack=$daysBack\"><br>\n";
}
?>
</body>
</html>

