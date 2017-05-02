<?php
/**
 * Created by PhpStorm.
 * User: horat1us
 * Date: 5/2/17
 * Time: 1:55 PM
 */

namespace Horat1us;


/**
 * Class XmlConvertibleObject
 * @package Horat1us
 */
class XmlConvertibleObject implements XmlConvertibleInterface
{
    use XmlConvertible {
        getXmlProperties as protected traitXmlProperties;
    }

    /**
     * @return array
     */
    public function getXmlProperties(): array
    {
        return $this->traitXmlProperties(array_keys(get_object_vars($this)));
    }
}