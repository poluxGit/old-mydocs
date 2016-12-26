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

use MyGED\Business\Document as Document;
use MyGED\Application\Application as App;

/**
 * DocumentTest Class testing Document class
 *
 * @author polux <polux@poluxfr.org>
 */
class DocumentTest extends \PHPUnit_Framework_TestCase
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
     * testAddNewDocument
     *
     * @test
     */
    public function testAddNewDocument()
    {
        $lObjDoc = new Document();

        $this->assertTrue($lObjDoc instanceof \MyGED\Business\Document, 'Document class object not valid!');

        $lStrTitre = 'Titre docuement phpunit';
        $lStrCode  = 'doc-phpunit-test53';

        $lObjDoc->setTitle($lStrTitre);
        $lObjDoc->setAttributeValue('doc_code', $lStrCode);

        $lObjDoc->store();
        $lStrIdDoc = $lObjDoc->getId();

        // Reload same doc as new Object !
        $lObjDoc2 = new Document($lStrIdDoc);

        // Title validating!
        $this->assertEquals($lObjDoc2->getTitle(), $lStrTitre, 'Title invalid ! #1');
        $this->assertEquals($lObjDoc->getTitle(), $lStrTitre, 'Title invalid ! #2');

//        // code validating!
//        $this->assertEquals($lObjDoc2->getAttributeValue('doc_code'), $lStrCode, 'Document Code invalid ! #1');
//        $this->assertEquals($lObjDoc->getAttributeValue('doc_code'), $lStrCode, 'Document Code invalid ! #2');
    }


    /**
     * testUpdateNewDocument
     *
     * @depends testAddNewDocument
     * @test
     */
    public function testUpdateNewDocument()
    {
        $lObjDoc = new Document();

        $this->assertTrue($lObjDoc instanceof \MyGED\Business\Document, 'Document class object not valid!');

        $lStrTitre = 'Titre document phpunit Pas Mis à jour';
        $lStrCode  = 'doc-phpunit-test58';

        $lObjDoc->setTitle($lStrTitre);
        $lObjDoc->setAttributeValue('doc_code', $lStrCode);

        $lObjDoc->store();
        $lStrIdDoc = $lObjDoc->getId();

        // Reload same doc as new Object !
        $lObjDoc2 = new Document($lStrIdDoc);

        $lStrNewTitre = 'Titre document phpunit Mis à jour #2';
        $lStrNewCode  = 'doc-phpunit-test85-updated';
        $lObjDoc2->setTitle($lStrNewTitre);
        $lObjDoc2->setAttributeValue('doc_code', $lStrNewCode);
        $lObjDoc2->store();

        // Reload same doc as new Object !
        $lObjDoc3 = new Document($lStrIdDoc);

        // Title validating!
        $this->assertEquals($lObjDoc3->getTitle(), $lStrNewTitre, 'Title updated invalid ! #1');
        $this->assertEquals($lObjDoc2->getTitle(), $lStrNewTitre, 'Title not updated invalid ! #2');
        $this->assertEquals($lObjDoc->getTitle(), $lStrTitre, 'Title not updated invalid ! #2');
    }



    /**
     * @covers MyGED\Business\Document::getDocById
     * @test
     */
    public function testGetDocById()
    {
        $lObjDoc = new Document();

        $this->assertTrue($lObjDoc instanceof \MyGED\Business\Document, 'Document class object not valid!');

        $lStrTitre = 'Titre docuement phpunit';
        $lStrCode  = 'doc-phpunit-test789';

        $lObjDoc->setTitle($lStrTitre);
        $lObjDoc->setAttributeValue('doc_code', $lStrCode);

        $lObjDoc->store();
        $lStrIdDoc = $lObjDoc->getId();

        // Reload same doc as new Object !
        $lObjDoc2 = Document::getDocById($lStrIdDoc);

        // Title validating!
        $this->assertEquals($lObjDoc2->getTitle(), $lStrTitre, 'Title invalid ! #1');
        $this->assertEquals($lObjDoc->getTitle(), $lStrTitre, 'Title invalid ! #2');

        // code validating!
        $this->assertEquals($lObjDoc2->getAttributeValue('doc_code'), $lStrCode, 'Document Code invalid ! #1');
        $this->assertEquals($lObjDoc->getAttributeValue('doc_code'), $lStrCode, 'Document Code invalid ! #2');
    }
}
