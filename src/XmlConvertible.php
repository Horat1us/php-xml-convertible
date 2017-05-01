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
    protected $xmlElementName;

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
            $this->xmlElementName ?? (new \ReflectionClass(get_called_class()))->getShortName()
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
     * Getting array of property names which will be used as attributes in created XML
     *
     * @return \ReflectionProperty[]
     */
    protected function getXmlProperties()
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