<?php

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

    private array $xmlProperties = [];

    /**
     * XmlConvertibleObject constructor.
     * @param string $xmlElementName
     * @param array $xmlChildren
     */
    public function __construct(?string $xmlElementName = null, array $xmlChildren = null)
    {
        $this->xmlElementName = $xmlElementName;
        $this->xmlChildren = $xmlChildren;
    }

    /**
     * @param array|null $properties
     * @return array
     */
    public function getXmlProperties(?array $properties = null): array
    {
        return $this->traitXmlProperties($properties ?? array_keys($this->xmlProperties));
    }

    public function __get(string $name)
    {
        return $this->xmlProperties[$name] ?? null;
    }

    public function __set(string $name, $value): void
    {
        $this->xmlProperties[$name] = $value;
    }
}
