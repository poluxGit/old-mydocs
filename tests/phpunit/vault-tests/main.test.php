<?php

/*
 * Copyright (C) 2016 polux
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace MyGED\Testing\Vault;

use MyGED\Vault as Vault;
use MyGED\Application as App;
use MyGED\Core\FileSystem as FsTools;

class VaultTest extends \PHPUnit_Framework_TestCase
{
    private static $_sSubDirectoryPathForVault = '/vault-tmp';
    
    private static $_docidTest = '';

    public static function getNewTmpVaultRootDir()
    {
        // Prepare vault directory
        $lStrCurrentPath = dirname(__FILE__);
        $lStrCompletePath = $lStrCurrentPath.self::$_sSubDirectoryPathForVault;

        return $lStrCompletePath;
    }

    /**
     * Initialization of a Vault for testing.
     *
     * @test
     */
    public static function cleanTmpVaultDir()
    {
        // Prepare vault directory
        $lStrCompletePath = self::getNewTmpVaultRootDir();

        // Cleaning dir if exists
        if (file_exists($lStrCompletePath) && is_dir($lStrCompletePath)) {
            FsTools\FileSystem::deleteDir($lStrCompletePath);
        }

        // Tmp vault root dir creation!
        $lBool_mkdirOK = mkdir($lStrCompletePath, 0777, true);

        if (!$lBool_mkdirOK) {
            throw new \Exception('Creation du tmp vault NOK !');
        }
    }

    /**
     * Initialize Vault From Nothing (empty dir).
     *
     * @covers MyGED\Vault\Vault::loadVault
     * 
     * @test
     */
    public function testLoadVaultFromNothing()
    {
        self::cleanTmpVaultDir();
        App\App::setAppParam('TEMPLATES_ROOT', '/home/polux/Projects/php-myged/application/templates');

        $lStrCompletePath = self::getNewTmpVaultRootDir();

        $this->assertFileExists($lStrCompletePath, 'TMP Vault Dir not exists : '.$lStrCompletePath);

        // Vault init...
        Vault\Vault::loadVault($lStrCompletePath, true);

        $this->assertFileExists($lStrCompletePath.'/files', "Sub directory 'files' doesn't not exists !");
        $this->assertFileExists($lStrCompletePath.'/db', "Sub directory 'files' doesn't exists !");
        $this->assertFileExists($lStrCompletePath.'/db/vault.db', "Sub files 'db/vault.db' doesn't exists !");
    }
    
      /**
     * testLoadVaultOK 
     * 
     * @depends testLoadVaultFromNothing
     * @covers MyGED\Vault\Vault::loadVault
     * @test
     */
    public function testLoadVaultOK()
    {
        $lStrCompletePath = self::getNewTmpVaultRootDir();

        // Vault init...
        Vault\Vault::loadVault($lStrCompletePath, false);
        
        $this->assertTrue(true);
    }

    /**
     * testLoadVaultNOK - LOAD_VAULT_CHECKFS Exception Expected
     * 
     * @depends testLoadVaultOK
     * @covers MyGED\Vault\Vault::loadVault
     * @expectedException MyGED\Core\Exceptions\GenericException
     * @test
     */
    public function testLoadVaultNOK()
    {
        $lStrCompletePath = self::getNewTmpVaultRootDir();

        $this->cleanTmpVaultDir();
        
        // Vault init...
        Vault\Vault::loadVault($lStrCompletePath, false);
    }
    
    /**
     * testGenerateUniqueID
     * 
     * @test
     * @covers MyGED\Vault\Vault::generateUniqueID
     *
     */
    public function testGenerateUniqueID()
    {
        $lBoolCondition = boolval(Vault\Vault::generateUniqueID() != Vault\Vault::generateUniqueID());
        $this->assertTrue($lBoolCondition);
    }
    
    /**
     * testStoreFromFilepath
     * 
     * @covers MyGED\Vault\Vault::storeFromFilepath
     */
    public function testStoreFromFilepath()
    {
        App\App::setAppParam('TEMPLATES_ROOT', '/home/polux/Projects/php-myged/application/templates');
        
        // Vault Tmp Init
        $lStrCompletePath = self::getNewTmpVaultRootDir();
        $this->cleanTmpVaultDir();
        Vault\Vault::loadVault($lStrCompletePath, true);
        
        $lStrUniqueId = Vault\Vault::storeFromFilepath(__FILE__);
        
        self::$_docidTest = $lStrUniqueId;
        $this->assertTrue(true);
    }

    /**
     * testGetFileContentByID
     * 
     * @covers MyGED\Vault\Vault::getFileContentByID
     * @depends testStoreFromFilepath
     * @test
     */
    public function testGetFileContentByID()
    {
        $filename = self::getNewTmpVaultRootDir().'/filetest.tmp';
        $lStrContent = Vault\Vault::getFileContentByID(self::$_docidTest);
        
        file_put_contents($filename, $lStrContent);
        
        $this->assertFileEquals($filename, __FILE__);
    }

    /**
     * testGetFilePathByID
     * 
     * @covers MyGED\Vault\Vault::getFilePathByID
     * @depends testStoreFromFilepath
     * @test
     */
    public function testGetFilePathByID()
    {
        $lStrPath = Vault\Vault::getFilePathByID(self::$_docidTest);
        $this->assertFileEquals($lStrPath, __FILE__);
    }

    /**
     * testGetFileOriginalNameByID
     * 
     * @covers MyGED\Vault\Vault::getFileOriginalNameByID
     * @depends testStoreFromFilepath
     * @test
     */
    public function testGetFileOriginalNameByID()
    {
        $lStrOName = Vault\Vault::getFileOriginalNameByID(self::$_docidTest);
        $this->assertEquals($lStrOName, basename(__FILE__));
    }

    /**
     * testGetFileOriginalNameByID
     * 
     * @covers MyGED\Vault\Vault::getDatabaseFilePath()
     * @depends testStoreFromFilepath
     * @test
     */
    public function testGetDatabaseFilePath()
    {
        $lStrOName = Vault\Vault::getDatabaseFilePath();
        $this->assertEquals($lStrOName, $filename = self::getNewTmpVaultRootDir().'/db/vault.db');
    }

    /**
     * testGetTemplateVaultDBFilePath
     * 
     * @covers MyGED\Vault\Vault::getTemplateVaultDBFilePath
     * @depends testLoadVaultFromNothing
     * @test
     */
    public function testGetTemplateVaultDBFilePath()
    {
        // Vault Tmp Init
        $lStrCompletePath = self::getNewTmpVaultRootDir();
        $this->cleanTmpVaultDir();
        Vault\Vault::loadVault($lStrCompletePath, true);
        
        $lStrOName = Vault\Vault::getTemplateVaultDBFilePath();
        $this->assertNotEquals(self::getNewTmpVaultRootDir().'/db/vault.db', $lStrOName);
        
        $this->assertFileEquals($lStrOName, self::getNewTmpVaultRootDir().'/db/vault.db','DB Files not equals!');
    }

    /**
     * testGetVaultRootDir
     * 
     * @covers MyGED\Vault\Vault::getVaultRootDir
     * @depends testGetTemplateVaultDBFilePath
     * @test     
     */
    public function testGetVaultRootDir()
    {
       $lStrOName = Vault\Vault::getVaultRootDir();
       $this->assertEquals(self::getNewTmpVaultRootDir(), $lStrOName);
    }

    /**
     * testGetPDOVaultDBObject
     * 
     * @covers MyGED\Vault\Vault::getPDOVaultDBObject
     * @depends testGetTemplateVaultDBFilePath
     */
    public function testGetPDOVaultDBObject()
    {
        $this->assertTrue(Vault\Vault::getPDOVaultDBObject() instanceof \PDO);
    }
    
    /**
     * tearDownAfterClass
     * 
     * Process after tests overs.
     */
    public static function tearDownAfterClass()
    {
        self::cleanTmpVaultDir();
    }
}
