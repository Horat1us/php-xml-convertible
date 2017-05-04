<?php

namespace Horat1us\Tests;


use Horat1us\Examples\Person;
use Horat1us\XmlConvertibleObject;

class EqualTest extends \PHPUnit_Framework_TestCase
{
    public function testNotEqualAttributes()
    {
        $person = new Person();
        $person->name = 'Alexander';
        $person->surname = 'Unknown';

        $comparedPerson = new Person();
        $comparedPerson->name = 'Alexander';
        $comparedPerson->surname = 'Letnikow';

        $this->assertTrue($person->xmlEqual($person));
        $this->assertTrue($comparedPerson->xmlEqual($comparedPerson));

        $this->assertFalse($person->xmlEqual($comparedPerson));
        $this->assertFalse($comparedPerson->xmlEqual($person));
    }

    public function testNotEqualChildren()
    {
        $person = new Person();
        $person->name = 'Alexander';
        $person->surname = 'Letnikow';

        $comparedPerson = Person::fromXml($person->toXml());
        $comparedPerson->xmlChildren = [new XmlConvertibleObject('sample', [
            clone $person
        ])];

        $this->assertTrue($comparedPerson->xmlEqual($comparedPerson));
        $this->assertTrue($person->xmlEqual($person));

        $this->assertFalse($comparedPerson->xmlEqual($person));
        $this->assertFalse($person->xmlEqual($comparedPerson));
    }

    public function testEqual()
    {
        $person = new Person();
        $person->name = 'Alexander';
        $person->surname = 'Letnikow';

        $person->xmlChildren = [
            new Person(),
            $test = new XmlConvertibleObject('test')
        ];
        $test->{'a'} = 2;

        $comparedPerson = Person::fromXml($person->toXml());
        $this->assertTrue($person->xmlEqual($comparedPerson));

        $deepClonePerson = Person::fromXml($comparedPerson->toXml());
        $this->assertTrue($deepClonePerson->xmlEqual($person));
        $this->assertTrue($comparedPerson->xmlEqual($deepClonePerson));
    }
}