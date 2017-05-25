<? error_reporting(E_ALL);
require_once("Database.php");
require_once ('jpgraph-3.5.0b1/src/jpgraph.php');
require_once ('jpgraph-3.5.0b1/src/jpgraph_line.php');
$name = "mmayer";
if(isset($_REQUEST["svp"])) {
  $name = $_REQUEST["svp"];
}
$forced = "";
if(isset($_REQUEST["force"])) {
   $forced = $_REQUEST["force"];
}
$daysBack = 500;
if(isset($_REQUEST["daysBack"])) {
  $daysBack = $_REQUEST["daysBack"];
}
$width = 500;
if(isset($_REQUEST["w"])) {
  $width = $_REQUEST["w"];
}
$height = 250;
if(isset($_REQUEST["h"])) {
  $height = $_REQUEST["h"];
}

$timeArray = Array();
$valArray = Array();
$db = Database::getDatabase();
$sql = "select * from EMPLOYEE_COUNT where SVP like '$name' and THE_DATE > DATE_SUB(now(), interval $daysBack day) order by THE_DATE";

$result = $db->Query($sql);
$numRows = mysqli_num_rows($result);
$numTicks = 5;
$tickMod = floor($numRows / $numTicks);
if($tickMod == 0) { $tickMod = 1; }
$counter = 0;
$currentVal = 0;
while($row = mysqli_fetch_assoc($result)) {
  $val = $row["FULL_TIME"];
  $time = $row["THE_DATE"];
  $time = preg_replace('/2015-/', '', $time);
  $time = preg_replace('/2016-/', '', $time);
  $time = preg_replace('/2017-/', '', $time);
  if($counter % $tickMod != 0) {
    $time = '';
  }
  array_push($timeArray, $time);
  array_push($valArray, $val);
  $currentVal = $val;
  $counter++;
}
$graphDater = Array('dates' => $timeArray, 'values' => $valArray);
createGraphImage("$name Total Employee Count ($currentVal)", $graphDater, $forced, $daysBack, $height, $width);

function createGraphImage($graphName, $graphData, $forced, $daysBack, $height, $width) {
    $graphFile = $graphName;
    $graphFileNoSpaces = preg_replace('/ /', '_', $graphFile);
    $graphFileName = $graphFileNoSpaces . "-" . Date('Y-m-d') . "-$daysBack-$height-$width.png";
    $graphFileLocalPath = "$graphFileName";
    $graphFileFullPath = "/www/ystats/img" . $graphFileLocalPath;
    // Setup the graph
    $graph = new Graph($width, $height);
    $graph->SetScale("textlin");

    $theme_class=new UniversalTheme;

    $graph->SetTheme($theme_class);
    $graph->img->SetAntiAliasing(false);
    $graph->title->Set($graphName);
    $graph->SetBox(false);

    $graph->img->SetAntiAliasing();

    $graph->yaxis->HideZeroLabel();
    $graph->yaxis->HideLine(false);
    $graph->yaxis->HideTicks(false,false);

    $graph->xgrid->Show();
    $graph->xgrid->SetLineStyle("solid");
    $graph->xaxis->SetTickLabels($graphData['dates']);
#    $graph->xaxis->SetTickLabels(Array('a','b','c','d'));
    $graph->xgrid->SetColor('#E3E3E3');

    // Create the first line
    $p1 = new LinePlot($graphData['values']);
#    $p1 = new LinePlot(Array(1,2,3,4));
    $graph->Add($p1);
    $p1->SetColor("#6495ED");
    $p1->SetLegend($graphName);

    $graph->legend->SetFrameWeight(1);
#    $graph->img->Stream($graphFileFullPath);

    // Output line
#    $graph->Stroke($graphFileFullPath);
    $graph->Stroke();
}
if(0) {
  echo "<pre>";
  print_r($graphDater);
  echo "</pre>";
}
?>