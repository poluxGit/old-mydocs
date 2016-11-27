<?php

/**
 * DatabaseTools Class definition
 *
 * @package MyGED
 * @subpackage DatabaseTools
 */

namespace MyGED\Core\Database;

use MyGED\Core\Exceptions as AppExceptions;

/**
 * DatabaseTools Class definition
 */
class DatabaseTools
{

    /**
     * getSQLitePDODbObj
     *
     * Returns PDO Object from SQLite filepath
     *
     * @param string $pStrDatabaseFilepath
     * @return \PDO
     * @throws ApplicationException\GenericException
     */
    public static function getSQLitePDODbObj($pStrDatabaseFilepath)
    {
        $lStrDSN = 'sqlite://'.$pStrDatabaseFilepath;

        try {
            $lObjPdoDb = new \PDO($lStrDSN);
        } catch (\Exception $ex) {
            $lArrOptions = array('msg'=>"SQLite db DSN : '".$lStrDSN."'. ExMsg : ".$ex->getMessage());
            throw new AppExceptions\GenericException('PDO_CONNECTION_FAILED', $lArrOptions);
        }

        return $lObjPdoDb;
    }
}
