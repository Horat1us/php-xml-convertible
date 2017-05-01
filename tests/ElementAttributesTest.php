<?php
/**
 * Created by PhpStorm.
 * User: horat1us
 * Date: 5/1/17
 * Time: 8:35 PM
 */

namespace Horat1us\Tests;


use Horat1us\XmlConvertible;
use Horat1us\XmlConvertibleInterface;

class ElementAttributesTest extends \PHPUnit_Framework_TestCase implements XmlConvertibleInterface
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
            [1,2,3], // This attribute must be ignored when converting
         ];
        $xml = $this->toXml();
        $this->assertEquals(2, $xml->attributes->length);

        // Removing not-convertible attribute
        unset($this->attributes[0]);
        foreach($this->attributes as $name => $value) {
            $this->assertEquals($value, $xml->getAttribute($name));
        }
    }


    public function getXmlProperties()
    {
        if(empty($this->attributes)) {
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