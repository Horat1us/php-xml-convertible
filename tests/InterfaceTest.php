<?php

namespace Horat1us\Tests;

use Horat1us\XmlConvertibleObject;
use PHPUnit\Framework\TestCase;

class InterfaceTest extends TestCase
{
    public function testElementName()
    {
        $object = new XmlConvertibleObject();
        $object->setXmlElementName('a');
        $this->assertEquals($object->getXmlElementName(), $object->xmlElementName);
        $object->setXmlElementName();
        $this->assertNull($object->xmlElementName);
        $this->assertNotEquals($object->getXmlElementName(), $object->xmlElementName);
    }

    public function testChildren()
    {
        $object = new XmlConvertibleObject();
        $object->setXmlChildren([]);
        $this->assertEquals($object->getXmlChildren(), $object->xmlChildren);
        $object->setXmlChildren();
        $this->assertNull($object->getXmlChildren());
    }
}
