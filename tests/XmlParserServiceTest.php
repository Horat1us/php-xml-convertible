<?php

namespace Horat1us\Tests;

use Horat1us\Services\XmlParserService;
use PHPUnit\Framework\TestCase;

class XmlParserServiceTest extends TestCase
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
        $document = new \stdClass();
        $this->expectException(\InvalidArgumentException::class);
        new XmlParserService($document);
    }
}
