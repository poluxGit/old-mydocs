<?php

/**
 * Vault Class File Definition
 *
 * @author polux <polux@poluxfr.org>
 *
 * @package MyGED
 * @subpackage Vault
 */

namespace MyGED\Vault;

use MyGED\Core\Exceptions as AppExceptions;
use MyGED\Core\Database as AppDb;
use MyGED\Application as App;

/**
 * Vaul Class Definition
 *
 * Class managing storing solution.
 */
class Vault {

    /**
     * Vault Root Path
     *
     * @var string
     * @access protected
     * @static
     */
    protected static $_sVaultPath = null;

    /**
     * Vault DB filepath
     *
     * @var \PDO
     * @access protected
     * @static
     */
    protected static $_sVaultDBFilepath = null;

     /**
     * Vault DB Pdo Object
     *
     * @var \PDO
     * @access protected
     * @static
     */
    protected static $_oPdoVaultDB = null;

    /**
     * Define Vault Root Path Property
     *
     * @param type $pStrVaultPath
     *
     * @static
     */
    private static function setVaultPath($pStrVaultPath)
    {
        self::$_sVaultPath = $pStrVaultPath;
    }

    /**
     * Load Vault Directory
     *
     * @param string  $pStrVaultPath    Path to manage.
     * @param boolean $pBoolReset       Force reset of vault (db)
     *
     * @throws \MyGED\Core\Exceptions\GenericException
     */
    public static function loadVault($pStrVaultPath, $pBoolReset=false)
    {
        self::setVaultPath($pStrVaultPath);
        // Check Filesystem !
        if(!VaultFs::isValidVault($pStrVaultPath))
        {
            if(!$pBoolReset)
            {
                $lArrOptions = array('msg'=> sprintf('Vault is not valid (path: %s)',$pStrVaultPath));
                throw new AppExceptions\GenericException('LOAD_VAULT_CHECKFS',$lArrOptions);
            }
            else
            {
                VaultFs::repairVaultDirectoriesAndFiles();
            }
        }

        // Check Database !
        if(!file_exists(self::getDatabaseFilePath()))
        {
            if(!$pBoolReset)
            {
                $lArrOptions = array('msg'=> sprintf('VaultDb is not valid (path: %s)',self::getDatabaseFilePath()));
                throw new AppExceptions\GenericException('LOAD_VAULT_CHECKDB',$lArrOptions);
            }
            else
            {
                self::setVaultPath($pStrVaultPath);
                VaultFs::repairVaultDirectoriesAndFiles();
            }
        }

        self::setVaultPath($pStrVaultPath);
        self::$_sVaultDBFilepath = self::getDatabaseFilePath();
    }//end loadVault()

    /**
     * Generate Unique id
     *
     * @static
     * @return string
     */
    public static function generateUniqueID($pStrPrefix='doc-')
    {
       return uniqid($pStrPrefix);
    }

    /**
     * Returns File Content from her Uid
     *
     * @param type $pStrUniqueID Unique id of file
     *
     * @return mixed Content of the file.
     */
    public static function getFileContentByID($pStrUniqueID)
    {
        $lStrFilePath = VaultDb::getFilePath($pStrUniqueID);
        return VaultFs::getFileContentByFilepath($lStrFilePath);
    }

    /**
     *
     * @param type $pStrUniqueID
     */
    public static function getFilePathByID($pStrUniqueID)
    {
        return VaultDb::getFilePath($pStrUniqueID);
    }

     /**
     *
     * @param type $pStrUniqueID
     */
    public static function getFileOriginalNameByID($pStrUniqueID)
    {
        return VaultDb::getFileOriginalName($pStrUniqueID);
    }

    public static function getDatabaseFilePath()
    {
        return self::$_sVaultPath.'/db/vault.db';
    }

    public static function getTemplateVaultDBFilePath()
    {
        return App\App::getAppParam('TEMPLATES_ROOT').'/vault_template.db';
    }


    public static function getVaultRootDir()
    {
        return self::$_sVaultPath;
    }

    public static function storeFromContent($pMixedContent,$pStrOriginalFilename='',$pStrFileTypeMime='')
    {
        $lStrUniqueIdDoc = self::generateUniqueID('fic-');

        try {

            $lStrExtensionFile = substr($pStrOriginalFilename,stripos($pStrOriginalFilename,'.')+1);
            $lStrFilePath = VaultFs::storeFileContent($lStrUniqueIdDoc, $pMixedContent,$lStrExtensionFile);
            VaultDb::insertNewFile($lStrUniqueIdDoc, basename($pStrOriginalFilename), $lStrFilePath,$pStrFileTypeMime);
        }
        catch(\Exception $ex)
        {
            $lArrOptions = array('msg'=> sprintf('Error saving a file into the Vault (filepath to store: %s) | Error : %s',$lStrFilePath,$ex->getMessage()));
            throw new AppExceptions\GenericException('LOAD_VAULT_CHECKDB',$lArrOptions);
        }
        return $lStrUniqueIdDoc;
    }

    /**
     * Store a file from existing file
     *
     * @param type $pStrFilePath Path of file to store.
     *
     * @return string Id Unique Doc
     */
    public static function storeFromFilepath($pStrFilePath)
    {
        $lStrUniqueIdDoc = self::generateUniqueID();

        try {
            $lStrFilePath = VaultFs::storeFromFilepath($lStrUniqueIdDoc, $pStrFilePath);
            VaultDb::insertNewFile($lStrUniqueIdDoc, basename($pStrFilePath), $lStrFilePath);
        }
        catch(\Exception $ex)
        {
            $lArrOptions = array('msg'=> sprintf('Error saving a file into the Vault (filepath to store: %s) | Error : %s',$pStrFilePath,$ex->getMessage()));
            throw new AppExceptions\GenericException('LOAD_VAULT_CHECKDB',$lArrOptions);
        }
        return $lStrUniqueIdDoc;
    }

    /**
     * Returns Vault PDO Database Object
     *
     * @static
     * @access protected
     * @return \PDO
     */
    public static function getPDOVaultDBObject()
    {
        if(is_null(self::$_oPdoVaultDB) || !(self::$_oPdoVaultDB instanceof \PDO))
        {
            self::$_oPdoVaultDB = AppDb\DatabaseTools::getSQLitePDODbObj(self::getDatabaseFilePath());
        }
        return self::$_oPdoVaultDB;
    }
}
