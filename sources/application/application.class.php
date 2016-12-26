<?php
/**
 * Application Class File Definition
 *
 * @package MyGED
 * @subpackage Application
 *
 * @category CoreApplication
 */

namespace MyGED\Application;

use MyGED\Vault as VaultApplication;
use MyGED\Core\FileSystem\FileSystem as FileFS;
use MyGED\Exceptions as ApplicationException;

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
    private static $_sPathJSONSettingsFile = __DIR__.'/../../conf/mydocs.settings.json';

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
    }//end getAppDabaseObject()

    /**
     * Returns an array containing all settings defined for application.
     *
     * @link conf/mypp.settings.json
     * @return array Array of settings.
     */
    private static function loadAppParamsFromJsonSettingFile($pStrConfigJSONFilePath=null)
    {
        $lstrFileToLoad = self::$_sPathJSONSettingsFile;
        if (!empty($pStrConfigJSONFilePath)) {
            if (file_exists($pStrConfigJSONFilePath)) {
                $lstrFileToLoad = $pStrConfigJSONFilePath;
            } else {
                throw new ApplicationException(
                  'APP-PARAM-FILE-NOT-FOUND',
                  array(
                    'msg' => sprintf(
                      "JSON settings file '%s' not found!",
                      $pStrConfigJSONFilePath)
                  )
                );
            }
        }

        $lStrJson     = file_get_contents($lstrFileToLoad);
        $lArrSettings = json_decode($lStrJson, true);

        if (array_key_exists('db_path', $lArrSettings['settings'])) {
            self::setAppParam('SQLITE_DB_FILEPATH', $lArrSettings['settings']['db_path']);
        } else {
            throw new ApplicationException('APP-PARAM-NOT-FOUND_DB-FILE',
              array(
                'msg'=> sprintf(
                  "Setting '%s' must be specified into '%s' file.",
                  'SQLITE_DB_FILEPATH',
                  $lstrFileToLoad)
              )
            );
        }

        if (array_key_exists('vault_ocr', $lArrSettings['settings'])) {
            self::setAppParam('VAULT_OCR_DIR', $lArrSettings['settings']['vault_ocr']);
        } else {
            throw new ApplicationException('APP-PARAM-NOT_FOUND-VAULT_OCR_DIR',
              array(
                'msg'=> sprintf(
                  "Setting '%s' must be specified into '%s' file.",
                  'vault_ocr',
                  $lstrFileToLoad)
              )
            );
        }


        if (array_key_exists('vault_path', $lArrSettings['settings'])) {
            self::setAppParam('VAULT_ROOT', $lArrSettings['settings']['vault_path']);
        } else {
            throw new ApplicationException('APP-PARAM-NOT-FOUND_VAULT-ROOT',
              array(
                'msg'=> sprintf(
                  "Setting '%s' must be specified into '%s' file.",
                  'VAULT_ROOT',
                  $lstrFileToLoad)
              )
            );
        }

        if (array_key_exists('vault_db', $lArrSettings['settings'])) {
            self::setAppParam('VAULT_DB', $lArrSettings['settings']['vault_db']);
        } else {
            throw new ApplicationException('APP-PARAM-NOT-FOUND_VAULT-DB',
            array(
              'msg'=> sprintf(
                "Setting '%s' must be specified into '%s' file.",
                'VAULT_DB',
                $lstrFileToLoad)
            )
          );
        }

        if (array_key_exists('templates_path', $lArrSettings['settings'])) {
            self::setAppParam('TEMPLATES_ROOT', $lArrSettings['settings']['templates_path']);
        } else {
            throw new ApplicationException('APP-PARAM-NOT-FOUND_TEMPLATES-ROOT',
            array(
              'msg'=> sprintf(
                "Setting '%s' must be specified into '%s' file.",
                'TEMPLATES_ROOT',
                $lstrFileToLoad)
            )
          );
        }
    }//end loadAppParamsFromJsonSettingFile()

    /**
     * @deprecated since version 1
     */
    public static function initApplication($pStrApplicationSettingsJsonFilepath=null)
    {
        self::loadAppParamsFromJsonSettingFile($pStrApplicationSettingsJsonFilepath);

        // Database init...
        self::initDatabase();

        // Vault init...
        self::initVault();
    }//end initApplication()

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
            $lArrOptions = array('msg'=>"SQLite db DSN : '".self::getAppParam('SQLITE_DB_FILEPATH')."'. ExMsg : ".$ex->getMessage());
            throw new ApplicationException\GenericException('PDO_CONNECTION_FAILED', $lArrOptions);
        }
    }//end initDatabase()

     /**
     * Vault initialisation
     *
     * @throws ApplicationException\GenericException
     */
    public static function initVault($pBCreateIfNeeded = false)
    {
        $lStrVaultFilePath = self::getAppParam('VAULT_ROOT');
        $lStrVaultOCRDir = self::getAppParam('VAULT_OCR_DIR');
        VaultApplication\Vault::loadVault($lStrVaultFilePath, $pBCreateIfNeeded);
        VaultApplication\Vault::setVaultOCRDirectory($lStrVaultOCRDir);
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
        return static::getAppParam('TEMPLATES_ROOT').'app.db';
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
}
