<?php

namespace Horat1us;

use Horat1us\Arrays\Collection;

/**
 * Class XmlConvertible
 * @package Horat1us
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
     * @param bool $skipEmpty
     * @return XmlConvertible|XmlConvertibleInterface|null
     */
    public function xmlIntersect(
        XmlConvertibleInterface $xml,
        bool $skipEmpty = true
    )
    {
        $current = clone $this;
        $compared = clone $xml;

        if (
            $current->getXmlElementName() !== $compared->getXmlElementName()
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
            return null;
        }

        $newChildren = array_uintersect(
            $compared->getXmlChildren() ?? [],
            $current->getXmlChildren() ?? [],
            function ($comparedChild, $currentChild) use ($skipEmpty) {
                if ($comparedChild === $currentChild) {
                    return 0;
                }

                $diff = ($currentChild instanceof XmlConvertibleInterface
                    ? $currentChild
                    : XmlConvertibleObject::fromXml($currentChild)
                )->xmlIntersect(
                    $comparedChild instanceof XmlConvertibleInterface
                        ? $comparedChild
                        : XmlConvertibleObject::fromXml($comparedChild)
                );

                return $diff === null ? -1 : 0;
            }
        );

        return $current->setXmlChildren($newChildren);
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
        $document = new \DOMDocument();
        $document->appendChild($this->toXml($document));
        $current = $document->saveXML();

        $document = new \DOMDocument();
        $document->appendChild($xml->toXml($document));
        $compared = $document->saveXML();

        return $current === $compared;
    }

    /**
     * @param \DOMDocument|\DOMElement $document
     * @param array $aliases
     * @return static
     */
    public static function fromXml($document, array $aliases = [])
    {
        if ($document instanceof \DOMDocument) {
            return static::fromXml($document->firstChild, $aliases);
        }

        /** @var \DOMElement $document */
        if (!in_array(get_called_class(), $aliases)) {
            $aliases[(new \ReflectionClass(get_called_class()))->getShortName()] = get_called_class();
        }
        foreach ($aliases as $key => $alias) {
            if (is_object($alias)) {
                if (!$alias instanceof XmlConvertibleInterface) {
                    throw new \UnexpectedValueException(
                        "All aliases must be instance or class implements " . XmlConvertibleInterface::class,
                        1
                    );
                }
                $aliases[is_int($key) ? $alias->getXmlElementName() : $key] = $alias;
                continue;
            }
            if (!is_string($alias)) {
                throw new \UnexpectedValueException(
                    "All aliases must be instance or class implements " . XmlConvertibleInterface::class,
                    2
                );
            }
            $instance = new $alias;
            if (!$instance instanceof XmlConvertibleInterface) {
                throw new \UnexpectedValueException(
                    "All aliases must be instance of " . XmlConvertibleInterface::class,
                    3
                );
            }
            unset($aliases[$key]);
            $aliases[is_int($key) ? $instance->getXmlElementName() : $key] = $instance;
        }

        $nodeObject = $aliases[$document->nodeName] ?? new XmlConvertibleObject;
        $properties = $nodeObject->getXmlProperties();

        /** @var \DOMAttr $attribute */
        foreach ($document->attributes as $attribute) {
            if (!$nodeObject instanceof XmlConvertibleObject) {
                if (!in_array($attribute->name, $properties)) {
                    throw new \UnexpectedValueException(
                        get_class($nodeObject) . ' must have defined ' . $attribute->name . ' XML property',
                        4
                    );
                }
            }
            $nodeObject->{$attribute->name} = $attribute->value;
        }

        /** @var \DOMElement $childNode */
        foreach ($document->childNodes as $childNode) {
            $nodeObject->xmlChildren[] = static::fromXml($childNode, $aliases);
        }
        $nodeObject->xmlElementName = $document->nodeName;

        return $nodeObject;
    }

    /**
     * @param \DOMDocument|null $document
     * @return \DOMElement
     */
    public function toXml(\DOMDocument $document = null): \DOMElement
    {
        $document = $document ?? new \DOMDocument();

        $xml = $document->createElement(
            $this->getXmlElementName()
        );

        Collection::from($this->xmlChildren ?? [])
            ->map(function($child) use($document) {
                return $child instanceof XmlConvertibleInterface
                    ? $child->toXml($document)
                    : $child;
            })
            ->forEach(function(\DOMNode $child) use($xml) {
                $xml->appendChild($child);
            });


        Collection::from($this->getXmlProperties())
            ->reduce(function(Collection $properties, string $property) {
                $properties[$property] = $this->{$property};
                return $properties;
            }, Collection::create())
            ->filter(function($value) :bool {
                return !is_array($value) && !is_object($value) && !is_null($value);
            })
            ->forEach(function($value, string $property) use($xml) {
                $xml->setAttribute($property, $value);
            });

        return $xml;
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