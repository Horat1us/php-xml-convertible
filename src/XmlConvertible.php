<?php

namespace Horat1us;

use Horat1us\Arrays\Collection;
use Horat1us\Services\XmlEqualityService;
use Horat1us\Services\XmlExportService;
use Horat1us\Services\XmlIntersectionService;
use Horat1us\Services\XmlParserService;

/**
 * Class XmlConvertible
 * @package Horat1us
 *
 * @mixin XmlConvertibleInterface
 */
trait XmlConvertible
{
    /**
     * @var XmlConvertibleInterface[]|\DOMNode[]|\DOMElement[]|null
     */
    public $xmlChildren;

    /**
     * Name of xml element (class name will be used by default)
     *
     * @var string
     */
    public $xmlElementName;

    /**
     * @param XmlConvertibleInterface $xml
     * @return XmlConvertible|XmlConvertibleInterface|null
     */
    public function xmlIntersect(
        XmlConvertibleInterface $xml
    )
    {
        $service = new XmlIntersectionService($this, $xml);
        return $service->intersect();
    }

    /**
     * @param XmlConvertibleInterface $xml
     * @return XmlConvertible|null
     */
    public function xmlDiff(XmlConvertibleInterface $xml)
    {
        $current = $this;
        $compared = $xml;

        if (
            $current->getXmlElementName() !== $compared->getXmlElementName()
            || empty($current->getXmlChildren()) && !empty($compared->getXmlChildren())
            || array_reduce(
                $current->getXmlProperties(),
                function (bool $carry, string $property) use ($compared, $current) : bool {
                    return $carry
                        || (!property_exists($compared, $property))
                        || $current->{$property} !== $compared->{$property};
                },
                false
            )
        ) {
            return clone $current;
        }


        $newChildren = Collection::from($current->getXmlChildren() ?? [])
            ->map(function ($child) use ($compared) {
                return array_reduce(
                    $compared->getXmlChildren() ?? [],
                    function ($carry, $comparedChild) use ($child) {
                        if ($carry) {
                            return $carry;
                        }

                        $diff = ($child instanceof XmlConvertibleInterface
                            ? $child
                            : XmlConvertibleObject::fromXml($child)
                        )->xmlDiff(
                            $comparedChild instanceof XmlConvertibleInterface
                                ? $comparedChild
                                : XmlConvertibleObject::fromXml($comparedChild)
                        );

                        return $diff;
                    });
            })
            ->filter(function ($child) {
                return $child !== null;
            })
            ->array;

        if (empty($newChildren)) {
            return null;
        }

        $target = clone $current;
        $target->setXmlChildren($newChildren);

        return clone $target;
    }

    /**
     * Converts object to XML and compares it with given
     *
     * @param XmlConvertibleInterface $xml
     * @return bool
     */
    public function xmlEqual(XmlConvertibleInterface $xml): bool
    {
        $service = new XmlEqualityService($this, $xml);
        return $service->compare();
    }

    /**
     * @param \DOMDocument|\DOMElement $document
     * @param array $aliases
     * @return XmlConvertibleInterface
     */
    public static function fromXml($document, array $aliases = [])
    {
        /** @var \DOMElement $document */
        if (!in_array(get_called_class(), $aliases)) {
            $aliases[(new \ReflectionClass(get_called_class()))->getShortName()] = get_called_class();
        }
        return (new XmlParserService($document, $aliases))->convert();
    }

    /**
     * @param \DOMDocument|null $document
     * @return \DOMElement
     */
    public function toXml(\DOMDocument $document = null): \DOMElement
    {
        $service = new XmlExportService($this, $document);
        return $service->export();
    }

    /**
     * Name of xml element (class name will be used by default)
     *
     * @return string
     */
    public function getXmlElementName(): string
    {
        return $this->xmlElementName ?? (new \ReflectionClass(get_called_class()))->getShortName();
    }

    /**
     * Settings name of xml element
     *
     * @param string $name
     * @return static
     */
    public function setXmlElementName(string $name = null)
    {
        $this->xmlElementName = $name;
        return $this;
    }

    /**
     * @return XmlConvertibleInterface[]|\DOMNode[]|\DOMElement[]|null
     */
    public function getXmlChildren()
    {
        return $this->xmlChildren;
    }

    /**
     * @param XmlConvertibleInterface[]|\DOMNode[]|\DOMElement[]|null $xmlChildren
     * @return static
     */
    public function setXmlChildren(array $xmlChildren = null)
    {
        $this->xmlChildren = $xmlChildren ?: null;
        return $this;
    }

    /**
     * Getting array of property names which will be used as attributes in created XML
     *
     * @param array|null $properties
     * @return array|string[]
     */
    public function getXmlProperties(array $properties = null): array
    {
        $properties = $properties
            ?: array_map(function(\ReflectionProperty $property) {
                return $property->getName();
            }, (new \ReflectionClass(get_called_class()))->getProperties(\ReflectionProperty::IS_PUBLIC));


        return array_filter($properties, function(string $property) {
            return !in_array($property, ['xmlChildren', 'xmlElementName']);
        });
    }

    /**
     * Cloning all children by default
     */
    public function __clone()
    {
        $this->xmlChildren = array_map(function ($xmlChild) {
            return clone $xmlChild;
        }, $this->xmlChildren ?? []) ?: null;
    }
}