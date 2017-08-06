<?php
/**
 * Created by PhpStorm.
 * User: horat1us
 * Date: 7/10/17
 * Time: 1:13 PM
 */

namespace Horat1us\Tests;


use Horat1us\XmlConvertibleObject;

class RealDiffTest extends \PHPUnit_Framework_TestCase
{
    public function testXmlEqual()
    {
        $this->assertNull($this->diff($this->getFirstText(), $this->getSecondText()));
    }

    public function testNotEqualXml()
    {
        $diff = $this->diff($this->getFirstText(), $this->getSecondText());
        xdebug_break();
    }

    protected function diff(string $text1, string $text2) {

        $document = new \DOMDocument();
        $document->loadXML($text1);

        $document2 = new \DOMDocument();
        $document2->loadXML($text2);


        $xml1 = XmlConvertibleObject::fromXml($document);
        $xml2 = XmlConvertibleObject::fromXml($document2);

        $diff = $xml1->xmlDiff($xml2);
        if(!$diff) {
            return null;
        }

        $result = new \DOMDocument();
        $diffElement = $diff->toXml($result);
        $result->appendChild($diffElement);
        return $result->saveXML();
    }

    protected function getFirstText()
    {
        $text = '<comp>
    <deal id="1">
        <period id="1"></period>
        <period id="2"></period>
    </deal>
    <deal id="2">
        <period id="1"></period>
        <period id="2"></period>
    </deal>
</comp>';
        return $this->prepare($text);
    }

    protected function getSecondText()
    {
        $text = '<comp>
    <deal id="1">
        <period id="2"></period>
    </deal>
    <deal id="2">
        <period id="1"></period>
    </deal>
</comp>';
        return $this->prepare($text);
    }

    protected function prepare($text)
    {
        return preg_replace('/>[\n\s]+</', '><', $text);
    }
}