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

namespace MyGED\Tests\Process;

use MyGED\Process\Engines\ImportFiles;
use MyGED\Application\Application;
use MyGED\Exceptions\GenericException;

/**
 * ImportFilesTest Class testing ImportFiles class.
 *
 * @author polux <polux@poluxfr.org>
 */
class ImportFilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        //Application::initApplication(null, true);
    }//end setUp()

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * testLaunchImportNonRecursive
     *
     * Launch a non recursive import of files
     *
     * @test
     *
     * @covers MyGED\Process\Engines\ImportFiles::store()
     * @covers MyGED\Process\Engines\ImportFiles::getId()
     */
    public function testLaunchImportNonRecursive()
    {
        // ImportFiles task creation!
        $lObjNewTask = ImportFiles::createNewImportTask();
        $this->assertTrue($lObjNewTask instanceof \MyGED\Process\Engines\ImportFiles, 'ImportFiles Task object not valid!');

        $lObjNewTask->setInputDirectoryPath(__DIR__.'/../tests-ressources/file-01/');
        $lObjNewTask->launchImportDirectory(false);

        // Expected attribute values?
        $this->assertEquals($lObjNewTask->getStepCount(), 1, 'Number of files imported invalid!');
        $this->assertEquals($lObjNewTask->getCurrentStep(), $lObjTask->getStepCount(), 'Not all steps proceed!');
    }//end testLaunchImportNonRecursive()


    /**
     * testLaunchImportNonRecursive
     *
     * Launch a non recursive import of files
     *
     * @test
     *
     * @covers MyGED\Process\Engines\ImportFiles::store()
     * @covers MyGED\Process\Engines\ImportFiles::getId()
     */
    public function testLaunchImportRecursive()
    {
        // ImportFiles task creation!
        $lObjNewTask = ImportFiles::createNewImportTask();
        $this->assertTrue($lObjNewTask instanceof \MyGED\Process\Engines\ImportFiles, 'ImportFiles Task object not valid!');

        $lObjNewTask->setInputDirectoryPath(__DIR__.'/../tests-ressources/file-01/');
        $lObjNewTask->launchImportDirectory(true);

        // Expected attribute values?
        $this->assertEquals($lObjNewTask->getStepCount(), 4, 'Number of files imported invalid!');
        $this->assertEquals($lObjNewTask->getCurrentStep(), $lObjTask->getStepCount(), 'Not all steps proceed!');
    }//end testLaunchImportRecursive()
}//end class
