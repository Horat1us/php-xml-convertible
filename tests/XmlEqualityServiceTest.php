<?php

namespace Horat1us\Tests;

use Horat1us\Services\XmlEqualityService;
use PHPUnit\Framework\TestCase;

class XmlEqualityServiceTest extends TestCase
{
    public function testWrongAttribute()
    {
        $service = new XmlEqualityService();

        $this->assertFalse($service->compare());
    }
}
