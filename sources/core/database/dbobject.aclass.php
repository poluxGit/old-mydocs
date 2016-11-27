<?php

/**
 * Generic DBObject class file definition.
 *
 * @author polux <polux@poluxfr.org>
 */

namespace MyGED\Core\Database;

use MyGED\Vault\Vault as Vault;
use MyGED\Core\Exceptions as AppExceptions;

/**
 * DBObject Class.
 *
 * Defintion of a generic object stored into Database
 */
abstract class AbstractDBObject
{
    /**
     * Fieldname definition.
     *
     * @var array(string) Array of fieldname about data table
     * @static
     */
    protected static $_aFieldNames = array();

    /**
     * Id DB Fieldname of this object.
     *
     * @var string DB Fieldname value
     * @static
     */
    protected static $_sIdDBFieldname = null;

    /**
     * Title data DB Fieldname of this object.
     *
     * @var string DB Fieldname value
     * @static
     */
    protected static $_sTitleDBFieldname = null;

    /**
     * DB Table definition.
     *
     * @var string Database tablename
     * @static
     */
    protected static $_sTableName = null;

    /**
     * Fields values of Object.
     *
     * @var array(string) Array of fieldname about data table
     */
    protected $_aFieldValues = null;

    /**
     * Fields values of Object updates.
     *
     * @var array(string) Array of fieldname about data table
     */
    protected $_aFieldValuesUpdated = null;

    /**
     * Database Handler
     *
     * @var array(string) Array of fieldname about data table
     */
    protected $_oPdoDBHandler = null;

    /**
     * Flag isNew ?
     *
     * @var bool
     */
    protected $_isNew = false;

    /**
     * Default Class Constructor - New DBObject.
     *
     * @param string    $pStrUid    UniqueId of DbObject
     * @param \PDO      $pObjPDODb  Database handler
     */
    public function __construct($pStrUid = null, $pObjPDODb = null)
    {
        static::setupDBConfig();
        $this->resetObject();

        if (!is_null($pStrUid)) {
            if (!$this->loadDB($pStrUid, $pObjPDODb)) {
                $lArrOptions = array('msg' => 'Technical Error during laoding DBObject (id:'.$pStrUid);
                throw new AppExceptions\GenericException('CORE_DB_LOAD_FAIL', $lArrOptions);
            }
        } else {
            $this->_isNew = true;
        }

        if (!is_null($pObjPDODb)) {
            $this->_oPdoDBHandler = $pObjPDODb;
        }
    }//end __construct()


    /**
     * Reset all attributes of current object.
     *
     * @access private
     */
    private function resetObject()
    {
        $this->_aFieldValuesUpdated = array();
        $this->_aFieldValues        = array();
        $this->_isNew               = false;

        // Instanciation an occurrence for each field into array!
        foreach (self::$_aFieldNames as $lStrDbFieldName) {
            $this->_aFieldValues[$lStrDbFieldName] = null;
        }
    }

    /**
     * Returns current object Uid
     *
     * @return string
     */
    public function getId()
    {
        return $this->getAttributeValue(self::$_sIdDBFieldname);
    }//end getId()

    /**
     * Returns current object  title value
     *
     * @return string
     */
    public function getTitle()
    {
        $lStrResult = null;
        if (array_key_exists(self::$_sTitleDBFieldname, $this->_aFieldValuesUpdated)) {
            $lStrResult = $this->_aFieldValuesUpdated[self::$_sTitleDBFieldname];
        } else {
            $lStrResult = $this->_aFieldValues[self::$_sTitleDBFieldname];
        }

        return $lStrResult;
    }//end getTitle()

    /**
     * Defines Current object title
     *
     * @param string $pStrNewTitle New title value
     */
    public function setTitle($pStrNewTitle)
    {
        $this->setAttributeValue(self::$_sTitleDBFieldname, $pStrNewTitle);
    }//end setTitle()

    /**
     * Store data into Database.
     *
     * @return bool
     */
    protected function storeDataToDB($pObjPDODb=null, $pbAutoGenerateUid=true)
    {
        try {
            // PDO Db Object
            if (!is_null($pObjPDODb)) {
                $lObjDb = $pObjPDODb;
            } elseif (!is_null($this->_oPdoDBHandler)) {
                $lObjDb = $this->_oPdoDBHandler;
            } else {
                $lArrOptions = array(
                    'msg' => "Error during storage into SQL DB - No DB Handler defined !"
                );
                throw new AppExceptions\GenericException('APP_DB_NO_DB_HANDLER', $lArrOptions);
            }
            // INSERT or UPDATE ?
            if ($this->_isNew) {
                // Mode INSERT!
                $lStrFullClassname = get_class($this);
                $laStrClassname = explode('\\', $lStrFullClassname);

                $lStrClassname = $laStrClassname[\count($laStrClassname)-1];

                if (empty($this->_aFieldValues[self::$_sIdDBFieldname])) {
                    if ($pbAutoGenerateUid) {
                        $lStrIdxDoc = Vault::generateUniqueID();
                        $this->_aFieldValues[self::$_sIdDBFieldname] = $lStrIdxDoc;
                    } else {
                        $lArrOptions = array(
                            'msg' => sprintf(
                                "An Uid must be specified on current object before storage into SQL DB (ID:%s)",
                                $lStrIdxDoc
                            )
                        );
                        throw new AppExceptions\GenericException('APP_DB_STORE_SQL - ABORTED', $lArrOptions);
                    }
                } else {
                    $lStrIdxDoc = $this->_aFieldValues[self::$_sIdDBFieldname];
                }
                $lStrSQL = $this->generateSQLInsertOrder();
            } else {
                // Mode UPDATE!
                $lStrSQL = $this->generateSQLUpdateOrder();
                $lStrIdxDoc = $this->getId();
            }

            $lObjPdoStat = $lObjDb->query($lStrSQL);

            if (($lObjPdoStat!=false)?($lObjPdoStat->rowCount() != 1):true) {
                $lArrOptions = array(
                    'msg' => sprintf(
                        "Error during storage into SQL DB (ID:%s) - Number of rows impacted : %d - (SQL query : '%s') - PDO Last error : %s",
                        $lStrIdxDoc,
                        ($lObjPdoStat!=false)?($lObjPdoStat->rowCount()):'0',
                        $lStrSQL,
                        sprintf("%s - %s", $lObjDb->errorInfo()[0], $lObjDb->errorInfo()[2])
                    )
                );
                throw new AppExceptions\GenericException('APP_DB_STORE_SQL -FAILED', $lArrOptions);
            } else {
                // Reload object from database!
                //print_r($lStrIdxDoc);
                $this->resetObject();
                $this->loadDB($lStrIdxDoc, $pObjPDODb);
            }
        } catch (\Exception $ex) {
            $lArrOptions = array('msg' => $ex->getMessage());
            throw new AppExceptions\GenericException('APP_DB_STORE_FAILED', $lArrOptions);
        }

        return true;
    }//end storeDataFromDB()


    /**
     * Store data into Database.
     *
     * @return bool
     */
    protected function deleteDataToDB($pObjPDODb=null)
    {
        try {

            // PDO Db Object
            if (!is_null($pObjPDODb)) {
                $lObjDb = $pObjPDODb;
            } elseif (!is_null($this->_oPdoDBHandler)) {
                $lObjDb = $this->_oPdoDBHandler;
            } else {
                $lArrOptions = array(
                    'msg' => "Error during storage into SQL DB - No DB Handler defined !"
                );
                throw new AppExceptions\GenericException('APP_DB_NO_DB_HANDLER', $lArrOptions);
            }


            $lStrSQL = $this->generateSQLDeleteOrder();
            $lObjPdoStat = $lObjDb->query($lStrSQL);

            if (($lObjPdoStat!=false)?($lObjPdoStat->rowCount() != 1):true) {
                $lArrOptions = array(
                    'msg' => sprintf(
                        "Error during storage into SQL DB (ID:%s) - Number of rows impacted : %d - (SQL query : '%s') - PDO Last error : %s",
                        $this->getId(),
                        ($lObjPdoStat!=false)?($lObjPdoStat->rowCount()):'0',
                        $lStrSQL,
                        sprintf("%s - %s", $lObjDb->errorInfo()[0], $lObjDb->errorInfo()[2])
                    )
                );
                throw new AppExceptions\GenericException('APP_DB_STORE_SQL -FAILED', $lArrOptions);
            }
        } catch (\Exception $ex) {
            $lArrOptions = array('msg' => $ex->getMessage());
            throw new AppExceptions\GenericException('APP_DB_STORE_FAILED', $lArrOptions);
        }

        return true;
    }//end storeDataFromDB()

    /**
     * Returns an attribute value, null if not found
     *
     * @param string $pStrAttrName
     *
     * @return mixed
     */
    public function getAttributeValue($pStrAttrName)
    {
        $lStrResult = null;
        if (array_key_exists($pStrAttrName, $this->_aFieldValuesUpdated)) {
            $lStrResult = $this->_aFieldValuesUpdated[$pStrAttrName];
        } else {
            if (array_key_exists($pStrAttrName, $this->_aFieldValues)) {
                $lStrResult = $this->_aFieldValues[$pStrAttrName];
            }
        }

        if (empty($lStrResult)) {
            $lStrResult = null;
        }

        return $lStrResult;
    }

     /**
     * Returns an array with all field value.
     *
     * @return array(fieldname => fieldvalue)
     */
    public function getAllAttributeValueToArray()
    {
        $lStrArray = array();
        $lStrArray = array_merge($lStrArray, $this->_aFieldValues, $this->_aFieldValuesUpdated);

        return $lStrArray;
    }



    /**
     * Defines an attribute value.
     *
     * @param string $pStrAttrName  Attribute name
     * @param mixed  $pStrValue     Attribute value
     */
    public function setAttributeValue($pStrAttrName, $pStrValue)
    {
        $lStrOldValue = $this->_aFieldValues[$pStrAttrName];

        if ($this->_isNew) {
            $this->_aFieldValues[$pStrAttrName] = $pStrValue;
        } else {
            // Old and new value are differents ?
            if (strcmp($pStrValue, $lStrOldValue) !== 0) {
                $this->_aFieldValuesUpdated[$pStrAttrName] = $pStrValue;
            }
        }
    }

    /**
     * Returns all items of concerned class
     *
     * @param \PDO      $pObjPDODb              Database PDO Object
     * @param string    $pStrWhereCondition     WHERE Condition
     *
     * @return array(mixed)
     * @throws AppExceptions\GenericException
     */
    protected static function getAllItems($pObjPDODb, $pStrWhereCondition=null)
    {
        try {
            // PDO Db Object
            $lObjDb = $pObjPDODb;

            $lStrSQL = self::generateSQLSelectOrder();

            if (!is_null($pStrWhereCondition)) {
                $lStrSQL .= "WHERE ".$pStrWhereCondition;
            }

            //XXX echo $lStrSQL;

            $lObjPdoStat = $lObjDb->query($lStrSQL);

            if (!$lObjPdoStat) {
                $lArrOptions = array('msg' => $lObjDb->errorInfo()[2]." - ".$lStrSQL);
                throw new AppExceptions\GenericException('APP_DB_LOAD_PDO_FAIL', $lArrOptions);
            } else {
                $lArrData = $lObjPdoStat->fetchAll(\PDO::FETCH_ASSOC);
            }
        } catch (\Exception $e) {
            $lArrOptions = array('msg' => $e->getMessage());
            throw new AppExceptions\GenericException('BUSINESS_DATA_GENERIC_LOAD_FAILED', $lArrOptions);
        }

        return $lArrData;
    }//end getAllItems()

    /**
     * Get an array resulting of an SQL Query
     *
     * @throws MyGED\Core\Exceptions\GenericException
     *
     * @param string    $pStrSQL    SQL SELECT QUERY
     * @param \PDO      $pObjPDODb
     *
     * @return array(mixed)  Array of data
     */
    public function getDataFromSQLQuery($pStrSQL, $pObjPDODb=null)
    {
        try {
            // PDO Db Object
            if (!is_null($pObjPDODb)) {
                $lObjDb = $pObjPDODb;
            } elseif (!is_null($this->_oPdoDBHandler)) {
                $lObjDb = $this->_oPdoDBHandler;
            } else {
                $lArrOptions = array(
                    'msg' => "Error during loading from DB - No DB Handler defined !"
                );
                throw new AppExceptions\GenericException('APP_DB_NO_DB_HANDLER', $lArrOptions);
            }

            $lStrSQL = $pStrSQL;

            $lObjPdoStat = $lObjDb->query($lStrSQL);

            if (!$lObjPdoStat) {
                $lArrOptions = array('msg' => $lObjDb->errorInfo()[2]);
                throw new AppExceptions\GenericException('VAULT_DB_LOAD_PDO_FAIL', $lArrOptions);
            } else {
                $lArrData = $lObjPdoStat->fetchAll(\PDO::FETCH_ASSOC);
            }
        } catch (\Exception $e) {
            $lArrOptions = array('msg' => 'Error during loading a data from DB => '.$e->getMessage());
            throw new AppExceptions\GenericException('VAULT_DB_LOAD_FAIL', $lArrOptions);
        }

        return $lArrData;
    }//end getDataFromSQLQuery()

    /**
     * Execute an SQL Query
     *
     * @throws MyGED\Core\Exceptions\GenericException
     *
     * @param string    $pStrSQL    SQL SELECT QUERY
     * @param \PDO      $pObjPDODb
     *
     * @return array(mixed)  Array of data
     */
    public function executeSQLQuery($pStrSQL, $pObjPDODb=null)
    {
        try {
            // PDO Db Object
            if (!is_null($pObjPDODb)) {
                $lObjDb = $pObjPDODb;
            } elseif (!is_null($this->_oPdoDBHandler)) {
                $lObjDb = $this->_oPdoDBHandler;
            } else {
                $lArrOptions = array(
                    'msg' => "Error during loading from DB - No DB Handler defined !"
                );
                throw new AppExceptions\GenericException('APP_DB_NO_DB_HANDLER', $lArrOptions);
            }

            $lStrSQL = $pStrSQL;

            $lObjPdoStat = $lObjDb->query($lStrSQL);

            if (!$lObjPdoStat) {
                $lArrOptions = array('msg' => $lObjDb->errorInfo()[2]);
                throw new AppExceptions\GenericException('DB_EXEC_SQL_PDO_FAIL', $lArrOptions);
            }
        } catch (\Exception $e) {
            $lArrOptions = array('msg' => 'Error during loading a data from DB => '.$e->getMessage());
            throw new AppExceptions\GenericException('DB_EXEC_SQL_PDO_FAIL', $lArrOptions);
        }

        return true;
    }//end executeSQLQuery()


    /**
     * Load Object from Database.
     *
     * @throws MyGED\Core\Exceptions\GenericException
     *
     * @param string    $pStrSQL    SQL SELECT QUERY
     * @param \PDO      $pObjPDODb
     *
     * @return array(mixed)  Array of data
     */
    public function loadDB($pStrUid, $pObjPDODb=null)
    {
        try {
            // PDO Db Object
            if (!is_null($pObjPDODb)) {
                $lObjDb = $pObjPDODb;
            } elseif (!is_null($this->_oPdoDBHandler)) {
                $lObjDb = $this->_oPdoDBHandler;
            } else {
                $lArrOptions = array(
                    'msg' => "Error during loading from DB - No DB Handler defined !"
                );
                throw new AppExceptions\GenericException('APP_DB_NO_DB_HANDLER', $lArrOptions);
            }

            $lStrSQL = self::generateSQLSelectOrder();
            $lStrSQL .= $this->getSQLWhereCondition($pStrUid);


            $lObjPdoStat = $lObjDb->query($lStrSQL);

            if (!$lObjPdoStat) {
                $lArrOptions = array('msg' => $lObjDb->errorInfo()[2]);
                throw new AppExceptions\GenericException('VAULT_DB_LOAD_PDO_FAIL', $lArrOptions);
            } else {
                $lArrData = $lObjPdoStat->fetchAll(\PDO::FETCH_ASSOC);
                foreach ($lArrData[0] as $lstrkey => $lStrValue) {
                    $this->_aFieldValues[$lstrkey] = $lStrValue;
                }
            }
        } catch (\Exception $e) {
            $lArrOptions = array('msg' => 'Error during loading a data from DB => '.$e->getMessage());
            throw new AppExceptions\GenericException('VAULT_DB_LOAD_FAIL', $lArrOptions);
        }

        return true;
    }//end loadDB()


    /**
     * Returns a SQL SELECT FIELD part of query
     *
     * @return string 'FieldName1, Fieldname2 ....'
     */
    protected static function getFieldNamesToSQLStringDefinition()
    {
        $lStrFieldNames = '';

        foreach (self::$_aFieldNames as $lStrFieldDef) {
            if (!empty($lStrFieldNames)) {
                $lStrFieldNames .= ', ';
            }

            $lStrFieldNames .= $lStrFieldDef;
        }

        return $lStrFieldNames;
    }//end getFieldNamesToSQLStringDefinition()

    /**
     * Returns a SQL INSERT VALUES FIELD part of insert query.
     *
     * @internal same order than definition of fields
     *
     * @return string 'FieldValue','FieldValue2' ....'
     */
    protected function getFieldValuesToSQLStringForInsert()
    {
        $lStrFieldNames = '';

        foreach (self::$_aFieldNames as $lStrFieldDef) {
            if (!empty($lStrFieldNames)) {
                $lStrFieldNames .= ', ';
            }

            if (array_key_exists($lStrFieldDef, $this->_aFieldValuesUpdated)) {
                $lStrFieldNames .= sprintf(" '%s'", $this->_aFieldValuesUpdated[$lStrFieldDef]);
            } elseif (array_key_exists($lStrFieldDef, $this->_aFieldValues)) {
                $lStrFieldNames .= sprintf(" '%s'", $this->_aFieldValues[$lStrFieldDef]);
            } else {
                $lStrFieldNames .= " ''";
            }
        }
        return $lStrFieldNames;
    }//end getFieldValuesToSQLStringForInsert()

    /**
     * Returns WHERE Condition filetring by uid of current object
     *
     * @param mixed $pMixedValue
     *
     * @return string
     */
    protected function getSQLWhereCondition($pStrValue = null)
    {
        $lStrValue = ' WHERE ';
        if (is_null($pStrValue)) {
            $lStrValue .= self::$_sIdDBFieldname." ='".$this->_aFieldValues[self::$_sIdDBFieldname]."' ";
        } else {
            $lStrValue .= self::$_sIdDBFieldname." ='".$pStrValue."' ";
        }

        return $lStrValue;
    }//end getSQLWhereCondition()

    /**
     * Returns a SQL UPDATE VALUES FIELD DEFINTION.
     *
     * @return string Fieldname1 = 'FieldValue1', Fieldname2 = 'FieldValue2' ...
     *
     * @return string Update SQL Query value
     */
    protected function getFieldValuesToSQLStringForUpdate()
    {
        $lStrFieldNames = '';

        foreach (self::$_aFieldNames as $lStrFieldDef) {
            if (array_key_exists($lStrFieldDef, $this->_aFieldValuesUpdated) && !empty($this->_aFieldValuesUpdated[$lStrFieldDef])) {
                if (!empty($lStrFieldNames)) {
                    $lStrFieldNames .= ', ';
                }

                $lStrFieldNames .= sprintf(" %s = '%s'", $lStrFieldDef, $this->_aFieldValuesUpdated[$lStrFieldDef]);
            }
        }

        return $lStrFieldNames;
    }//end getFieldValuesToSQLStringForUpdate()

    /**
     * Generate A Simple SQL Select Order (wo WHERE)
     *
     * @return string SELECT Query for all object
     */
    protected static function generateSQLSelectOrder()
    {
        return sprintf(
                'SELECT %s FROM %s ',
                self::getFieldNamesToSQLStringDefinition(),
                self::$_sTableName);
    }

    /**
     * Generate A Simple SQL Insert Order
     *
     * @return string INSERT Query for current object
     */
    protected function generateSQLInsertOrder()
    {
        return sprintf(
                'INSERT INTO %s (%s) VALUES (%s)',
                self::$_sTableName,
                self::getFieldNamesToSQLStringDefinition(),
                $this->getFieldValuesToSQLStringForInsert()

        );
    }

    /**
     * Generate A Simple SQL Update Order for current object
     *
     * @return string UPDATE Query for current object
     */
    protected function generateSQLUpdateOrder()
    {
        return sprintf(
                'UPDATE %s SET %s %s',
                self::$_sTableName,
                $this->getFieldValuesToSQLStringForUpdate(),
                $this->getSQLWhereCondition()
        );
    }

     /**
     * Generate A Simple SQL Delete Order for current object
     *
     * @return string DELETE Query for current object
     */
    protected function generateSQLDeleteOrder()
    {
        return sprintf(
                'DELETE FROM %s %s',
                self::$_sTableName,
                $this->getSQLWhereCondition()
        );
    }

    /**
     * Returns TRUE if Fieldname defined for current class.
     *
     * @param string $pStrFieldName Name of the field to check.
     *
     * @return boolean  TRUE if Fieldanme defined elseif FALSE
     */
    public function isValidFieldForClass($pStrFieldName)
    {
        return !empty(array_search($pStrFieldName, static::$_aFieldNames, true));
    }

    /**
     * Database config set up.
     *
     * @example implementation must set internal attributes $_sIdDBFieldname, $_sTableName, $_sTitleDBFieldname
     * @abstract
     */
    abstract public static function setupDBConfig();

    /**
     * Returns all Class Items filtered.
     *
     * @return array(mixed)
     * @abstract
     */
    abstract public static function getAllClassItemsData($pStrWhereCondition);

    /**
     * Record data into database
     *
     * @return boolean TRUE if OK
     * @abstract
     */
    abstract public function store();

     /**
     * Delete data into database
     *
     * @return boolean TRUE if OK
     * @abstract
     */
    abstract public function delete();
}//end class
