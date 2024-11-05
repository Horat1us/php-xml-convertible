<?php

namespace Horat1us\Tests;

use Horat1us\Tests\helpers\SampleXml;
use Horat1us\XmlConvertible;
use Horat1us\XmlConvertibleInterface;
use PHPUnit\Framework\TestCase;

class ElementChildrenTest extends TestCase implements XmlConvertibleInterface
{
    use XmlConvertible;

    public function testWithout()
    {
        $this->xmlChildren = null;
        $xml = $this->toXml();
        $this->assertEquals(0, $xml->childNodes->length);
    }

    public function testInvalid()
    {
        $this->xmlChildren = [
            new \UnexpectedValueException()
        ];
        $this->expectException(\TypeError::class);
        $this->toXml();
    }

    public function testOne()
    {
        $doc = new \DOMDocument();
        $this->xmlChildren = [$this->toXml($doc)];
        $xml = $this->toXml($doc);
        $this->assertEquals(1, $xml->childNodes->length);
    }

    public function testFew()
    {
        $doc = new \DOMDocument();
        $this->xmlChildren = [
            new SampleXml(),
            $this->toXml($doc),
        ];
        $xml = $this->toXml($doc);
        $this->assertEquals(2, $xml->childNodes->length);
    }
}
