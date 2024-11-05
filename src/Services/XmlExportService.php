<?php

namespace Horat1us\Services;

use Horat1us\XmlConvertibleInterface;

/**
 * Class XmlExportService
 * @package Horat1us\Services
 */
class XmlExportService
{
    /**
     * @var XmlConvertibleInterface
     */
    public $object;

    /**
     * @var \DOMDocument
     */
    public $document;

    /**
     * XmlExportService constructor.
     * @param XmlConvertibleInterface $object
     * @param \DOMDocument|null $document
     */
    public function __construct(XmlConvertibleInterface $object, ?\DOMDocument $document = null)
    {
        $this->setDocument($document)
            ->setObject($object);
    }

    /**
     * Converting object to \DOMElement
     *
     * @return \DOMElement
     */
    public function export()
    {
        $xml = $this->createElement();

        foreach ($this->getObject()->getXmlChildren() ?? [] as $child) {
            if (!is_object($child)) {
                throw new \TypeError("Invalid child node type: " . gettype($child));
            }
            if ($child instanceof XmlConvertibleInterface) {
                $child = $child->toXml($this->document);
            }
            if (!$child instanceof \DOMNode) {
                throw new \TypeError("Invalid child node class: " . get_class($child));
            }
            $xml->appendChild(
                $child instanceof XmlConvertibleInterface
                    ? $child->toXml($this->document)
                    : $child
            );
        }

        foreach ($this->getObject()->getXmlProperties() as $property) {
            $value = $this->getObject()->getXmlProperty($property);
            if (is_array($value) || is_object($value) || is_null($value)) {
                continue;
            }
            $xml->setAttribute($property, $value);
        }

        return $xml;
    }

    /**
     * Creating new element to put object into
     *
     * @return \DOMElement
     */
    protected function createElement(): \DOMElement
    {
        return $this->getDocument()->createElement(
            $this->getObject()->getXmlElementName()
        );
    }

    /**
     * Can we put current attribute to XML
     *
     * @return \Closure
     */
    protected function getIsAttribute(): \Closure
    {
        return function ($value): bool {
            return !is_array($value) && !is_object($value) && !is_null($value);
        };
    }

    /**
     * @return XmlConvertibleInterface
     */
    public function getObject(): XmlConvertibleInterface
    {
        return $this->object;
    }

    /**
     * @param XmlConvertibleInterface $object
     * @return $this
     */
    public function setObject(XmlConvertibleInterface $object)
    {
        $this->object = $object;
        return $this;
    }

    /**
     * @return \DOMDocument
     */
    public function getDocument(): \DOMDocument
    {
        return $this->document;
    }

    /**
     * @param \DOMDocument $document
     * @return $this
     */
    public function setDocument(?\DOMDocument $document = null)
    {
        $this->document = $document ?? new \DOMDocument();
        return $this;
    }
}
