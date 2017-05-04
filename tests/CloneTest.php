<?php

namespace Horat1us\Tests;


use Horat1us\Examples\Head;
use Horat1us\Examples\Person;
use Horat1us\XmlConvertibleObject;

class CloneTest extends \PHPUnit_Framework_TestCase
{
    public function testClone()
    {
        $xml = new Person('Alex', 'Letnikow', [
            new Head('big', 'strong', [
                new XmlConvertibleObject('Test', [
                    new XmlConvertibleObject('undefined'),
                ])
            ])
        ]);

        $cloned = clone $xml;
        $xml->name = 'Roman';
        $this->assertNotEquals($xml->name, $cloned->name);
        $xml->getXmlChildren()[0]->xmlChildren[0]->xmlChildren[0]->{'a'} = 1;
        $cloned->xmlChildren[0]->xmlChildren[0]->xmlChildren[0]->{'a'} = 2;
        $this->assertNotEquals(
            $xml->getXmlChildren()[0]->xmlChildren[0]->xmlChildren[0]->{'a'},
            $cloned->xmlChildren[0]->xmlChildren[0]->xmlChildren[0]->{'a'}
        );
    }

    public function testCloneEmptyXmlChildren()
    {
        $xml = new Person();

        $cloned = clone $xml;

        $this->assertNull($cloned->xmlChildren);
    }
}