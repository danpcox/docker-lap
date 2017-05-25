<?
$_myConfig = null;
class Config
{

  var $port_ = "80";
  var $securePort_ = "443";
  var $mainURL = "192.168.99.100";
  var $webRoot_ = "/www/ystats";
  var $accessed_ = 0;
  var $logger = null;
// Database info
  var $dbUser_ = "root";
  var $dbPass_ = "";
  var $dbHost_ = "192.168.99.100";
//  var $dbHost_ = "localhost";
  var $dbDB_ = "employees";
  function Config()
  {
    $this->logger = Logger::getLogger();
    $this->logger->info("Config Instance Created");
    $this->accessed_ = 1;
    $this->dbPass_ =  getenv("MYSQL_PASS");
    if(!$this->dbPass_) {
      echo "MYSQL_PASS environment variable is not set for this instance!  DB won't work.<br>";
    }
  }

  function getWebRoot()
  {
    return $this->webRoot_;
  }

  static function getInstance()
  {
    global $_myConfig;
    if( $_myConfig != null)
    {
      $_myConfig->accessed_++;
      return $_myConfig;
    }
    $_myConfig = new Config();
    return $_myConfig;

  }
  function getSecureURL($uri)
  {
    $sPort = $this->securePort_;
    if($sPort != "443")
    {
      return "https://" . $this->mainURL . ":$sPort" . $uri;
    }
    return "https://" . $this->mainURL . $uri;
  }


  function getURL($uri)
  {
    $rPort = $this->port_;
    if($rPort != "80")
    {
      return "http://" . $this->mainURL . ":$rPort" . $uri;
    }
    return "http://" . $this->mainURL . $uri;
  }

  function goUnsecure()
  {
    if($_SERVER["SERVER_PORT"] == $this->sPort)
    {
      $string = $this->getURL($_SERVER["REQUEST_URI"]);
      header($string);
      exit;
    }
  }
  static function goSecure()
  {
    if($_SERVER["SERVER_PORT"] == $this->rPort)
    {
      $string = $this->getSecureURL($_SERVER["REQUEST_URI"]);
      header($string);
      exit;
    }
  }
}
?>
