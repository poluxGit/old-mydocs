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

namespace MyGED\Testing\Application;

use MyGED\Business\Tier as Tier;
use MyGED\Application\App as App;
use MyGED\Core\Exceptions as AppExceptions;

/**
 * DocumentTest Class testing Document class.
 *
 * @author polux <polux@poluxfr.org>
 */
class TierTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        App::setAppParam('TEMPLATES_ROOT', '/home/polux/Projects/php-myged/application/templates');
        App::setAppParam('SQLITE_DB_FILEPATH', '/home/polux/Projects/php-myged/data/app.db');
        App::setAppParam('VAULT_ROOT', '/home/polux/Projects/php-myged/data/vault');

        App::resetApplicationDBFile();
        
        // Database init...
        App::initDatabase();

        // Vault init...
        App::initVault();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * testAddNewTier.
     *
     * @test
     */
    public function testAddNewTier()
    {
        $lObjDoc = new Tier();

        $this->assertTrue($lObjDoc instanceof \MyGED\Business\Tier, 'Tier class object not valid!');

        $lStrTitre = 'Tier #1';
        $lStrCode = 'tie-phpunit-test01';

        $lObjDoc->setTitle($lStrTitre);
        $lObjDoc->setAttributeValue('tier_code', $lStrCode);

        $lObjDoc->store();
        $lStrIdDoc = $lObjDoc->getId();

        // Reload same doc as new Object !
        $lObjDoc2 = new Tier($lStrIdDoc);

        // Title validating!
        $this->assertEquals($lObjDoc2->getTitle(), $lStrTitre, 'Title invalid ! #1');
        $this->assertEquals($lObjDoc->getTitle(), $lStrTitre, 'Title invalid ! #2');

       
    }

    /**
     * testUpdateNewTier.
     *
     * @depends testAddNewTier
     * @test
     */
    public function testUpdateNewTier()
    {
        $lObjDoc = new Tier();

        $this->assertTrue($lObjDoc instanceof \MyGED\Business\Tier, 'Tier class object not valid!');

        $lStrTitre = 'Tier #2';
        $lStrCode = 'tie-phpunit-test02';

        $lObjDoc->setTitle($lStrTitre);
        $lObjDoc->setAttributeValue('tier_code', $lStrCode);

        $lObjDoc->store();
        $lStrIdDoc = $lObjDoc->getId();

        // Reload same doc as new Object !
        $lObjDoc2 = new Tier($lStrIdDoc);

        $lStrNewTitre = 'Tier #2 updated';
        $lStrNewCode = 'tie-phpunit-test02-updated';
        $lObjDoc2->setTitle($lStrNewTitre);
        $lObjDoc2->setAttributeValue('tier_code', $lStrNewCode);
        $lObjDoc2->store();

        // Reload same doc as new Object !
        $lObjDoc3 = new Tier($lStrIdDoc);

        // Title validating!
        $this->assertEquals($lObjDoc3->getTitle(), $lStrNewTitre, 'Title updated invalid ! #1');
        $this->assertEquals($lObjDoc2->getTitle(), $lStrNewTitre, 'Title not updated invalid ! #2');
        $this->assertEquals($lObjDoc->getTitle(), $lStrTitre, 'Title not updated invalid ! #2');

        // code validating!
        $this->assertEquals($lObjDoc3->getAttributeValue('tier_code'), $lStrNewCode, 'Document Code updated invalid ! #1');
        $this->assertEquals($lObjDoc2->getAttributeValue('tier_code'), $lStrNewCode, 'Document Code updated invalid ! #2');
        $this->assertEquals($lObjDoc->getAttributeValue('tier_code'), $lStrCode, 'Document Code not updated invalid ! #3');
    }

    /**
     * @covers MyGED\Business\Document::getDocById
     *
     * @todo   Implement testGetDocById()
     */
    public function testGetDocById()
    {
        $lObjDoc = new Tier();

        $this->assertTrue($lObjDoc instanceof \MyGED\Business\Tier, 'Tier class object not valid!');

        $lStrTitre = 'Tier #2';
        $lStrCode = 'tie-phpunit-test03';

        $lObjDoc->setTitle($lStrTitre);
        $lObjDoc->setAttributeValue('tier_code', $lStrCode);

        $lObjDoc->store();
        $lStrIdDoc = $lObjDoc->getId();

        // Reload same doc as new Object !
        $lObjDoc2 = Tier::getDocById($lStrIdDoc);

        // Title validating!
        $this->assertEquals($lObjDoc2->getTitle(), $lStrTitre, 'Title invalid ! #1');
        $this->assertEquals($lObjDoc->getTitle(), $lStrTitre, 'Title invalid ! #2');

        // code validating!
        $this->assertEquals($lObjDoc2->getAttributeValue('tier_code'), $lStrCode, 'Document Code invalid ! #1');
        $this->assertEquals($lObjDoc->getAttributeValue('tier_code'), $lStrCode, 'Document Code invalid ! #2');
    }
}
