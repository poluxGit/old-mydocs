<?php

/**
 * MetaDocument class file definition
 *
 * @package Core
 * @author polux <polux@poluxfr.org>
 */
namespace MyGED\Business;

use MyGED\Application\Application as App;
use MyGED\Core as Core;
use MyGED\Core\Database\AbstractDBObject;

/**
 * MetaDocument Class
 *
 * Defintion of a MetaDocument
 */
class MetaDocument extends AbstractDBObject
{
    /**
     * Default Class Constructor - New MetaDocument
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
        return new MetaDocument($pStrDocId);
    }//end getDocById()

     /**
     * Database config set up
     *
     * @static
     */
    public static function setupDBConfig()
    {
        self::$_sIdDBFieldname = 'meta_id';
        self::$_sTitleDBFieldname = 'mdoc_title';
        self::$_sTableName = 'app_meta_doc';
        self::$_aFieldNames = array(
            'meta_id',
            'doc_id',
            'tdoc_id',
            'mdoc_title',
            'mdoc_value'
        );
        self::$_sUIDPrefix='mdoc-';
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
     *
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
     * Returns all records about your class about a Document
     *
     * @param string $pStrDocUID Document Uid concerned.
     *
     * @return array(mixed)
     */
    public static function getAllItemsDataFromDocument($pStrDocUID)
    {
        $lStrWhereCondition = " doc_id = '$pStrDocUID' ";
        static::setupDBConfig();
        return static::getAllItems(App::getAppDabaseObject(), $lStrWhereCondition);
    }//end getAllItemsDataFromDocument()
}//end class
