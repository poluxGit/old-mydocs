<?php

/**
 * VaultFs Class File Definition
 *
 * @author polux <polux@poluxfr.org>
 *
 * @package MyGED
 * @subpackage Vault
 */

namespace MyGED\Vault;

use MyGED\Core\Exceptions as AppExceptions;
use MyGED\Core\FileSystem\FileSystem as FileFS;

/**
 * VaulFs Class Definition
 *
 * Class managing Filesystem storage.
 */
class VaultFs {

    /**
     * Mandatory folders for Vault internal deployment
     *
     * @var array(string)
     * @static
     */
    protected static $_aMandatoryDirectories = array('','/files','/db');

    /**
     * Mandatory files for Vault internal deployment
     *
     * @var array(string)
     * @static
     */
    protected static $_aMandatoryFiles = array('/db/vault.db');

    /**
     * Returns TRUE if mandatory files and directories are reacheable.
     *
     * @param string $pStrPathToCheck Path to check
     * @todo Intégrer la gestion par paramètre du VAULT_DB et des répertoires/fichiers à vérifier
     *
     * @return boolean
     */
    public static function isValidVault($pStrPathToCheck)
    {
        $lStrValue1 = VaultFs::checkVaultDirectories($pStrPathToCheck);
        $lStrValue2 = VaultFs::checkVaultConfigFiles($pStrPathToCheck);

        $lBoolDirs  = is_bool($lStrValue1);
        $lBoolFiles = is_bool($lStrValue2);

        return ($lBoolDirs && $lBoolFiles);
    }//end isValidVault()

    /**
     * Reset Vault DB File from empty template vault DB file
     *
     * @param type $pStrVaultDbFilePath Filepath to vault db file.
     * @param type $pBoolForceReset     Flag forcing copy of empty vault_template_db
     *
     * @throws \MyGED\Core\Exceptions\GenericException
     */
    public static function resetVaultDBFile()
    {
        try {
            $lStrRoot = Vault::getTemplateVaultDBFilePath();
            $lStrDest = Vault::getDatabaseFilePath();

            FileFS::filecopy($lStrRoot, $lStrDest);
        }
        catch (\Exception $e)
        {
            $lArrOptions = array('msg'=> $e->getMessage());
            throw new AppExceptions\GenericException('INIT_VAULT_DB_FAILED',$lArrOptions);
        }
    }//end resetVaultDBFile()


    /**
     * Repairs all needed.
     */
    public static function repairVaultDirectoriesAndFiles()
    {
        $lStrVaultRootDir = Vault::getVaultRootDir();
        if(!VaultFs::isValidVault($lStrVaultRootDir))
        {
            VaultFS::repairVaultDirectories($lStrVaultRootDir);
            VaultFs::repairVaultFiles();
        }
    }

    /**
     * Checking Vault directories validity.
     *
     * @param type $pStrVaultPathToCheck
     *
     * @return boolean  TRUE if ok - Directory which is missing instead
     */
    protected static function checkVaultDirectories($pStrVaultPathToCheck=null)
    {
        $lStrVaultPathToCheck = $pStrVaultPathToCheck;
        if(is_null($pStrVaultPathToCheck))
        {
            $lStrVaultPathToCheck = Vault::getVaultRootDir();
        }

        // Check if mandatory dirs exists and reachable ?
        foreach( VaultFs::$_aMandatoryDirectories as $lStrDirPath)
        {
            try{
                VaultFs::checkIfPathIsReachable($lStrVaultPathToCheck.$lStrDirPath,true);
            } catch (AppExceptions\GenericException $ex) {
                return $lStrVaultPathToCheck.$lStrDirPath;
            }
        }

        return true;
    }//end checkVaultDirectories()

     /**
     * Checking Vault configuration files validity.
     *
     * @param type $pStrVaultPathToCheck
     *
     * @return boolean TRUE if ok - File which is missing instead
     */
    protected static function checkVaultConfigFiles($pStrVaultPathToCheck=null)
    {
        $lStrVaultPathToCheck = $pStrVaultPathToCheck;
        if(is_null($pStrVaultPathToCheck))
        {
            $lStrVaultPathToCheck = Vault::getVaultRootDir();
        }

        // Check if mandatory dirs exists and reachable ?
        foreach( VaultFs::$_aMandatoryFiles as $lStrDirPath)
        {
           if(!file_exists($lStrVaultPathToCheck.$lStrDirPath))
           {
               return $lStrVaultPathToCheck.$lStrDirPath;
           }
        }

        return true;
    }//end checkVaultConfigFiles()

    /**
     * Repairs Vault Directories structure if needed.
     *
     * @static
     *
     * @param string $pStrVaultRootPath Vault root path (if not specified Vault::getVaultRootDir() used.
     */
    protected static function repairVaultDirectories($pStrVaultRootPath=null)
    {
        $lStrVaultPathToCheck = $pStrVaultRootPath;
        if(is_null($pStrVaultRootPath))
        {
            $lStrVaultPathToCheck = Vault::getVaultRootDir();
        }

        $liCpt = 0;

        $lBoolContinue = true;
        while($lBoolContinue === true && $liCpt <= count(VaultFs::$_aMandatoryDirectories))
        {
            $lStrDirToCreate = VaultFs::checkVaultDirectories($pStrVaultRootPath);
            if(!is_bool($lStrDirToCreate))
            {
                mkdir($lStrDirToCreate,0777,true);
            }
            else
            {
                $lBoolContinue = false;
            }
            $liCpt++;
        }
    }

    protected static function repairVaultFiles()
    {
        VaultFs::resetVaultDBFile();
    }


    /**
     * Returns content of aimed file
     *
     * @param string $pStrFilepath Filepath to return.
     * @static
     */
    public static function getFileContentByFilepath($pStrFilepath)
    {
        return file_get_contents($pStrFilepath);
    }//end getFileContentByID()


    /**
     * Store a file into Vault
     *
     * @param string $pStrIdObject      UniqueId of file
     * @param mixed  $pMixedContent     Content to store
     * @param string $pStrFileExtension File extension
     *
     * @return boolean Storage OK ?
     */
    public static function storeFileContent($pStrIdObject,$pMixedContent,$pStrFileExtension)
    {
        $lStrOutFilepath = Vault::getVaultRootDir().'/files/'.$pStrIdObject.'.'.$pStrFileExtension;

        // Target file exists ?
        if(file_exists($lStrOutFilepath))
        {
            $lArrOptions = array('msg'=> 'Error during check before storage of a file into Vault => targetfile already exists : '.$lStrOutFilepath);
            throw new AppExceptions\GenericException('VAULT_FS_STORE_TARGETFILE_EXISTS',$lArrOptions);
        }

        // Store content into new file into Vault filesystem
        try {
            file_put_contents($lStrOutFilepath,$pMixedContent);
        } catch (\Exception $ex) {
            $lArrOptions = array('msg'=> sprintf('Error during storage content of a file into Vault. (target file : "%s") - Error : %s ',$lStrOutFilepath,$ex->getMessage()));
            throw new AppExceptions\GenericException('VAULT_FS_STORE_CONTENT',$lArrOptions);
        }

        return $lStrOutFilepath;
    }//end storeFileContent()


    /**
     * * Store a file from existing file
     *
     * @param string $pStrIdObject UniqueID of file
     * @param string $pStrFilePath Path of source file to store.
     *
     * @throws AppExceptions\GenericException
     * @throws \Exception
     *
     * @return string Filepath of storage
     */
    public static function storeFromFilepath($pStrIdObject,$pStrFilePath)
    {
        // Source file ok ?
        if(empty($pStrFilePath) || !file_exists($pStrFilePath))
        {
            $lArrOptions = array('msg'=> 'Error during storing a new file into Vault => source file not reacheable : '.$pStrFilePath);
            throw new AppExceptions\GenericException('VAULT_FS_STORE_SOURCEFILE_NOK',$lArrOptions);
        }
        $lArrPathInfo    = pathinfo($pStrFilePath);
        $lStrUniqueIdDoc = $pStrIdObject;
        $lStrNewDocName  = $lStrUniqueIdDoc.'.'.$lArrPathInfo['extension'];

        // Copy file !
        try {
            $lStrRoot = $pStrFilePath;$lStrDest = Vault::getVaultRootDir().'/files/'.$lStrNewDocName;
            $lBoolResult = copy($lStrRoot, $lStrDest);
            if(!$lBoolResult)
            {
                throw new \Exception(sprintf("Error during storing a new file into Vault (copying source to vault): source: %s => target: %s.",$lStrRoot,$lStrDest));
            }

        } catch (\Exception $ex) {
            $lArrOptions = array('msg'=> $ex->getMessage());
            throw new AppExceptions\GenericException('VAULT_FS_STORE_SOURCEFILE_NOK',$lArrOptions);
        }
        return $lStrDest;
    }


    /**
     * checkIfPathIsReachable
     *
     * Returns TRUE if path to ckeck is reachable and a valid directory.
     * Check also if writing rights are set.
     *
     * @param string $pStrFilePath Complete Filepath to check.
     * @param boolean $pBoolRaiseException Raise n exception if checks not satisfied
     * @return boolean
     * @throws AppExceptions\GenericException
     */
    public static function checkIfPathIsReachable($pStrFilePath,$pBoolRaiseException=false)
    {
        return VaultFs::checkIfPathIsEmpty($pStrFilePath,$pBoolRaiseException) && VaultFs::checkIfPathExistsAnIsDir($pStrFilePath,$pBoolRaiseException) && VaultFs::checkIfPathIsReadable($pStrFilePath,$pBoolRaiseException);
    }

    /**
     * checkIfPathIsEmpty
     *
     * Returns TRUE if path to ckeck is reachable and a valid directory.
     * Check also if writing rights are set.
     *
     * @param string $pStrFilePath Complete Filepath to check.
     * @param boolean $pBoolRaiseException Raise n exception if checks not satisfied
     * @return boolean
     * @throws AppExceptions\GenericException
     */
    private static function checkIfPathIsEmpty($pStrFilePath,$pBoolRaiseException=false)
    {
        $lBoolCheck = true;

        // Parameter not empty
        if(empty($pStrFilePath)){
            $lBoolCheck = false;
            if($pBoolRaiseException)
            {
                $lArrOptions = array('msg'=>"Path empty.");
                throw new AppExceptions\GenericException("VAULT_CHK_EMPTY_FILEPATH",$lArrOptions);
            }
        }

        return $lBoolCheck;
    }

     /**
     * checkIfPathExistsAnIsDir
     *
     * Returns TRUE if path to ckeck is reachable and a valid directory.
     * Check also if writing rights are set.
     *
     * @param string $pStrFilePath Complete Filepath to check.
     * @param boolean $pBoolRaiseException Raise n exception if checks not satisfied
     * @return boolean
     * @throws AppExceptions\GenericException
     */
    private static function checkIfPathExistsAnIsDir($pStrFilePath,$pBoolRaiseException=false)
    {
        $lBoolCheck = true;

        if(!file_exists($pStrFilePath) || !is_dir($pStrFilePath)) {
            $lBoolCheck = false;
            if($pBoolRaiseException)
            {
                $lArrOptions = array('msg'=>"Path '".$pStrFilePath."' not exists or not a dir.");
                throw new AppExceptions\GenericException("VAULT_CHK_NOT_A_DIRECTORY",$lArrOptions);
            }
        }

        return $lBoolCheck;
    }

     /**
     * checkIfPathIsReadable
     *
     * Returns TRUE if path to ckeck is reachable and a valid directory.
     * Check also if writing rights are set.
     *
     * @param string $pStrFilePath Complete Filepath to check.
     * @param boolean $pBoolRaiseException Raise n exception if checks not satisfied
     * @return boolean
     * @throws AppExceptions\GenericException
     */
    private static function checkIfPathIsReadable($pStrFilePath,$pBoolRaiseException=false)
    {
        $lBoolCheck = true;

        // Is readable ?
        if(!is_readable($pStrFilePath)) {
            $lBoolCheck = false;
            if($pBoolRaiseException)
            {
                $lArrOptions = array('msg'=>"Path '".$pStrFilePath."' can't be read..");
                throw new AppExceptions\GenericException("VAULT_CHK_PATH_NOTREADABLE",$lArrOptions);
            }
        }

        return $lBoolCheck;
    }
}
