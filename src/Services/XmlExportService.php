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

    public function __construct(XmlConvertibleInterface $object, \DOMDocument $document = null)
    {
        $this->setDocument($document)
            ->setObject($object);
    }

    public function export()
    {
        $xml = $this->getDocument()->createElement(
            $this->getObject()->getXmlElementName()
        );

        Collection::from($this->getObject()->getXmlChildren() ?? [])
            ->map(function ($child) {
                return $child instanceof XmlConvertibleInterface
                    ? $child->toXml($this->document)
                    : $child;
            })
            ->forEach(function (\DOMNode $child) use ($xml) {
                $xml->appendChild($child);
            });


        Collection::from($this->getObject()->getXmlProperties())
            ->reduce(function (Collection $properties, string $property) {
                $properties[$property] = $this->getObject()->{$property};
                return $properties;
            }, Collection::create())
            ->filter(function ($value): bool {
                return !is_array($value) && !is_object($value) && !is_null($value);
            })
            ->forEach(function ($value, string $property) use ($xml) {
                $xml->setAttribute($property, $value);
            });

        return $xml;
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