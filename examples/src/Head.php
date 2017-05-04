<?php

namespace Horat1us\Examples;

use Horat1us\XmlConvertible;
use Horat1us\XmlConvertibleInterface;

/**
 * Class Head
 * @package Horat1us\Examples
 */
class Head implements XmlConvertibleInterface
{
    use XmlConvertible;

    public $size;

    public $mind;

    /**
     * Head constructor.
     * @param string $size
     * @param string $mind
     * @param array|null $xmlChildren
     */
    public function __construct($size = 'large', $mind = 'small', array $xmlChildren = null)
    {
        $this->size = $size;
        $this->mind = $mind;

        $this->xmlChildren = $xmlChildren;
    }
}