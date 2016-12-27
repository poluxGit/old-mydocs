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

namespace MyGED\Tests\Core;

use MyGED\Core\Tasks\Task;
use MyGED\Application\Application;
use MyGED\Exceptions\GenericException;

/**
 * TaskTest Class testing Task class.
 *
 * @author polux <polux@poluxfr.org>
 */
class TaskTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        Application::initApplication(null, true);
    }//end setUp()

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * testAddNewCategorie.
     *
     * @test
     *
     * @covers MyGED\Core\Tasks\Task::store
     * @covers MyGED\Core\Tasks\Task::getId
     * @covers MyGED\Core\Tasks\Task::setupDBConfig()
     * @covers MyGED\Core\Tasks\Task::__construct()
     * @covers MyGED\Core\Tasks\Task::setCreationTimestamp()
     * @covers MyGED\Core\Tasks\Task::setStartTimeStamp()
     * @covers MyGED\Core\Tasks\Task::setStatus()
     * @covers MyGED\Core\Tasks\Task::getCreationTimestamp()
     * @covers MyGED\Core\Tasks\Task::getStatus()
     * @covers MyGED\Core\Tasks\Task::getStartTimeStamp()
     * @covers MyGED\Core\Tasks\Task::getEndTimesTamp()
     */
    public function testCreateNewTask()
    {
        // Task creation!
        $lObjNewTask = new Task();
        $this->assertTrue($lObjNewTask instanceof \MyGED\Core\Tasks\Task, 'Task object not valid!');

        $lObjNewTask->setTitle('Task ID#1.');
        $lObjNewTask->setCreationTimestamp(time());
        $lObjNewTask->setStartTimeStamp(time());
        $lObjNewTask->setStatus('TEST');
        $lObjNewTask->store();

        $lStrIdTask = $lObjNewTask->getId();

        // Reload same task as a new object !
        $lObjTask = new Task($lStrIdTask);

        // Title validating!
        $this->assertEquals($lObjNewTask->getTitle(), $lObjTask->getTitle(), 'Title invalid ! #1');
        $this->assertEquals($lObjNewTask->getStatus(), $lObjTask->getStatus(), 'Status invalid ! #1');
        $this->assertNotEquals($lObjNewTask->getCreationTimestamp(), 0, 'CreationTimestamp invalid ! #1');
        $this->assertNotEquals($lObjNewTask->getStartTimeStamp(), 0, 'StartTimeStamp invalid ! #1');
        $this->assertEquals($lObjNewTask->getEndTimesTamp(), 0, 'EndTimesTamp invalid ! #1');
    }//end testCreateNewTask()

    /**
     * testGetAllClassItem
     *
     * @test
     *
     * @covers MyGED\Core\Tasks\Task::getAllClassItemsData()
     */
    public function testGetAllClassItem()
    {
        $lObjNewTask = new Task();
        $lArrTasks = Task::getAllClassItemsData(null);
        //$this->assertEquals(count($lArrTasks), 1, 'Task object not valid!');
    }//end testGetAllClassItem()
}//end class
