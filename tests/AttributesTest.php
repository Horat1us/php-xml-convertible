<?php
/**
 * Created by PhpStorm.
 * User: horat1us
 * Date: 5/2/17
 * Time: 4:41 PM
 */

namespace Horat1us\Tests;


use Horat1us\Examples\Person;
use Horat1us\XmlConvertibleObject;

class AttributesTest extends \PHPUnit_Framework_TestCase
{
    public function testWrongAttribute()
    {
        $xml = '<?xml version="1.0"?>
<Person name="Alexander" surname="Letnikow" middlename="Alexandrovich"><Head size="big" mind="small"/></Person>
';

        $document = new \DOMDocument();
        $document->loadXML($xml);

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionCode(4);

        Person::fromXml($document);
    }

    public function testAttributes()
    {
        $name = 'Alexander';
        $surname = 'Letnikow';
        $xml = '<?xml version="1.0"?>
<Person name="' . $name . '" surname="' . $surname . '" ><Head size="big" mind="small"/></Person>
';

        $document = new \DOMDocument();
        $document->loadXML($xml);

        $person = Person::fromXml($document);
        $this->assertEquals($name, $person->name);
        $this->assertEquals($surname, $person->surname);
    }

    public function testWithDefaultObject()
    {
        $name = 'Alexander';
        $surname = 'Letnikow';
        $xml = '<?xml version="1.0"?>
<Person name="' . $name . '" surname="' . $surname . '" ><Head size="big" mind="small"/></Person>
';

        $document = new \DOMDocument();
        $document->loadXML($xml);

        $person = XmlConvertibleObject::fromXml($document);
        $this->assertEquals($name, $person->name);
        $this->assertEquals($surname, $person->surname);
    }
}