<?php

namespace Horat1us\Tests;

use Horat1us\XmlConvertible;
use Horat1us\XmlConvertibleInterface;
use PHPUnit\Framework\TestCase;

class ElementAttributesTest extends TestCase implements XmlConvertibleInterface
{
    use XmlConvertible {
        getXmlProperties as traitGetXmlProperties;
    }

    public $attributes = [];

    public function testWithout()
    {
        $xml = $this->toXml();
        $this->assertEquals(0, $xml->attributes->length);
    }

    public function testFew()
    {
        $this->attributes = [
            'a' => 1,
            'b' => 2,
            // This attribute must be ignored
            'array' => [1,2,3],
            'null' => null,
            'object' => (object)[1,2,3],
         ];
        $xml = $this->toXml();
        $this->assertEquals(2, $xml->attributes->length);

        // Removing not-convertible attribute
        $attributes = array_splice($this->attributes, 0, 2);
        foreach ($attributes as $name => $value) {
            $this->assertEquals($value, $xml->getAttribute($name));
        }
    }


    public function getXmlProperties(?array $properties = null): array
    {
        if (empty($this->attributes)) {
            return $this->traitGetXmlProperties();
        }
        return array_keys($this->attributes);
    }

    public function __get($name)
    {
        $this->assertArrayHasKey($name, $this->attributes);
        return $this->attributes[$name];
    }
}
