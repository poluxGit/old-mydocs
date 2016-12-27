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


    // DATABASE Management Methods
    // =========================================================================
    // /**
    //  * Create a Task into DB
    //  *
    //  * @param \PDOStatement $pObjPDODatabase    PDOStatement Objet to use.
    //  * @param string        $pStrUidTaskPrefix  Specific prefix to use for UID Generation.
    //  *
    //  * @return string Task ID
    //  */
    // public function createTask($pObjPDODatabase=null, $pStrUidTaskPrefix=null)
    // {
    //     try {
    //         // PDO Db Object
    //         if (!is_null($pObjPDODatabase)) {
    //             $lObjDb = $pObjPDODatabase;
    //         } elseif (!is_null(self::getPDODatabase())) {
    //             $lObjDb = self::getPDODatabase();
    //         } else {
    //             $lArrOptions = array(
    //                 'msg' => "Error during storage into SQL DB - No DB Handler defined !"
    //             );
    //             throw new Exceptions\GenericException('TASK_DB_NO_DB_HANDLER', $lArrOptions);
    //         }
    //
    //         //Define Unique ID of Tasks!
    //         $lStrPrefixUID = 'tasks-';
    //         if (!is_null($pStrUidTaskPrefix)) {
    //             $lStrPrefixUID = $pStrUidTaskPrefix."-";
    //         }
    //         $this->setID(Vault::generateUniqueID($lStrPrefixUID));
    //
    //         // Prepare Data Fields Value !
    //         $lArrFieldValues = array();
    //
    //         $lArrFieldValues['task_id']                 = "'".$this->getID()."'";
    //         $lArrFieldValues['task_title']              = "'".$this->getTitle()."'";
    //         $lArrFieldValues['task_start_timestamp']    = $this->getStartTimeStamp();
    //         $lArrFieldValues['task_register_timestamp'] = $this->getCreationTimestamp();
    //         $lArrFieldValues['task_end_timestamp']      = $this->getEndTimesTamp();
    //         $lArrFieldValues['task_result_code']        = 0;
    //         $lArrFieldValues['task_status']             = "'".$this->getStatus()."'";
    //         $lArrFieldValues['task_pid']                = "'".$this->getPID()."'";
    //         $lArrFieldValues['task_json_param']         = "'".$this->getTaskParametersJSON()."'";
    //
    //         // Build SQL Insert Statement!
    //         //
    //         //INSERT INTO tasks (task_id,task_title,task_start_timestamp,task_register_timestamp,task_end_timestamp,task_result_code,task_status,task_pid,task_json_param) VALUES ('ocr-585c47493b22d','OCR analysis creation. File to analyze : /var/www/html/php-myged/data/app_vault//files/fic-585c41ae02119.pdf.',0,1482442569,0,,'NEW','','')
    //         $lStrSQL = sprintf(
    //             'INSERT INTO %s (%s) VALUES (%s)',
    //             'tasks',
    //             join(',', array_keys($lArrFieldValues)),
    //             join(',', array_values($lArrFieldValues))
    //           );
    //
    //         $lObjPdoStat = $lObjDb->query($lStrSQL);
    //
    //         if (($lObjPdoStat!=false)?($lObjPdoStat->rowCount() != 1):true) {
    //             $lArrOptions = array(
    //               'msg' => sprintf(
    //                   "Error during storage into SQL DB (ID:%s) - Number of rows impacted : %d - (SQL query : '%s') - PDO Last error : %s",
    //                   $this->getID(),
    //                   ($lObjPdoStat!=false)?($lObjPdoStat->rowCount()):'0',
    //                   $lStrSQL,
    //                   sprintf("%s - %s", $lObjDb->errorInfo()[0], $lObjDb->errorInfo()[2])
    //               )
    //             );
    //             throw new Exceptions\GenericException('TASK_DB_STORE_SQL -FAILED', $lArrOptions);
    //         } else {
    //             // Reload object from database!
    //             $lStrTaskUID = $this->getID();
    //             $this->_resetProperties();
    //             $this->loadTask($lStrTaskUID);
    //         }
    //     } catch (\Exception $ex) {
    //         $lArrOptions = array('msg' => $ex->getMessage());
    //         throw new Exceptions\GenericException('TASK_DB_STORE_FAILED', $lArrOptions);
    //     }
    //     return $this->getID();
    // }//end createTask()


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
        self::$_sUIDPrefix='task-';
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
}//end class
