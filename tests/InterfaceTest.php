<?php
/**
 * Created by PhpStorm.
 * User: horat1us
 * Date: 5/4/17
 * Time: 2:26 PM
 */

namespace Horat1us\Tests;


use Horat1us\XmlConvertibleObject;

class InterfaceTest extends \PHPUnit_Framework_TestCase
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