<?php

/**
 * TypeDocument class file definition
 *
 * @package Core
 * @author polux <polux@poluxfr.org>
 */
namespace MyGED\Business;

use MyGED\Application\App as App;
use MyGED\Core as Core;

/**
 * TypeDocument Class
 *
 * Defintion of a TypeDocument
 */
class TypeDocument extends Core\AbstractDBObject
{

    /**
     * Default Class Constructor - New TypeDocument
     */
    public function __construct($pStrUid=null)
    {
        parent::__construct($pStrUid, App::getAppDabaseObject());
    }//end __construct()

    /**
     * getDocById
     *
     * Returns a Document by his id
     *
     * @param string $pStrDocId
     * @return \Document
     */
    public static function getDocById($pStrDocId)
    {
        // TODO To dev when vault OK
        return new TypeDocument($pStrDocId);
    }//end getDocById()

     /**
     * Database config set up
     *
     * @static
     */
    public static function setupDBConfig()
    {
        self::$_sIdDBFieldname = 'tdoc_id';
        self::$_sTitleDBFieldname = 'tdoc_title';
        self::$_sTableName = 'app_typesdoc';
        self::$_aFieldNames = array(
            'tdoc_id',
            'tdoc_title',
            'tdoc_code',
            'tdoc_desc'
        );
    }

    /**
     * Store Data
     */
    public function store()
    {
        parent::storeDataToDB(App::getAppDabaseObject());
    }

    /**
     * Returns all records about your class
     *
     * @param string $pStrWhereCondition Filtering Condition (without WHERE)
     * @return array(mixed)
     */
    public static function getAllClassItemsData($pStrWhereCondition=null)
    {
        return static::getAllItems(App::getAppDabaseObject(), $pStrWhereCondition);
    }//end getAllClassItemsData()

     /**
     * Delete Data
     */
    public function delete()
    {
        return parent::deleteDataToDB(App::getAppDabaseObject());
    }//end store()

    /**
     * Returns array including all metadata data of document
     */
    public function getAllMetadataDataInArray()
    {
        return MetaTypeDocument::getAllItemsDataFromTypeDocument($this->getId());
    }
}//end class
