<?php
/**
 * Created by PhpStorm.
 * User: horat1us
 * Date: 5/4/17
 * Time: 11:35 AM
 */

namespace Horat1us\Tests;


use Horat1us\Examples\Head;
use Horat1us\Examples\Person;
use Horat1us\XmlConvertibleObject;

class IntersectTest extends \PHPUnit_Framework_TestCase
{
    public function testIntersection()
    {
        $xml = new Person("Alex", "Letni", [
            new Head("small", null, [
                new XmlConvertibleObject('Eye'),
                new XmlConvertibleObject('Eye', [
                    new Person('Adam', 'Morgan'),
                ]),
            ])
        ]);

        $compared = new Person("Alex", "Letni", [
            new Head('small', null, [
                new XmlConvertibleObject('Eye', [
                    new Person('Adam', 'Morgan'),
                ]),
            ])
        ]);


        $result = $xml->xmlIntersect($compared);
        $this->assertInstanceOf(Person::class, $result);
        /** @var Person $result */

        $this->assertEquals($result->name, $compared->name);
        $this->assertEquals($result->name, $xml->name);
        $this->assertNotNull($result->xmlChildren);
        $this->assertCount(1, $result->xmlChildren);
        $this->assertInstanceOf(Head::class, $result->xmlChildren[0]);
        $this->assertNotNull($result->xmlChildren[0]->xmlChildren);
        $this->assertCount(1, $result->xmlChildren[0]->xmlChildren);
        // Eye
        $this->assertInstanceOf(XmlConvertibleObject::class, $result->xmlChildren[0]->xmlChildren[0]);
        $this->assertNotNull(
            $result->xmlChildren[0]->xmlChildren[0]->xmlChildren
        );
        $this->assertCount(
            1,
            // Sub-person
            $result->xmlChildren[0]->xmlChildren[0]->xmlChildren
        );
        $this->assertInstanceOf(
            Person::class,
            $subPerson = $result->xmlChildren[0]->xmlChildren[0]->xmlChildren[0]
        );
        $this->assertEquals(
            'Adam',
            $subPerson->name
        );
        $this->assertEquals(
            'Morgan',
            $subPerson->surname
        );
    }

    public function testEmptyIntersect()
    {
        $first = new Person('Alex', "Lowe", [
            new XmlConvertibleObject('inner')
        ]);
        $second = clone $first;

        $result = $first->xmlIntersect($second);
        $this->assertInstanceOf(Person::class, $result);
        $this->assertCount(1, $result->getXmlChildren());
        $this->assertInstanceOf(XmlConvertibleObject::class, $result->getXmlChildren()[0]);
        $this->assertEquals('inner', $result->getXmlChildren()[0]->getXmlElementName());
    }

    public function testDifferentElements()
    {
        $xml = new Person();
        $compared = new Head();
        $this->assertNull($xml->xmlIntersect($compared));
    }

    public function testOne()
    {
        $first = new Person('Alex', "First");
        $second = clone $first;

        $result = $first->xmlIntersect($first);
        $this->assertInstanceOf(get_class($first), $result);
    }
}