<?
require_once("Logging.php");
require_once("Config.php");
$_myDB = null;

class Database
{

  var $connection_         = null;
  var $pass_               = null;
  var $login_              = null;
  var $host_               = null;
  var $transactionStarted_ = null;
  var $db_                 = null;
  var $closed_             = true;
  var $log_                = null;
  function Database()
  {
    $config = Config::getInstance();

    $this->log_ = Logger::getLogger();
    $this->login_   = $config->dbUser_;
    $this->pass_    = $config->dbPass_;
    $this->host_    = $config->dbHost_;
    $this->db_      = $config->dbDB_;
    $this->closed_ = false;
#    $this->log_->info("Creating connection with " . $this->host_ . "," . $this->login_ . ", " . $this->pass_ . ", " . $this->db_);
    $this->connection_ = mysqli_connect($this->host_, $this->login_, $this->pass_, $this->db_);
    if (!mysqli_ping($this->connection_))
    {
      echo mysqli_error($this->connection_);
      echo "Error connecting to DB at " . $this->host_ . " With login " . $this->login_ . " And password " . $this->pass_;
      exit;
    }
  }
  
  public static function getDatabase()
  {
    global $_myDB;
    if( is_object($_myDB))
    {
      return $_myDB;
    }
    $_myDB = new Database();
    return $_myDB;
  }

  function ping()
  {
    return mysqli_ping($this->connection_);
  }

  function InsertNoAuto($sql)
  {
    if(!mysqli_ping( $this->connection_ ) )
    {
      $this->log_->error("We lost connection to DB");
    }
    try
    {
#      $this->log_->query($sql);
      $result = mysqli_query($this->connection_, $sql);
      $affectedRows = mysqli_affected_rows($this->connection_);
    }
    catch(Exception $e)
    {
      $this->log_->error($e->getMessage());
      $this->log_->backtrace();
    }
    return $affectedRows;

  }
  function Insert($sql)
  {
    if(!mysqli_ping( $this->connection_ ) )
    {
      $this->log_->error("We lost connection to DB");
    }
    try
    {
#      $this->log_->query($sql);
      $result = mysqli_query($this->connection_, $sql);
    }
    catch(Exception $e)
    {
      $this->log_->error($e->getMessage());
      $this->log_->backtrace();
    }
    if(mysqli_error($this->connection_))
    {
      return 0;
    }
    $lastItemID = mysqli_insert_id($this->connection_);
    return $lastItemID;

  }
  function Query($sql)
  {
    if(!mysqli_ping( $this->connection_ ) )
    {
      $this->log_->error("We lost connection to DB");
    }
    try
    {
#      $this->log_->query($sql);
      $result = mysqli_query($this->connection_, $sql);
    }
    catch(Exception $e)
    {
      $this->log_->error($e->getMessage());
      $this->log_->backtrace();
    }
    return $result;
  }
  function Update($sql)
  {
    if(!mysqli_ping( $this->connection_ ) )
    {
      $this->log_->error("We lost connection to DB");
    }
#    $this->log_->query($sql);
    try
    {
      $result = mysqli_query($this->connection_, $sql);
    }
    catch(Exception $e)
    {
      $this->log_->error($e->getMessage());
      $this->log_->backtrace();
    }
    $affectedRows = mysqli_affected_rows($this->connection_);
    return $affectedRows;
  }

  function startTransaction()
  {
    if($this->transactionStarted_)
    {
      $this->log_->error("[ERROR] - Transaction already started");
      return false;
    }
#    $this->log_->query("Start Transaction");
    $this->transactionStarted_ = 1;
    mysqli_query($this->connection_, "set autocommit=0");
    mysqli_query($this->connection_, "START TRANSACTION");
    return true;
  } 

  function stopTransaction()
  {
    if(!$this->transactionStarted_)
    {
      $this->log_->error("[ERROR] - Transaction never started");
      return false;
    }
#    $this->log_->query("Stop Transaction");
    $this->transactionStarted_ = 0;
    mysqli_query($this->connection_, "STOP TRANSACTION");
    mysqli_query($this->connection_, "set autocommit=1");
    return true;
  }

  function commit()
  {
#    $this->log_->query("In Commit");
    if(!$this->transactionStarted_)
    {
      $this->log_->error("Transaction never started, Can't commit");
      return false;
    }
#    $this->log_->query("Commit and End Transaction");
    mysqli_query($this->connection_, "COMMIT");
    mysqli_query($this->connection_, "set autocommit=1");
    $this->transactionStarted_ = 0;
    return true;
  } 
  function ms2($val) {
    return mysqli_real_escape_string($this->connection_,$val);
  }
  function ms($val) {
//    return mysqli_real_escape_string($this->connection_,$val);
//    return mysqli_real_escape_string($val, $this->connection_);
    return mysqli_real_escape_string($this->connection_,$val);
#    return addslashes($val);
  }
  function rollback()
  {
    $this->log_->info("In rollback");
    if(!$this->transactionStarted_)
    {
      $this->log_->error("Transaction never started, Can't rollback");
      return false;
    }
    $this->log_->query("Rollback");
    mysqli_query($this->connection_, "ROLLBACK"); 
    mysqli_query($this->connection_, "set autocommit=1");
    $this->transactionStarted_ = 0;
    return true;
  }
  function getLastError()
  {
    return $this->connection_->error;
  }
  function close()
  {
    $this->__destruct();
  }
  function real_escape_string($str) {
    return $this->connection_->real_escape_string($str);
  }
  function __destruct()
  {
#    $this->log_->info("Database Destructor Called");
    if(!$this->closed_)// We might have already called it.
    {
      if(mysqli_ping( $this->connection_ ) )
      {
        mysqli_close($this->connection_);
        $this->closed_ = true;
      }
    }
  }
}
?>