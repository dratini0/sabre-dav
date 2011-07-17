<?php

class Sabre_CardDAV_CardTest extends PHPUnit_Framework_TestCase {

    protected $card;
    protected $backend;

    function setUp() {

        $this->backend = new Sabre_CardDAV_MockBackend();
        $this->card = new Sabre_CardDAV_Card(
            $this->backend,
            array(
                'uri' => 'book1',
                'id' => 'foo',
            ),
            array(
                'uri' => 'card1',
                'addressbookid' => 'foo',
                'carddata' => 'card',
            )
        );

    }

    function testGet() {

        $result = $this->card->get();
        $this->assertEquals('card', stream_get_contents($result));

    }


    /**
     * @depends testGet
     */
    function testPut() {

        $file = fopen('php://memory','r+');
        fwrite($file, 'newdata');
        rewind($file);
        $this->card->put($file);
        $result = $this->card->get();
        $this->assertEquals('newdata', stream_get_contents($result));

    }


    function testDelete() {

        $this->card->delete();
        $this->assertEquals(array(), $this->backend->cards['foo']);

    }

    function testGetContentType() {

        $this->assertEquals('text/x-vcard', $this->card->getContentType());

    }

    function testGetETag() {

        $this->assertEquals(md5('card'), $this->card->getETag());

    }

    function testGetLastModified() {

        $this->assertEquals(null, $this->card->getLastModified());

    }

    function testGetSize() {

        $this->assertEquals(4, $this->card->getSize());

    }
}