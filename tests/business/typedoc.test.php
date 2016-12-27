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

use MyGED\Business\TypeDocument as TypeDocument;
use MyGED\Application\Application as App;
use MyGED\Exceptions as AppExceptions;

/**
 * TypeDocumentTest Class testing TypeDocument class.
 *
 * @author polux <polux@poluxfr.org>
 */
class TypeDocumentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * testCreateTypeDocument
     *
     * @test
     *
     * @covers MyGED\Business\TypeDocument::__construct()
     * @covers MyGED\Business\TypeDocument::store()
     * @covers MyGED\Business\TypeDocument::setTitle()
     * @covers MyGED\Business\TypeDocument::setAttributeValue()
     * @covers MyGED\Business\TypeDocument::getId()
     * @covers MyGED\Business\TypeDocument::getTitle()
     */
    public function testCreateTypeDocument()
    {
        $lObjDoc = new TypeDocument();
        $this->assertTrue($lObjDoc instanceof \MyGED\Business\TypeDocument, 'TypeDocument object not valid!');

        $lStrTitre = 'Title TypeDocument #1';
        $lStrDesc  = 'Description TypeDocument #1';
        $lStrCode  = 'tdoc-unit-01';

        $lObjDoc->setTitle($lStrTitre);
        $lObjDoc->setAttributeValue('tdoc_code', $lStrCode);
        $lObjDoc->setAttributeValue('tdoc_desc', $lStrDesc);

        $lObjDoc->store();
        $lStrIdDoc = $lObjDoc->getId();

        // Reload same doc as new Object !
        $lObjDoc2 = new TypeDocument($lStrIdDoc);

        // Title checking!
        $this->assertEquals($lObjDoc2->getTitle(), $lStrTitre, 'Title invalid ! #1');
        $this->assertEquals($lObjDoc->getTitle(), $lStrTitre, 'Title invalid ! #2');
    }

    /**
     * testUpdateNewTier.
     *
     * @depends testCreateTypeDocument
     * @test
     *
     * @covers MyGED\Business\TypeDocument::getAttributeValue()
     */
    public function testUpdateTypeDocument()
    {
        $lObjDoc = new TypeDocument();

        $this->assertTrue($lObjDoc instanceof \MyGED\Business\TypeDocument, 'TypeDocument class object not valid!');

        $lStrTitre = 'TypeDocument #2';
        $lStrCode = 'tdoc-unit-02';

        $lObjDoc->setTitle($lStrTitre);
        $lObjDoc->setAttributeValue('tdoc_code', $lStrCode);

        $lObjDoc->store();
        $lStrIdDoc = $lObjDoc->getId();

        // Reload same doc as new Object !
        $lObjDoc2 = new TypeDocument($lStrIdDoc);
        $lStrNewTitre = 'TypeDocument #2 updated';
        $lStrNewCode = 'tdoc-unit-02-updated';
        $lObjDoc2->setTitle($lStrNewTitre);
        $lObjDoc2->setAttributeValue('tdoc_code', $lStrNewCode);
        $lObjDoc2->store();

        // Reload same doc as new Object !
        $lObjDoc3 = new TypeDocument($lStrIdDoc);

        // Title validating!
        $this->assertEquals($lObjDoc3->getTitle(), $lStrNewTitre, 'Title updated invalid ! #1');
        $this->assertEquals($lObjDoc2->getTitle(), $lStrNewTitre, 'Title not updated invalid ! #2');
        $this->assertEquals($lObjDoc->getTitle(), $lStrTitre, 'Title not updated invalid ! #2');

        // code validating!
        $this->assertEquals($lObjDoc3->getAttributeValue('tdoc_code'), $lStrNewCode, 'Document Code updated invalid ! #1');
        $this->assertEquals($lObjDoc2->getAttributeValue('tdoc_code'), $lStrNewCode, 'Document Code updated invalid ! #2');
        $this->assertEquals($lObjDoc->getAttributeValue('tdoc_code'), $lStrCode, 'Document Code not updated invalid ! #3');
    }

    /**
     * testGetDocById
     *
     * @covers MyGED\Business\Document::getDocById()
     *
     * @test
     */
    public function testGetDocById()
    {
        $lObjDoc = new TypeDocument();

        $this->assertTrue($lObjDoc instanceof \MyGED\Business\TypeDocument, 'Tier class object not valid!');

        $lStrTitre = 'TypeDocument #3';
        $lStrCode = 'tdoc-unit-03';

        $lObjDoc->setTitle($lStrTitre);
        $lObjDoc->setAttributeValue('tdoc_code', $lStrCode);

        $lObjDoc->store();
        $lStrIdDoc = $lObjDoc->getId();

        // Reload same doc as new Object !
        $lObjDoc2 = TypeDocument::getDocById($lStrIdDoc);

        // Title validating!
        $this->assertEquals($lObjDoc2->getTitle(), $lStrTitre, 'Title invalid ! #1');
        $this->assertEquals($lObjDoc->getTitle(), $lStrTitre, 'Title invalid ! #2');

        // code validating!
        $this->assertEquals($lObjDoc2->getAttributeValue('tdoc_code'), $lStrCode, 'Document Code invalid ! #1');
        $this->assertEquals($lObjDoc->getAttributeValue('tdoc_code'), $lStrCode, 'Document Code invalid ! #2');
    }
}
