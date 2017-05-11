<?php
/**
 * Created by PhpStorm.
 * User: horat1us
 * Date: 5/11/17
 * Time: 6:22 PM
 */

namespace Horat1us\Tests;


use Horat1us\Services\XmlEqualityService;

class XmlEqualityServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testWrongAttribute()
    {
        $service = new XmlEqualityService();

        $this->assertFalse($service->compare());
    }
}