<?php
/**
 * Created by PhpStorm.
 * User: horat1us
 * Date: 5/11/17
 * Time: 6:10 PM
 */

namespace Horat1us\Tests;


use Horat1us\Services\XmlParserService;

class XmlParserServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testGetters()
    {
        $xml = '<?xml version="1.0"?>
<Person name="Alexander" surname="Letnikow"><Head size="big" mind="small"/></Person>
';

        $document = new \DOMDocument();
        $document->loadXML($xml);

        $service = new XmlParserService($document);
        $this->assertEquals($document, $service->getDocument());
        $this->assertEquals([], $service->getAliases());
    }

    public function testIncorrectDocument()
    {
        $document = new static;
        $this->expectException(\InvalidArgumentException::class);
        new XmlParserService($document);
    }
}