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

namespace MyGED\Tests\Business;

use MyGED\Business\Categorie as Categorie;
use MyGED\Application\Application as App;

/**
 * CategorieTest Class testing Categorie class.
 *
 * @author polux <polux@poluxfr.org>
 */
class CategorieTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        App::initApplication();
    }

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
     */
    public function testAddNewCategorie()
    {
        $lObjDoc = new Categorie();

        $this->assertTrue($lObjDoc instanceof \MyGED\Business\Categorie, 'Categorie class object not valid!');

        $lStrTitre = 'Cat #1';
        $lStrCode = 'cat-phpunit-test01';

        $lObjDoc->setTitle($lStrTitre);
        $lObjDoc->setAttributeValue('cat_code', $lStrCode);

        $lObjDoc->store();
        $lStrIdDoc = $lObjDoc->getId();

        // Reload same doc as new Object !
        $lObjDoc2 = new Categorie($lStrIdDoc);

        // Title validating!
        $this->assertEquals($lObjDoc2->getTitle(), $lStrTitre, 'Title invalid ! #1');
        $this->assertEquals($lObjDoc->getTitle(), $lStrTitre, 'Title invalid ! #2');
    }

    /**
     * @covers MyGED\Business\Document::getDocById
     *
     * @test
     */
    public function testGetDocById()
    {
        $lObjDoc = new Categorie();

        $this->assertTrue($lObjDoc instanceof \MyGED\Business\Categorie, 'Categorie class object not valid!');

        $lStrTitre = 'Cat #2';
        $lStrCode = 'cat-phpunit-test03';

        $lObjDoc->setTitle($lStrTitre);
        $lObjDoc->setAttributeValue('cat_code', $lStrCode);

        $lObjDoc->store();
        $lStrIdDoc = $lObjDoc->getId();

        // Reload same doc as new Object !
        $lObjDoc2 = Categorie::getDocById($lStrIdDoc);

        // Title validating!
        $this->assertEquals($lObjDoc2->getTitle(), $lStrTitre, 'Title invalid ! #1');
        $this->assertEquals($lObjDoc->getTitle(), $lStrTitre, 'Title invalid ! #2');
    }
}
