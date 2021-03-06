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
     * XmlConvertibleObject constructor.
     * @param string $xmlElementName
     * @param array $xmlChildren
     */
    public function __construct(string $xmlElementName = null, array $xmlChildren = null)
    {
        $this->xmlElementName = $xmlElementName;
        $this->xmlChildren = $xmlChildren;
    }

    /**
     * @param array|null $properties
     * @return array
     */
    public function getXmlProperties(array $properties = null): array
    {
        return $this->traitXmlProperties($properties ?? array_keys(get_object_vars($this)));
    }


}