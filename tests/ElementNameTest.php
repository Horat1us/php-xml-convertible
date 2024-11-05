<?php

namespace Horat1us\Tests;

use Horat1us\XmlConvertible;
use Horat1us\XmlConvertibleInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class ElementNameTest
 * @package Horat1us\Tests
 */
class ElementNameTest extends TestCase implements XmlConvertibleInterface
{
    use XmlConvertible;

    public function testDefault()
    {
        $this->xmlElementName = null;
        $xml = $this->toXml();
        $this->assertEquals('ElementNameTest', $xml->tagName);
    }

    public function testCustom()
    {
        $this->xmlElementName = 'someThingDifferent';
        $xml = $this->toXml();
        $this->assertEquals($this->xmlElementName, $xml->tagName);
    }
}
