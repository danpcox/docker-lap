<?
include_once("Logging.php");
include_once("Database.php");
$_session_instance = null;
class Session
{
  
  var $oldInformation_   = Array();
  var $information_      = Array();
  var $db_               = null;
  var $log_              = null;
  
  
  function Session($sessionID)
  {
    // They did not pass in an ID
    if(!$sessionID)
    {
      $this->information_ = Array();
    }
    else
    {
      $this->db_ = Database::getDatabase();
      $sql = "select USER_ID,END_TIME,SESSION_INFO from WEB_SESSION where SESSION_ID='$sessionID' "
           . "AND END_TIME > now()";
      $result = $this->db_->Query($sql);
      if(mysqli_num_rows($result))
      {
        $info = mysqli_fetch_assoc($result);
        // Bring in the saved data from the DB.
        $this->information_ = unserialize($info["SESSION_INFO"]);
#        Logger::getLogger()->info("Information from DB: " . $this->information_);
        $this->oldInformation_ = unserialize($info["SESSION_INFO"]);
        $this->information_["endTime"] = $info["END_TIME"]; 
        $this->information_["USER_ID"] = $info["USER_ID"];
        $this->information_["sessionid"] = $sessionID;
        $this->information_["NET_SESSION_VALID_ID"] = $sessionID;
      }
      else
      {
        Logger::getLogger()->warn("Session: $sessionID is not a valid session\nNullify information");
        $this->information_ = null;
      }
      $this->needsSave_ = false;
    }
  }
  
  public function isValid() {
    return ($this->information_ && $this->information_["USER_ID"]);
  }

  public static function saveSessionInfo($sessionInfo) {
    global $_session_instance;
    if( is_object($_session_instance))
    {
#      Logger::getLogger()->info("Setting new value for sessionInfo");
      $_session_instance->information_ = $sessionInfo;
    }
  }

  public static function & getSessionInfo($sessionID = "")
  {
#    Logger::getLogger()->info("getSessionInfo($sessionID)");
    global $_session_instance;
    if( is_object($_session_instance))
    {
      return $_session_instance->information_;
    }
    if(!$sessionID)
    {
      if(isset($_COOKIE["session_id"])){
		$sessionID = $_COOKIE["session_id"];
      }
      if(!$sessionID)
      {
#        Logger::getLogger()->info("Creating new empty session");
        Session::startSession(0);
#        $_session_instance = new Session(0);
        return $_session_instance->information_;
      }
    }
#    Logger::getLogger()->info("Creating session based on sessionID: $sessionID");
    $_session_instance = new Session($sessionID);
    return $_session_instance->information_;
  }
  
  // Start a session (once logged in) for 30 minutes. 
  public static function startSession($userID)
  {
    global $_session_instance;
    $db = Database::getDatabase();
#    Logger::getLogger()->info("Start a new session for $userID");
    $return = false;
    $counter = 0;
    while(!$return) {
      $counter++;
      $sessionID = Session::rand_str(40);
      $sql = "insert into WEB_SESSION (SESSION_ID,USER_ID,END_TIME) values (";
      $sql .= "'$sessionID', $userID, now() + interval 3 hour)";
//      Logger::getLogger()->info("Attempting to run this sql: $sql");
      $res = $db->InsertNoAuto($sql);
//      Logger::getLogger()->info("Result: $res");
      if($res > 0) {
        $return = true;
      } else {
        Logger::getLogger()->error("Error generating unique ID");
        if($counter > 20) {
          return false;
        }
      }
    }
    setcookie("session_id", $sessionID, time()+(3600*3), "/");
    $_session_instance = new Session($sessionID);
    return $sessionID;
  }
  public function getUserID() {
    return $this->information_["USER_ID"];
  }
  public static function endSession()
  {
    global $_session_instance;
    Logger::getLogger()->info("endSession: set_cookie session_id=''");
    setcookie("session_id", "", time(), "/");
    $_session_instance = null;
  }
  
  public function setValue($key, $information)
  {
    global $_session_instance;
//    Logger::getLogger()->info("setValue($key, $information)");
    $this->information_[$key] = $information;
    $this->needsSave_ = true;
  } 
  public function getValue($key)
  {
    if($this->information_ && $this->information_[$key])
    {
      return $this->information_[$key];
    }
    return "";
  }
  
  function __destruct()
  {
    $db = Database::getDatabase();
    global $_session_instance;
#    Logger::getLogger()->info("Session destructor called");
    $currentInfoString = "";
    $oldInfoString = "";
    if(isset($this->information_))
    {
      $currentInfoString = serialize($this->information_);
    }
    if(isset($this->oldInformation_))
    {
      $oldInfoString = serialize($this->oldInformation_);
    }
    if($currentInfoString != $oldInfoString)
    {
      $sessionID = $this->information_["sessionid"];
      $sql = "update WEB_SESSION set SESSION_INFO='$currentInfoString' where SESSION_ID='$sessionID'";
#      Logger::getLogger()->info("Saving Updated Session Info");
      $db->Query($sql);
    }
    $_session_instance = null;
    $db->close();
  }

  // Generate a random character string
  public static function rand_str($length = 32, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890')
  {
    // Length of character list
    $chars_length = (strlen($chars) - 1);

    // Start our string
    $string = $chars{rand(0, $chars_length)};
   
    // Generate random string
    for ($i = 1; $i < $length; $i = strlen($string))
    {
      // Grab a random character from our list
      $r = $chars{rand(0, $chars_length)};
     
      // Make sure the same two characters don't appear next to each other
      if ($r != $string{$i - 1}) $string .=  $r;
    }
   
    // Return the string
    return $string;
  }
}


#
#CREATE TABLE WEB_SESSION (
#  SESSION_ID varchar(50) NOT NULL,
#  USER_ID int(11) NOT NULL,
#  END_TIME datetime default NULL,
#  SESSION_INFO text,
#  PRIMARY KEY  (SESSION_ID,USER_ID)
#);

?>
