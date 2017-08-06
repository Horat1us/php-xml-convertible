<?php
/**
 * Created by PhpStorm.
 * User: horat1us
 * Date: 8/6/17
 * Time: 5:11 PM
 */

namespace Horat1us\Tests\helpers;


use Horat1us\XmlConvertible;
use Horat1us\XmlConvertibleInterface;

/**
 * Class XmlWithProtectedField
 * @package Horat1us\Tests\helpers
 */
class XmlWithProtectedField implements XmlConvertibleInterface
{
    use XmlConvertible;

    /**
     * @var string
     */
    protected $protectedField;

    /**
     * XmlWithProtectedField constructor.
     * @param string $protectedFieldValue
     */
    public function __construct(string $protectedFieldValue)
    {
        $this->setProtectedField($protectedFieldValue);
    }

    /**
     * @return string
     */
    public function getProtectedField(): string
    {
        return $this->protectedField;
    }

    /**
     * @param string $newValue
     * @return $this
     */
    public function setProtectedField(string $newValue)
    {
        $this->protectedField = $newValue;
        return $this;
    }

    /**
     * @param array|null $properties
     * @return array
     */
    public function getXmlProperties(array $properties = null): array
    {
        return ['protectedField'];
    }
}