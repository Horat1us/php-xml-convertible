<?php

namespace Horat1us;

/**
 * Interface XmlConvertibleInterface
 * @package Horat1us
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
    public function toXml(\DOMDocument $document): \DOMElement;

    /**
     * Converts object to XML and compares it with given
     *
     * @param XmlConvertibleInterface $xml
     * @return bool
     */
    public function xmlEqualTo(XmlConvertibleInterface $xml) :bool;
}