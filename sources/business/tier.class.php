<?php

/**
 * Tier class file definition
 *
 * @package Core
 * @author polux <polux@poluxfr.org>
 */
namespace MyGED\Business;

use MyGED\Application\App as App;
use MyGED\Core as Core;

/**
 * Tier Class
 *
 * Defintion of a Tier
 */
class Tier extends Core\AbstractDBObject
{

    /**
     * Default Class Constructor - New Tier
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
        return new Tier($pStrDocId);
    }//end getDocById()

     /**
     * Database config set up
     *
     * @static
     */
    public static function setupDBConfig()
    {
        self::$_sIdDBFieldname = 'tier_id';
        self::$_sTitleDBFieldname = 'tier_title';
        self::$_sTableName = 'app_tiers';
        self::$_aFieldNames = array(
            'tier_id',
            'tier_title',
            'tier_code',
            'tier_desc'
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
     * @return array(tierfieldsvalues)    Array of Tiers
     */
    public function getTiersDataForDocument($pStrDocID)
    {
        $lStrSQL = "SELECT tie.tier_id as tier_id,tie.tier_title as tier_title,tie.tier_code as tier_code,tie.tier_desc as tier_desc FROM app_tiers tie INNER JOIN app_asso_docs_tiers ass ON tie.tier_id = ass.tier_id WHERE ass.doc_id = '$pStrDocID'";
        return $this->getDataFromSQLQuery($lStrSQL);
    }//end getCategoriesDataForDocument()
}//end class
