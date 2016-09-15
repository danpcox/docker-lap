<?
$_myLogger = null;
class Logger
{
  var $accessed_ = 0;
  function Logger()
  {
    $this->accessed_ = 1;
  }
  static function getLogger()
  {
    global $_myLogger;
    if( $_myLogger != null)
    {
      $_myLogger->accessed_++;
      return $_myLogger;
    }
    $_myLogger = new Logger();
    return $_myLogger;
    
  }
  function query($str)
  {
    $this->logit("QUERY", $str, 2);
  }
  function info($str)
  {
    $this->logit("INFO", $str, 1);
  }
  function warn($str)
  {
    $this->logit("WARN", $str, 1);
  }
  function error($str)
  {
    $this->logit("ERROR", $str, 1);
  }
  function logit($level, $message, $depth)
  {
    if(!$depth) { $depth = 1; }
    $bTrace = debug_backtrace();
    $fileCalling = $bTrace[$depth]["file"];
    $fileCalling = preg_replace("/\/www/", "", $fileCalling);
    $line = $bTrace[$depth]["line"];
    $message = "[$level] - $fileCalling:$line - $message";
    if($_GET && isset($_GET["debug"]))
    {
      echo "<b>$message</b><br />";
      echo "<pre>";
      debug_print_backtrace();
      echo "</pre>";
    }
    error_log($message, 0);	
  }
  function backtrace()
  {
    echo "<pre>";
    debug_print_backtrace();
    echo "</pre><hr /><br />";
  }

  function dump($obj)
  {
    echo "<pre>";
    if($obj) {print_r($obj);}
    debug_print_backtrace();
    echo "</pre><hr /><br />";
  }
}
?>
