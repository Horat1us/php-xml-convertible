<?php

namespace Horat1us;

/**
 * Interface XmlConvertibleInterface
 * @package Horat1us
 *
 * Do not implement this interface without using XmlConvertible trait!
 */
interface XmlConvertibleInterface
{
    /**
     * Converts object to XML and compares it with given
     *
     * @param XmlConvertibleInterface $xml
     * @return bool
     */
    public function xmlEqual(XmlConvertibleInterface $xml) :bool;

    /**
     * @param XmlConvertibleInterface $xml
     * @param XmlConvertibleInterface|null $target
     * @param bool $skipEmpty
     * @return XmlConvertible|XmlConvertibleInterface|null
     */
    public function xmlIntersect(
        XmlConvertibleInterface $xml,
        XmlConvertibleInterface $target = null,
        bool $skipEmpty = true
    );

    /**
     * @param XmlConvertibleInterface $xml
     * @return XmlConvertibleInterface|XmlConvertible
     */
    public function xmlDiff(XmlConvertibleInterface $xml);

    /**
     * Converts current object to XML element
     *
     * @param \DOMDocument $document
     * @return \DOMElement
     */
    public function toXml(\DOMDocument $document = null): \DOMElement;

    /**
     * @param \DOMDocument|\DOMElement $document
     * @param array $aliases
     * @return static
     */
    public static function fromXml($document, array $aliases = []);

    /**
     * Name of xml element (class name will be used by default)
     *
     * @return string
     */
    public function getXmlElementName(): string;

    /**
     * Settings name of xml element
     *
     * @param string $name
     * @return static
     */
    public function setXmlElementName(string $name = null);

    /**
     * @return XmlConvertibleInterface[]|\DOMNode[]|\DOMElement[]|null
     */
    public function getXmlChildren();

    /**
     * @param XmlConvertibleInterface[]|\DOMNode[]|\DOMElement[]|null $xmlChildren
     * @return static
     */
    public function setXmlChildren(array $xmlChildren = null);
}