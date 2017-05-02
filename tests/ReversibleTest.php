<?php
/**
 * Created by PhpStorm.
 * User: horat1us
 * Date: 5/2/17
 * Time: 4:12 PM
 */

namespace Horat1us\Tests;


use Horat1us\Examples\Person;

class ReversibleTest extends \PHPUnit_Framework_TestCase
{
    public function testFromFirst()
    {
        $xml = '<?xml version="1.0"?>
<Person name="Alexander" surname="Letnikow"><Head size="big" mind="small"/></Person>
';

        $document = new \DOMDocument();
        $document->loadXML($xml);

        $person = Person::fromXml($document);

        $document = new \DOMDocument();
        $node = $person->toXml($document);
        $document->appendChild($node);
        $result = $document->saveXML();
        $this->assertEquals($xml, $result);
    }

    public function testToFirst()
    {
        $person = new Person();

        $person->name = 'Alexander';
        $person->surname = 'Letnikow';

        $childPerson = new Person();
        $childPerson->name = 'Some';
        $childPerson->surname = 'Name';

        $subChildPersonFirst = new Person();
        $subChildPersonFirst->name = 'Sub';
        $subChildPersonFirst->surname = 'First';

        $subChildPersonSecond = new Person();
        $subChildPersonSecond->name = 'Sub';
        $subChildPersonSecond->surname = 'Second';

        $childPerson->xmlChildren = [
            $subChildPersonFirst,
            $subChildPersonSecond
        ];

        $person->xmlChildren = [$childPerson];

        $documentGenerated = new \DOMDocument();
        $documentReversed = new \DOMDocument();


        $xml = $person->toXml($documentGenerated);
        $documentGenerated->appendChild($xml);

        $documentParsed = new \DOMDocument();
        $documentParsed->loadXML($documentGenerated->saveXML());

        $result = Person::fromXml($documentParsed)->toXml($documentReversed);
        $documentReversed->appendChild($result);

        $this->assertEquals($documentGenerated->saveXML(), $documentReversed->saveXML());

    }
}