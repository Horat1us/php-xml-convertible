<?php
/**
 * Created by PhpStorm.
 * User: horat1us
 * Date: 5/2/17
 * Time: 4:33 PM
 */

namespace Horat1us\Tests;


use Horat1us\Examples\Person;
use Horat1us\XmlConvertibleObject;

class AliasTest extends \PHPUnit_Framework_TestCase
{
    public function testWrongAliasClass()
    {
        $xml = '<?xml version="1.0"?>
<Person name="Alexander" surname="Letnikow"><Head size="big" mind="small"/></Person>
';
        $document = new \DOMDocument();
        $document->loadXML($xml);

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionCode(3);
        Person::fromXml($document, [
            'Person' => static::class,
            'Some' => Person::class,
        ]);

    }

    public function testWrongAliasType()
    {
        $xml = '<?xml version="1.0"?>
<Person name="Alexander" surname="Letnikow"><Head size="big" mind="small"/></Person>
';
        $document = new \DOMDocument();
        $document->loadXML($xml);


        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionCode(2);
        Person::fromXml($document, [
            'Person' => 2,
            'Some' => Person::class
        ]);
    }

    public function testWrongAliasInstance()
    {
        $xml = '<?xml version="1.0"?>
<Person name="Alexander" surname="Letnikow"><Head size="big" mind="small"/></Person>
';
        $document = new \DOMDocument();
        $document->loadXML($xml);


        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionCode(1);
        Person::fromXml($document, [
            'Person' => $this,
            'Some' => Person::class,
        ]);
    }

    public function testCustomAlias()
    {
        $xml = '<?xml version="1.0"?>
<Person name="Alexander" surname="Letnikow"><Head size="big" mind="small"/></Person>
';
        $document = new \DOMDocument();
        $document->loadXML($xml);

        $person = Person::fromXml($document, [
            'Person' => XmlConvertibleObject::class,
            'Custom' => Person::class,
        ]);
        $this->assertInstanceOf(XmlConvertibleObject::class, $person);
    }

    public function testCustomAliasInstance()
    {
        $xml = '<?xml version="1.0"?>
<Person name="Alexander" surname="Letnikow"><Head size="big" mind="small"/></Person>
';
        $document = new \DOMDocument();
        $document->loadXML($xml);

        $instance = new XmlConvertibleObject();
        $person = Person::fromXml($document, [
            'Person' => $instance,
            'Custom' => Person::class,
        ]);
        $this->assertInstanceOf(XmlConvertibleObject::class, $person);
        $this->assertEquals($instance, $person);
    }
}