<?php

namespace Horat1us\Tests;

use Horat1us\Tests\helpers\XmlWithProtectedField;
use PHPUnit\Framework\TestCase;

class ProtectedFieldTest extends TestCase
{
    public function testFormingWithProtectedField()
    {
        $object = new XmlWithProtectedField('object');
        $document = new \DOMDocument();
        $document->appendChild($object->toXml($document));
        $string = $document->saveXML();

        $this->assertEquals(
            "<?xml version=\"1.0\"?>\n<XmlWithProtectedField protectedField=\"{$object->getProtectedField()}\"/>\n",
            $string
        );
    }
}
