<?php

/**
 * Categorie class file definition
 *
 * @package Core
 * @author polux <polux@poluxfr.org>
 */
namespace MyGED\Business;

use MyGED\Application\Application as App;
use MyGED\Core as Core;
use MyGED\Core\Database\AbstractDBObject;

/**
 * Categorie Class
 *
 * Defintion of a Categorie
 */
class Categorie extends AbstractDBObject
{

    /**
     * Default Class Constructor - New Categorie
     */
    public function __construct($pStrUid=null)
    {
        parent::__construct($pStrUid, App::getAppDabaseObject());
    }//end __construct()

    /**
     * getDocById
     *
     * Returns a Categorie by his id
     *
     * @param string $pStrDocId
     * @return \Document
     */
    public static function getDocById($pStrDocId)
    {
        return new Categorie($pStrDocId);
    }//end getDocById()

     /**
     * Database config set up
     *
     * @static
     */
    public static function setupDBConfig()
    {
        self::$_sIdDBFieldname = 'cat_id';
        self::$_sTitleDBFieldname = 'cat_title';
        self::$_sTableName = 'app_categories';
        self::$_aFieldNames = array(
            'cat_id',
            'cat_title',
            'cat_code',
            'cat_desc'
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
     * Returns an array containing categories data wich are linked to the Document
     *
     * @param string $pStrDocID Document UID
     *
     * @return array(categories)    Array of categories
     */
    public function getCategoriesDataForDocument($pStrDocID)
    {
        $lStrSQL = "SELECT cat.cat_id as cat_id,cat.cat_title as cat_title,cat.cat_code as cat_code,cat.cat_desc as cat_desc FROM app_categories cat INNER JOIN app_asso_docs_cats ass ON cat.cat_id = ass.cat_id WHERE ass.doc_id = '$pStrDocID'";
        return $this->getDataFromSQLQuery($lStrSQL);
    }//end getCategoriesDataForDocument()
}//end class
