<?php
/**
 * Created by PhpStorm.
 * User: horat1us
 * Date: 5/1/17
 * Time: 8:06 PM
 */

namespace Horat1us\Tests;


use Horat1us\XmlConvertible;
use Horat1us\XmlConvertibleInterface;

/**
 * Class ElementNameTest
 * @package Horat1us\Tests
 */
class ElementNameTest extends \PHPUnit_Framework_TestCase implements XmlConvertibleInterface
{
    use XmlConvertible;

    public function testDefault()
    {
        $this->xmlElementName = null;
        $xml = $this->toXml();
        $this->assertEquals('ElementNameTest', $xml->tagName);
    }

    public function testCustom()
    {
        $this->xmlElementName = 'someThingDifferent';
        $xml = $this->toXml();
        $this->assertEquals($this->xmlElementName, $xml->tagName);
    }
}