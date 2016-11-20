<?php

/**
 * Application Class File Definition
 *
 * @package MyGED
 * @subpackage Application
 *
 * @category CoreApplication
 */

namespace MyGED\Core;

use MyGED\Exceptions;
use MyGED\Vault as VaultApplication;
use MyGED\Core\FileSystem\FileSystem as FileFS;

/**
 * Application Class definition
 *
 * @package MyGED
 * @category CoreApplication
 */
class Application
{

    /**
     * JSON Filepath of Application Settings
     *
     * @var string
     * @access private
     */
    private static $_sPathJSONSettingsFile = '/var/www/html/php-myged/conf/mydocs.settings.json';

    /**
     * Application Parameters
     *
     * @var array(mixed) $_aParams
     * @access private
     */
    private static $_aParams = array();

    /**
     * Database PDO Object to Metadata DB.
     *
     * @var \PDO
     * @access private
     */
    private static $_oMetaDatabase = null;

    /**
     * Returns PDO Object about Application Metadata database
     *
     * @return \PDO
     */
    public static function getAppDabaseObject()
    {
        return self::$_oMetaDatabase;
    }

    /**
     * Returns an array containing all settings defined for application.
     *
     * @link conf/mypp.settings.json
     * @return array Array of settings.
     */
    private static function loadAppParamsFromJsonSettingFile()
    {
        $lStrJson     = file_get_contents(self::$_sPathJSONSettingsFile);
        $lArrSettings = json_decode($lStrJson, true);

        if (array_key_exists('db_path', $lArrSettings['settings'])) {
            self::setAppParam('SQLITE_DB_FILEPATH', $lArrSettings['settings']['db_path']);
        } else {
            $lObjException = new ApplicationException('SETTINGS-01');
        }

        if (array_key_exists('vault_path', $lArrSettings['settings'])) {
            self::setAppParam('VAULT_ROOT', $lArrSettings['settings']['vault_path']);
        } else {
            $lObjException = new ApplicationException('SETTINGS-02');
        }

        if (array_key_exists('vault_db', $lArrSettings['settings'])) {
            self::setAppParam('VAULT_DB', $lArrSettings['settings']['vault_db']);
        } else {
            $lObjException = new ApplicationException('SETTINGS-03');
        }

        if (array_key_exists('templates_path', $lArrSettings['settings'])) {
            self::setAppParam('TEMPLATES_ROOT', $lArrSettings['settings']['templates_path']);
        } else {
            $lObjException = new ApplicationException('SETTINGS-04');
        }
    }//end loadAppParamsFromJsonSettingFile()

    /**
     * @deprecated since version 1
     */
    public static function initApplication()
    {
        self::loadAppParamsFromJsonSettingFile();

        // Database init...
        self::initDatabase();

        // Vault init...
        self::initVault();
    }

    /**
     * Database initialisation
     *
     * @throws ApplicationException\GenericException
     */
    public static function initDatabase()
    {
        try {
            // Application DB file does not exists ?
            if (!file_exists(self::getAppParam('SQLITE_DB_FILEPATH'))) {
                // Recreate it from template!
                static::resetApplicationDBFile();
            }
            $lObjPdoDB = \MyGED\Core\Database\DatabaseTools::getSQLitePDODbObj(self::getAppParam('SQLITE_DB_FILEPATH'));
            self::$_oMetaDatabase = $lObjPdoDB;
        } catch (\Exception $ex) {
            $lArrOptions = array('msg'=>"SQLite db DSN : '".$lStrDSN."'. ExMsg : ".$ex->getMessage());
            throw new ApplicationException\GenericException('PDO_CONNECTION_FAILED', $lArrOptions);
        }
    }

     /**
     * Vault initialisation
     *
     * @throws ApplicationException\GenericException
     */
    public static function initVault($pBCreateIfNeeded = false)
    {
        $lStrVaultFilePath = self::getAppParam('VAULT_ROOT');
        VaultApplication\Vault::loadVault($lStrVaultFilePath, $pBCreateIfNeeded);
    }

    /**
     * getAppParam
     *
     * Returns Parameter value. NULL if not defined.
     *
     * @param string $pStrParamIdx Parameter Id
     * @return mixed Value of Parameter (null if not founded)
     */
    public static function getAppParam($pStrParamIdx)
    {
        $lMixedResult = null;
        if (array_key_exists($pStrParamIdx, self::$_aParams)) {
            $lMixedResult =  self::$_aParams[$pStrParamIdx];
        }

        return $lMixedResult;
    }

    /**
     * setAppParam
     *
     * Set Parameter value.
     *
     * @param string $pStrParamIdx Parameter Id
     * @param mixed  $pMixedValue  Value to define
     */
    public static function setAppParam($pStrParamIdx, $pMixedValue)
    {
        self::$_aParams[$pStrParamIdx] = $pMixedValue;
    }

     /**
     * Returns filepath about Application Template DB File
     *
     * @return string Filepath of Application Template DB File
     */
    public static function getTemplateAppDbFilePath()
    {
        return static::getAppParam('TEMPLATES_ROOT').'/app_template.db';
    }

    /**
     * Returns  filepath about Application DB File
     *
     * @return string Filepath of Application DB File
     */
    public static function getAppDbFilePath()
    {
        return static::getAppParam('SQLITE_DB_FILEPATH');
    }

    /**
     * Resets Application DB File
     *
     * @static
     * @throws AppExceptions\GenericException
     */
    public static function resetApplicationDBFile()
    {
        try {
            $lStrRoot = static::getTemplateAppDbFilePath();
            $lStrDest = static::getAppDbFilePath();

            FileFS::filecopy($lStrRoot, $lStrDest);
        } catch (\Exception $e) {
            $lArrOptions = array('msg'=> $e->getMessage());
            throw new ApplicationException\GenericException('INIT_APP_DB_FAILED', $lArrOptions);
        }
    }

    /**
     * Return a temporary filename
     */
    public static function getTemporaryFilename()
    {
    }
}
