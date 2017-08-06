<?php
/**
 * Created by PhpStorm.
 * User: horat1us
 * Date: 5/11/17
 * Time: 6:23 PM
 */

namespace Horat1us\Services;


use Horat1us\Arrays\Collection;
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
    public function __construct(XmlConvertibleInterface $object, \DOMDocument $document = null)
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

        Collection::from($this->getObject()->getXmlChildren() ?? [])
            ->map($this->mapChild())
            ->forEach(function (\DOMNode $child) use ($xml) {
                $xml->appendChild($child);
            });

        $reduce = function (Collection $properties, string $property) {
            $properties[$property] = $this->{$property};
            return $properties;
        };

        Collection::from($this->getObject()->getXmlProperties())
            ->reduce(function (Collection $collection, string $property) use ($reduce) {
                return $reduce->call($this->getObject(), $collection, $property);
            }, Collection::create())
            ->filter($this->getIsAttribute())
            ->forEach(function ($value, string $property) use ($xml) {
                $xml->setAttribute($property, $value);
            });

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
     * Preparing all children to export
     *
     * @return \Closure
     */
    protected function mapChild(): \Closure
    {
        return function ($child) {
            return $child instanceof XmlConvertibleInterface
                ? $child->toXml($this->document)
                : $child;
        };
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
    public function setDocument(\DOMDocument $document = null)
    {
        $this->document = $document ?? new \DOMDocument();
        return $this;
    }
}