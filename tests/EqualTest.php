<?php
/**
 * Created by PhpStorm.
 * User: horat1us
 * Date: 5/3/17
 * Time: 1:28 PM
 */

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

        $this->assertTrue($person->xmlEqualTo($person));
        $this->assertTrue($comparedPerson->xmlEqualTo($comparedPerson));

        $this->assertFalse($person->xmlEqualTo($comparedPerson));
        $this->assertFalse($comparedPerson->xmlEqualTo($person));
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

        $this->assertTrue($comparedPerson->xmlEqualTo($comparedPerson));
        $this->assertTrue($person->xmlEqualTo($person));

        $this->assertFalse($comparedPerson->xmlEqualTo($person));
        $this->assertFalse($person->xmlEqualTo($comparedPerson));
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
        $this->assertTrue($person->xmlEqualTo($comparedPerson));

        $deepClonePerson = Person::fromXml($comparedPerson->toXml());
        $this->assertTrue($deepClonePerson->xmlEqualTo($person));
        $this->assertTrue($comparedPerson->xmlEqualTo($deepClonePerson));
    }
}