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
     * @param \DOMDocument|null $document
     * @return \DOMElement
     */
    public function toXml(\DOMDocument $document = null): \DOMElement
    {
        if (!$document) {
            $document = new \DOMDocument();
        }

        $xml = $document->createElement(
                $this->getXmlElementName()
        );
        if (!is_null($this->xmlChildren)) {
            foreach ((array)$this->xmlChildren as $child) {
                if ($child instanceof XmlConvertibleInterface) {
                    $xml->appendChild($child->toXml($document));
                } elseif ($child instanceof \DOMNode || $child instanceof \DOMElement) {
                    $xml->appendChild($child);
                } else {
                    throw new \UnexpectedValueException(
                        "Each child element must be an instance of " . XmlConvertibleInterface::class
                    );
                }
            }
        }

        $properties = $this->getXmlProperties();
        foreach ($properties as $property) {
            $value = $this->{$property};
            if (is_array($value) || is_object($value) || is_null($value)) {
                continue;
            }
            $xml->setAttribute($property, $value);
        }

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
     * @param \DOMDocument|\DOMElement $document
     * @param array $aliases
     * @return XmlConvertible[]|XmlConvertible|XmlConvertibleInterface[]|XmlConvertibleInterface
     */
    public static function fromXml($document, array $aliases = [])
    {
        if ($document instanceof \DOMDocument) {
            $length = $document->childNodes->length;
            if ($length === 1) {
                return static::fromXml($document->firstChild);
            }
            $nodes = [];
            for ($i = 0; $i < $length; $i++) {
                $nodes[] = static::fromXml($document->childNodes->item($i));
            }
            return $nodes;
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
            if(!$nodeObject instanceof XmlConvertibleObject) {
                if(!in_array($attribute->name, $properties)) {
                    throw new \UnexpectedValueException(
                        get_class($nodeObject) . ' must have defined ' . $attribute->name . ' XML property',
                        4
                    );
                }
            }
            $nodeObject->{$attribute->name} = $attribute->value;
        }

        $nodeObject->xmlChildren = [];
        /** @var \DOMElement $childNode */
        foreach($document->childNodes as $childNode) {
            $nodeObject->xmlChildren[] = static::fromXml($childNode, $aliases);
        }
        $nodeObject->xmlElementName = $document->nodeName;

        return $nodeObject;
    }

    /**
     * Getting array of property names which will be used as attributes in created XML
     *
     * @return \ReflectionProperty[]
     */
    public function getXmlProperties():array
    {
        $reflection = new \ReflectionClass(get_called_class());
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);

        return Collection::from($properties)
            ->filter(function (\ReflectionProperty $property): bool {
                return $property->getName() !== 'xmlChildren';
            })
            ->map(function (\ReflectionProperty $property): string {
                return $property->name;
            })
            ->array;
    }
}