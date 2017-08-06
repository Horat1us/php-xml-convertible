<?php
/**
 * Created by PhpStorm.
 * User: horat1us
 * Date: 8/6/17
 * Time: 5:21 PM
 */

namespace Horat1us\Tests;


use Horat1us\Tests\helpers\XmlWithProtectedField;

/**
 * Class ProtectedFieldTest
 * @package Horat1us\Tests
 */
class ProtectedFieldTest extends \PHPUnit_Framework_TestCase
{
    public function testFormingWithProtectedField()
    {
        $object = new XmlWithProtectedField('object');
        $document = new \DOMDocument;
        $document->appendChild($object->toXml($document));
        $string = $document->saveXML();

        $this->assertEquals(
            "<?xml version=\"1.0\"?>\n<XmlWithProtectedField protectedField=\"{$object->getProtectedField()}\"/>\n",
            $string
        );
    }
}