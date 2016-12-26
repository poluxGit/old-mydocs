<?php

/**
 * MetaTypeDocument class file definition
 *
 * @package Core
 * @author polux <polux@poluxfr.org>
 */
namespace MyGED\Business;

use MyGED\Application\Application as App;
use MyGED\Core as Core;
use MyGED\Core\Database\AbstractDBObject;

/**
 * MetaTypeDocument Class
 *
 * Defintion of a MetaTypeDocument
 */
class MetaTypeDocument extends AbstractDBObject
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
        return new MetaTypeDocument($pStrDocId);
    }//end getDocById()

     /**
     * Database config set up
     *
     * @static
     */
    public static function setupDBConfig()
    {
        self::$_sIdDBFieldname = 'meta_id';
        self::$_sTitleDBFieldname = 'meta_title';
        self::$_sTableName = 'app_meta_tdoc';
        self::$_aFieldNames = array(
            'meta_id',
            'tdoc_id',
            'meta_title',
            'meta_desc',
            'meta_datatype',
            'meta_pattern',
            'meta_required',
            'meta_placeholder',
            'meta_mask',
            'meta_json_html_attributes'
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
     * Returns all records about your class about a Type of Document
     *
     * @param string $pStrTypeDocUID  Type of Document Uid concerned.
     *
     * @return array(mixed)
     */
    public static function getAllItemsDataFromTypeDocument($pStrTypeDocUID)
    {
        $lStrWhereCondition = " tdoc_id = '$pStrTypeDocUID' ";
        static::setupDBConfig();
        return static::getAllItems(App::getAppDabaseObject(), $lStrWhereCondition);
    }//end getAllItemsDataFromTypeDocument()
}//end class
