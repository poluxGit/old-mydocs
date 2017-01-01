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
use MyGED\Core\Database\AbstractDBObject as AbstractDBObject;

/**
 * Task Class.
 *
 * Defintion of a generic engine processing some tasks
 */
class Task extends AbstractDBObject
{

    /**
     * Array of Specific Parameters key=>value
     *
     * @var array(mixed)
     * @access protected
     */
    protected $_aSpecificParams = array();


    //  DEFAULT CONSTRUCTOR
    // =========================================================================

    /**
     * Default Class Constructor - New Categorie
     *
     * @param string    $pStrUid    UniqueId of DbObject
     */
    public function __construct($pStrUid=null)
    {
        parent::__construct($pStrUid, Application::getAppDabaseObject());
    }//end __construct()

    /**
     * Returns a string with json encode specific param
     *
     * @return string  JSON encode string from  Specific Parameters Array values
     */
    protected function getTaskParametersJSON()
    {
        return json_encode($this->_aSpecificParams);
    }

    /**
     * Returns a string with json encode specific param
     *
     * @return array(paramkey=>paramValue)  Array bout Specific Parameters values
     */
    protected function loadTaskParametersFromJSON($pStrJSONParametersValues)
    {
        $this->_aSpecificParams = json_decode($pStrJSONParametersValues);
    }

    // Getters and Setters Methods
    // =========================================================================
    /**
     * Defines a task's attribute value.
     *
     * @param string $pStrAttrName  Attribute name
     * @param mixed  $pStrValue     Attribute value
     */
    public function setTaskAttributeValue($pStrAttrName, $pStrValue)
    {
        $this->_aSpecificParams[$pStrAttrName] = $pStrValue;
    }//end setTaskAttributeValue()

    /**
     * Returns a task's attribute value, null if not found
     *
     * @param string $pStrAttrName
     *
     * @return mixed
     */
    public function getTaskAttributeValue($pStrAttrName)
    {
        $lStrResult = null;
        if (array_key_exists($pStrAttrName, $this->_aSpecificParams)) {
            $lStrResult = $this->_aFieldValuesUpdated[$pStrAttrName];
        }

        if (empty($lStrResult)) {
            $lStrResult = null;
        }

        return $lStrResult;
    }//end getAttributeValue()

    /**
     * Set Status object value (_statusTask)
     *
     * @param string  $pStrStatusValue  Status value
     */
    public function setStatus($pStrStatusValue)
    {
        $this->setAttributeValue('task_status', $pStrStatusValue);
    }//end setStatus()

    /**
     * Get Status object value (_statusTask)
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->getAttributeValue('task_status');
    }//end getStatus()

    /**
     * Set StartTimeStamp object value (_startTimeStamp)
     *
     * @param timestamp  $pStrStartTimeStampValue
     */
    public function setStartTimeStamp($pStrStartTimeStampValue)
    {
        $this->setAttributeValue('task_start_timestamp', $pStrStartTimeStampValue);
    }//end setStartTimeStamp()

    /**
     * Get StartTimeStamp object value (_startTimeStamp)
     *
     * @return timestamp
     */
    public function getStartTimeStamp()
    {
        return $this->getAttributeValue('task_start_timestamp');
    }//end getStartTimeStamp()

    /**
     * Set CreationTimestamp object value (_creationTimeStamp)
     *
     * @param timestamp  $pStrCreationTimestampValue
     */
    public function setCreationTimestamp($pStrCreationTimestampValue)
    {
        $this->setAttributeValue('task_register_timestamp', $pStrCreationTimestampValue);
    }//end setCreationTimestamp()

    /**
     * Get CreationTimestamp object value (_creationTimeStamp)
     *
     * @return timestamp
     */
    public function getCreationTimestamp()
    {
        return $this->getAttributeValue('task_register_timestamp');
    }//end getCreationTimestamp()

    /**
     * Set EndTimesTamp object value (_endTimeStamp)
     *
     * @param timestamp  $pStrEndTimesTampValue
     */
    public function setEndTimesTamp($pStrEndTimesTampValue)
    {
        $this->setAttributeValue('task_end_timestamp', $pStrEndTimesTampValue);
    }//end setEndTimesTamp()

    /**
     * Get EndTimesTamp object value (_endTimeStamp)
     *
     * @return timestamp
     */
    public function getEndTimesTamp()
    {
        return $this->getAttributeValue('task_end_timestamp');
    }//end getEndTimesTamp()

    /**
     * Set ResultCode object value (_resultCode)
     *
     * @param integer  $pIntResultCode
     */
    public function setResultCode($pIntResultCode)
    {
        $this->setAttributeValue('task_result_code', $pIntResultCode);
    }//end setResultCode()

    /**
     * Get ResultCode object value (_resultCode)
     *
     * @return integer
     */
    public function getResultCode()
    {
        return $this->getAttributeValue('task_result_code');
    }//end getResultCode()

    /**
     * Set PID object value (_pidTask)
     *
     * @param string  $pStrPIDValue
     */
    public function setPID($pStrPIDValue)
    {
        $this->setAttributeValue('task_pid', $pStrPIDValue);
    }//end setPID()

    /**
     * Get PID object value (_pidTask)
     *
     * @return string
     */
    public function getPID()
    {
        return $this->getAttributeValue('task_pid');
    }//end getPID()

    /***************************************************************************
     *  Extending AbstractDBObject
     ***************************************************************************/
    /**
     * Database config set up.
     *
     */
    public static function setupDBConfig()
    {
        self::$_sIdDBFieldname = 'task_id';
        self::$_sTitleDBFieldname = 'task_title';
        self::$_sTableName = 'tasks';
        self::$_aFieldNames = array(
          'task_id',
          'task_title',
          'task_start_timestamp',
          'task_register_timestamp',
          'task_end_timestamp',
          'task_result_code',
          'task_status',
          'task_pid',
          'task_json_param'
        );
        self::$_sUIDPrefix='task';
    }

    /**
     * Returns all Class Items filtered.
     *
     * @return array(mixed)
     * @abstract
     */
    public static function getAllClassItemsData($pStrWhereCondition)
    {
        return static::getAllItems(Application::getAppDabaseObject(), $pStrWhereCondition);
    }//end getAllClassItemsData()

    /**
     * Record data into database
     *
     * @return boolean TRUE if OK
     */
    public function store()
    {
        $this->setAttributeValue('task_json_param', $this->getTaskParametersJSON());
        return parent::storeDataToDB(Application::getAppDabaseObject());
    }//end store()

     /**
     * Delete data into database
     *
     * @return boolean TRUE if OK
     * @abstract
     */
    public function delete()
    {
        return parent::deleteDataToDB(Application::getAppDabaseObject());
    }//end delete()

    /**
     * Add A Log message into Database relative to the current task object.
     *
     * @param string $pStrLogMessage  Message to Log
     *
     * @return  boolean TRUE if OK, FALSE else
     */
      public function addLogMessageAboutCurrentTask($pStrLogMessage)
      {
          try {
              $lStrSQL = sprintf(
                "INSERT INTO tasks_log (task_id,task_log_description,task_log_timestamp,task_status,task_pid)
                 VALUES ('%s','%s',%d,'%s',0)",
                $this->getId(),
                $pStrLogMessage,
                time(),
                $this->getStatus()
              );

              $this->executeSQLQuery($lStrSQL);
          } catch (Exception $ex) {
              $lArrOptions = array('msg' => 'Error during execution of a SQL Statement => '.$ex->getMessage());
              throw new AppExceptions\GenericException('DB_EXEC_SQL_PDO_FAIL', $lArrOptions);
          }

          return true;
      }//end addLogMessageAboutCurrentTask()

      /**
       * Returns an array with all log messages (inverse chronologically ordered)
       *
       * @return array(task_logs_attributes)  Array containg all messages about current task.
       */
      public function getAllLogsMessageOnCurrentTask()
      {
          $lArrResult = null;
          try {
              $lStrSQL = sprintf(
                    "SELECT task_id,task_log_description,task_log_timestamp,task_status,task_pid
                     FROM tasks_log
                     WHERE task_id='%s'
                     ORDER BY task_log_timestamp DESC",
                    $this->getId()
            );

              $lArrResult = $this->getDataFromSQLQuery($lStrSQL);
          } catch (Exception $ex) {
              $lArrOptions = array('msg' => 'Error during execution of a SQL Statement => '.$ex->getMessage());
              throw new AppExceptions\GenericException('DB_EXEC_SQL_PDO_FAIL', $lArrOptions);
          }

          return $lArrResult;
      }//end getAllLogsMessageOnCurrentTask()
}//end class
