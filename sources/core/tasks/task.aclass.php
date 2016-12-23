<?php

/**
 * Task Class file definition
 *
 * @author polux <polux@poluxfr.org>
 * @package MyGED
 * @subpackage Tasks
 */

namespace MyGED\Core\Tasks;

use MyGED\Application\Application;
use MyGED\Exceptions as Exceptions;
use MyGED\Vault\Vault;

/**
 * Task Class.
 *
 * Defintion of a generic engine processing some tasks
 */
abstract class AbstractTask
{

    //  CLASS PROPERTIES
    // =========================================================================
    /**
     * Unique Identifier of the Task Object
     *
     * @var string
     */
    protected $_idTask = null;

    /**
     * Title (label) of the Task Object
     *
     * @var string
     */
    protected $_titleTask = '';

    /**
     * Start timestamp of the Task
     *
     * @var timestamp
     */
    protected $_startTimeStamp = 0;

    /**
     * Creation timestamp of the Task Object
     *
     * @var timestamp
     */
    protected $_creationTimeStamp = 0;

    /**
     * End timestamp of the Task
     *
     * @var timestamp
     */
    protected $_endTimeStamp = 0;

    /**
     * Result code of the Task
     *
     * @var integer
     */
    protected $_resultCode = null;

    /**
     * Status of the Task
     *
     * @var string
     */
    protected $_statusTask = 'NEW';

    /**
     * pid of the Task
     *
     * @var string
     */
    protected $_pidTask = '';

    /**
     * Database PDO Object
     *
     * @var \PDOStatement
     * @static
     */
    protected static $_oPdoDBHandler = null;

    //  DEFAULT CONSTRUCTOR
    // =========================================================================

    /**
     * Default Class Constructor - New DBObject.
     *
     * @param string    $pStrUid    UniqueId of DbObject
     * @param \PDO      $pObjPDODb  Database handler
     */
    public function __construct($pStrUid = null, $pObjPDODb = null)
    {
        if (!is_null($pStrUid)) {
            if (!$this->loadTask($pStrUid, $pObjPDODb)) {
                $lArrOptions = array('msg' => 'Technical Error during laoding DBObject (id:'.$pStrUid);
                throw new Exceptions\GenericException('CORE_DB_LOAD_FAIL', $lArrOptions);
            }
        }

        if (!is_null($pObjPDODb)) {
            static::setPDODatabase($pObjPDODb);
        }
    }//end __construct()


    //  ABSTRACT METHOD
    // =========================================================================
    /**
     * Returns a string with json encode specific param
     *
     * @abstract
     *
     * @return array(paramkey=>paramValue)  Array bout Specific Parameters values
     */
    abstract protected function getTaskParametersJSON();

    /**
     * Returns a string with json encode specific param
     *
     * @abstract
     *
     * @return array(paramkey=>paramValue)  Array bout Specific Parameters values
     */
    abstract protected function loadTaskParametersFromJSON($pStrJSONParametersValues);

    // Getters and Setters Methods
    // =========================================================================
    /**
     * Set Status object value (_statusTask)
     *
     * @param string  $pStrStatusValue  Status value
     */
    public function setStatus($pStrStatusValue)
    {
        $this->_statusTask = $pStrStatusValue;
    }//end setStatus()

    /**
     * Get Status object value (_statusTask)
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->_statusTask;
    }//end getStatus()

    /**
     * Set Title object value (_titleTask)
     *
     * @param ParamType  $ParamName ParamDesc
     *
     */
    public function setTitle($pStrTitleValue)
    {
        $this->_titleTask = $pStrTitleValue;
    }//end setTitle()

    /**
     * Get Title object value (_titleTask)
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_titleTask;
    }//end getTitle()

    /**
     * Set ID object value (_idTask)
     *
     * @param string  $pStrIDValue
     */
    protected function setID($pStrIDValue)
    {
        $this->_idTask = $pStrIDValue;
    }//end setID()

    /**
     * Get ID object value (_idTask)
     *
     * @return string
     */
    public function getID()
    {
        return $this->_idTask;
    }//end getID()

    /**
     * Set StartTimeStamp object value (_startTimeStamp)
     *
     * @param timestamp  $pStrStartTimeStampValue
     */
    public function setStartTimeStamp($pStrStartTimeStampValue)
    {
        $this->_startTimeStamp = $pStrStartTimeStampValue;
    }//end setStartTimeStamp()

    /**
     * Get StartTimeStamp object value (_startTimeStamp)
     *
     * @return timestamp
     */
    public function getStartTimeStamp()
    {
        return $this->_startTimeStamp;
    }//end getStartTimeStamp()

    /**
     * Set CreationTimestamp object value (_creationTimeStamp)
     *
     * @param timestamp  $pStrCreationTimestampValue
     */
    public function setCreationTimestamp($pStrCreationTimestampValue)
    {
        $this->_creationTimeStamp = $pStrCreationTimestampValue;
    }//end setCreationTimestamp()

    /**
     * Get CreationTimestamp object value (_creationTimeStamp)
     *
     * @return timestamp
     */
    public function getCreationTimestamp()
    {
        return $this->_creationTimeStamp;
    }//end getCreationTimestamp()

    /**
     * Set EndTimesTamp object value (_endTimeStamp)
     *
     * @param timestamp  $pStrEndTimesTampValue
     */
    public function setEndTimesTamp($pStrEndTimesTampValue)
    {
        $this->_endTimeStamp = $pStrEndTimesTampValue;
    }//end setEndTimesTamp()

    /**
     * Get EndTimesTamp object value (_endTimeStamp)
     *
     * @return timestamp
     */
    public function getEndTimesTamp()
    {
        return $this->_endTimeStamp;
    }//end getEndTimesTamp()

    /**
     * Set ResultCode object value (_resultCode)
     *
     * @param integer  $pIntResultCode
     */
    public function setResultCode($pIntResultCode)
    {
        $this->_resultCode = $pIntResultCode;
    }//end setResultCode()

    /**
     * Get ResultCode object value (_resultCode)
     *
     * @return integer
     */
    public function getResultCode()
    {
        return $this->_resultCode;
    }//end getResultCode()

    /**
     * Set PID object value (_pidTask)
     *
     * @param string  $pStrPIDValue
     */
    public function setPID($pStrPIDValue)
    {
        $this->_pidTask = $pStrPIDValue;
    }//end setPID()

    /**
     * Get PID object value (_pidTask)
     *
     * @return string
     */
    public function getPID()
    {
        return $this->_pidTask;
    }//end getPID()

    /**
     * Set PDODatabase object value (_oPdoDBHandler)
     *
     * @param ParamType  $pStrPDODatabaseValue
     */
    protected static function setPDODatabase($pStrPDODatabaseValue)
    {
        static::$_oPdoDBHandler = $pStrPDODatabaseValue;
    }//end setPDODatabase()

    /**
     * Get PDODatabase object value (_oPdoDBHandler)
     *
     * @return ParamType
     */
    public static function getPDODatabase()
    {
        return static::$_oPdoDBHandler;
    }//end getPDODatabase()

    /**
     * Reset all properties to initial values.
     *
     * @access private
     */
    private function _resetProperties()
    {
        $this->_idTask = null;
        $this->_titleTask = '';
        $this->_startTimeStamp = 0 ;
        $this->_creationTimeStamp = 0;
        $this->_endTimeStamp = 0;
        $this->_resultCode = null;
        $this->_pidTask = '';
        $this->_statusTask = 'NEW';
    }//end _resetProperties()

    // DATABASE Management Methods
    // =========================================================================
    /**
     * Create a Task into DB
     *
     * @param \PDOStatement $pObjPDODatabase    PDOStatement Objet to use.
     * @param string        $pStrUidTaskPrefix  Specific prefix to use for UID Generation.
     *
     * @return string Task ID
     */
    public function createTask($pObjPDODatabase=null, $pStrUidTaskPrefix=null)
    {
        try {
            // PDO Db Object
            if (!is_null($pObjPDODatabase)) {
                $lObjDb = $pObjPDODatabase;
            } elseif (!is_null(self::getPDODatabase())) {
                $lObjDb = self::getPDODatabase();
            } else {
                $lArrOptions = array(
                    'msg' => "Error during storage into SQL DB - No DB Handler defined !"
                );
                throw new Exceptions\GenericException('TASK_DB_NO_DB_HANDLER', $lArrOptions);
            }

            //Define Unique ID of Tasks!
            $lStrPrefixUID = 'tasks-';
            if (!is_null($pStrUidTaskPrefix)) {
                $lStrPrefixUID = $pStrUidTaskPrefix."-";
            }
            $this->setID(Vault::generateUniqueID($lStrPrefixUID));

            // Prepare Data Fields Value !
            $lArrFieldValues = array();

            $lArrFieldValues['task_id']                 = "'".$this->getID()."'";
            $lArrFieldValues['task_title']              = "'".$this->getTitle()."'";
            $lArrFieldValues['task_start_timestamp']    = $this->getStartTimeStamp();
            $lArrFieldValues['task_register_timestamp'] = $this->getCreationTimestamp();
            $lArrFieldValues['task_end_timestamp']      = $this->getEndTimesTamp();
            $lArrFieldValues['task_result_code']        = 0;
            $lArrFieldValues['task_status']             = "'".$this->getStatus()."'";
            $lArrFieldValues['task_pid']                = "'".$this->getPID()."'";
            $lArrFieldValues['task_json_param']         = "'".$this->getTaskParametersJSON()."'";

            // Build SQL Insert Statement!
            //
            //INSERT INTO tasks (task_id,task_title,task_start_timestamp,task_register_timestamp,task_end_timestamp,task_result_code,task_status,task_pid,task_json_param) VALUES ('ocr-585c47493b22d','OCR analysis creation. File to analyze : /var/www/html/php-myged/data/app_vault//files/fic-585c41ae02119.pdf.',0,1482442569,0,,'NEW','','')
            $lStrSQL = sprintf(
                'INSERT INTO %s (%s) VALUES (%s)',
                'tasks',
                join(',', array_keys($lArrFieldValues)),
                join(',', array_values($lArrFieldValues))
              );

            $lObjPdoStat = $lObjDb->query($lStrSQL);

            if (($lObjPdoStat!=false)?($lObjPdoStat->rowCount() != 1):true) {
                $lArrOptions = array(
                  'msg' => sprintf(
                      "Error during storage into SQL DB (ID:%s) - Number of rows impacted : %d - (SQL query : '%s') - PDO Last error : %s",
                      $this->getID(),
                      ($lObjPdoStat!=false)?($lObjPdoStat->rowCount()):'0',
                      $lStrSQL,
                      sprintf("%s - %s", $lObjDb->errorInfo()[0], $lObjDb->errorInfo()[2])
                  )
                );
                throw new Exceptions\GenericException('TASK_DB_STORE_SQL -FAILED', $lArrOptions);
            } else {
                // Reload object from database!
                $lStrTaskUID = $this->getID();
                $this->_resetProperties();
                $this->loadTask($lStrTaskUID);
            }
        } catch (\Exception $ex) {
            $lArrOptions = array('msg' => $ex->getMessage());
            throw new Exceptions\GenericException('TASK_DB_STORE_FAILED', $lArrOptions);
        }
        return $this->getID();
    }//end createTask()


    /**
     * Load a Task from DB
     *
     * @param string        $pStrTaskID       Task ID to load
     * @param \PDOStatement $pObjPDODatabase  PDOStatement Objet to use.
     *
     * @return bool false if not found
     */
    public function loadTask($pStrTaskID, $pObjPDODatabase=null)
    {
        $lBoolResult = false;
        try {
            // PDO Db Object
            if (!is_null($pObjPDODatabase)) {
                $lObjDb = $pObjPDODb;
            } elseif (!is_null(self::getPDODatabase())) {
                $lObjDb = self::getPDODatabase();
            } else {
                $lArrOptions = array(
                  'msg' => "Error during storage into SQL DB - No DB Handler defined !"
              );
                throw new Exceptions\GenericException('TASK_DB_NO_DB_HANDLER', $lArrOptions);
            }

            $lStrSQL = sprintf(
              "SELECT * FROM %s WHERE task_id='%s'",
              'tasks',
              $pStrTaskID
            );

            $lObjPdoStat = $lObjDb->query($lStrSQL);

            if (!$lObjPdoStat) {
                $lArrOptions = array('msg' => $lObjDb->errorInfo()[2].' during execution of this SQL Query : '.$lStrSQL);
                throw new Exceptions\GenericException('TASK_DB_LOAD_PDO_FAIL', $lArrOptions);
            } else {
                $lArrData = $lObjPdoStat->fetchAll(\PDO::FETCH_ASSOC);
                if(count($lArrData) == 0)
                {
                  $lArrOptions = array('msg' => 'No Task "'.$pStrTaskID.'" founded !');
                  throw new Exceptions\GenericException('TASK_NOT_FOUND', $lArrOptions);
                }
                else {

                foreach ($lArrData[0] as $lstrkey => $lStrValue) {
                    $lBoolResult = true;
                    switch ($lstrkey) {
                      case 'task_id':
                        $this->setID($lStrValue);
                        break;
                      case 'task_title':
                        $this->setTitle($lStrValue);
                        break;
                      case 'task_start_timestamp':
                        $this->setStartTimeStamp($lStrValue);
                        break;
                      case 'task_register_timestamp':
                        $this->setCreationTimestamp($lStrValue);
                        break;
                      case 'task_end_timestamp':
                        $this->setEndTimesTamp($lStrValue);
                        break;
                      case 'task_result_code':
                        $this->setResultCode($lStrValue);
                        break;
                      case 'task_status':
                        $this->setStatus($lStrValue);
                        break;
                      case 'task_pid':
                        $this->setPID($lStrValue);
                        break;
                      case 'task_json_param':
                        $this->loadTaskParametersFromJSON($lStrValue);
                        break;
                      default:
                        break;
                    }
                }
              }

            }

        } catch (\Exception $e) {
            $lArrOptions = array('msg' => 'Error during loading a data from DB => '.$e->getMessage());
            throw new Exceptions\GenericException('TASK_DB_LOAD_FAIL', $lArrOptions);
        }

        return $lBoolResult;
    }//end loadTask()

    /**
     * Update Task into DB
     *
     * @param \PDOStatement $pObjPDODatabase    PDOStatement Objet to use.
     *
     * @return boolean True if update OK
     */
    public function updateTask($pObjPDODatabase=null)
    {
        $lBoolResult = false;
        try {
            // PDO Db Object
            if (!is_null($pObjPDODatabase)) {
                $lObjDb = $pObjPDODatabase;
            } elseif (!is_null(self::getPDODatabase())) {
                $lObjDb = self::getPDODatabase();
            } else {
                $lArrOptions = array(
                    'msg' => "Error during storage into SQL DB - No DB Handler defined !"
                );
                throw new Exceptions\GenericException('TASK_DB_NO_DB_HANDLER', $lArrOptions);
            }

            // Prepare Data Fields Value !
            $lStrTaskID  = $this->getID();
            $lArrFieldValues = array();
            $lArrFieldValues['task_title']              = "'".$this->getTitle()."'";
            $lArrFieldValues['task_start_timestamp']    = $this->getStartTimeStamp();
            $lArrFieldValues['task_register_timestamp'] = $this->getCreationTimestamp();
            $lArrFieldValues['task_end_timestamp']      = $this->getEndTimesTamp();
            $lArrFieldValues['task_result_code']        = $this->getResultCode();
            $lArrFieldValues['task_status']             = "'".$this->getStatus()."'";
            $lArrFieldValues['task_pid']                = "'".$this->getPID()."'";
            $lArrFieldValues['task_json_param']         = "'".$this->getTaskParametersJSON()."'";

            $lArrSQLUPDATESetOrders = array();
            foreach ($lArrFieldValues as $key => $value) {
                $lArrSQLUPDATESetOrders[] = $key.' = '.$value;
            }

            // Build SQL Insert Statement!
            $lStrSQL = sprintf(
                    "UPDATE %s SET %s WHERE task_id='%s'",
                    'tasks',
                    join(', ', array_values($lArrSQLUPDATESetOrders)),
                    $lStrTaskID
            );
            $lObjPdoStat = $lObjDb->query($lStrSQL);

            if (($lObjPdoStat!=false)?($lObjPdoStat->rowCount() != 1):true) {
                $lArrOptions = array(
                'msg' => sprintf(
                    "Error during storage into SQL DB (ID:%s) - Number of rows impacted : %d - (SQL query : '%s') - PDO Last error : %s",
                    $lStrTaskID,
                    ($lObjPdoStat!=false)?($lObjPdoStat->rowCount()):'0',
                    $lStrSQL,
                    sprintf("%s - %s", $lObjDb->errorInfo()[0], $lObjDb->errorInfo()[2])
                )
            );
                throw new Exceptions\GenericException('TASK_DB_STORE_SQL -FAILED', $lArrOptions);
            } else {
                // // Reload object from database!
                // $this->_resetProperties();
                // $this->loadTask($lStrTaskID);
                $lBoolResult = true;
            }
        } catch (\Exception $ex) {
            $lArrOptions = array('msg' => $ex->getMessage());
            throw new Exceptions\GenericException('TASK_DB_STORE_FAILED', $lArrOptions);
        }
        return $lBoolResult;
    }//end updateTask()
}//end class
