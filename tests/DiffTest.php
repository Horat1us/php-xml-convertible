<?php
/**
 * Created by PhpStorm.
 * User: horat1us
 * Date: 5/4/17
 * Time: 12:10 PM
 */

namespace Horat1us\Tests;


use Horat1us\Examples\Head;
use Horat1us\Examples\Person;
use Horat1us\XmlConvertibleObject;
use SebastianBergmann\PHPLOC\Log\XML;

class DiffTest extends \PHPUnit_Framework_TestCase
{
    public function testDifferenceWithEmpty()
    {
        $first = new Person('Alex', 'Jobs');
        $second = new Person('Alex', 'Jobs', [
            new XmlConvertibleObject()
        ]);
        $diff = $first->xmlDiff($second);
        $this->assertInstanceOf(get_class($first), $diff);
    }

    public function testDifferenceWithAdditional()
    {
        $first = new Person('Alex', 'Jobs', [
            new XmlConvertibleObject('a'),
            new XmlConvertibleObject('b'),
        ]);
        $second = new Person('Alex', 'Jobs', [
            new XmlConvertibleObject('b'),
        ]);

        $diff = $first->xmlDiff($second);
        $this->assertInstanceOf(get_class($first), $diff);
        $this->assertNotNull($diff->xmlChildren);
        $this->assertEquals(1, count($diff->xmlChildren ?? []));
        $this->assertEquals('a', $diff->xmlChildren[0]->getXmlElementName());
    }

    public function testDifferenceWithNoProperty()
    {
        $a = new XmlConvertibleObject('undefined');
        $b = new XmlConvertibleObject('undefined');
        $a->{'property'} = mt_rand();
        $diff = $a->xmlDiff($b);
        $this->assertInstanceOf(get_class($a), $diff);
    }

    public function testDifference()
    {
        $xml = new Person("Alex", "Letni", [
            new Head("small", 'cool', [
                new XmlConvertibleObject('Eye'),
                new XmlConvertibleObject('Eye', [
                    new Person('Adam', 'Morgan'),
                ]),
            ])
        ]);

        $compared = new Person("Alex", "Letni", [
            new Head('small', 'cool', [
                new XmlConvertibleObject('Eye', [
                    new Person('Adam', 'Morgan'),
                ]),
            ])
        ]);


        $result = $xml->xmlDiff($compared);

        $this->assertInstanceOf(Person::class, $result);

        $this->assertNotNull($result->xmlChildren);
        $this->assertCount(1, $result->xmlChildren);

        $this->assertInstanceOf(Head::class, $result->xmlChildren[0]);
        $this->assertNotNull($result->xmlChildren[0]->xmlChildren);
        $this->assertCount(1, $result->xmlChildren[0]->xmlChildren);

        $this->assertInstanceOf(
            XmlConvertibleObject::class,
            $result->xmlChildren[0]->xmlChildren[0]
        );
        $this->assertNull($result->xmlChildren[0]->xmlChildren[0]->xmlChildren);
    }
}