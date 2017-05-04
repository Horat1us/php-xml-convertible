<?php

namespace Horat1us;

/**
 * Interface XmlConvertibleInterface
 * @package Horat1us
 *
 * Do not implement this interface without using XmlConvertible trait!
 *
 * @mixin XmlConvertible
 */
interface XmlConvertibleInterface
{
    /**
     * Converts current object to XML element
     *
     * @param \DOMDocument $document
     * @return \DOMElement
     */
    public function toXml(\DOMDocument $document = null): \DOMElement;

    /**
     * Converts object to XML and compares it with given
     *
     * @param XmlConvertibleInterface $xml
     * @return bool
     */
    public function xmlEqual(XmlConvertibleInterface $xml) :bool;

    /**
     * @param \DOMDocument|\DOMElement $document
     * @param array $aliases
     * @return static
     */
    public static function fromXml($document, array $aliases = []);
}