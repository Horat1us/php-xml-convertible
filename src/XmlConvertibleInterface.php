<?php

namespace Horat1us;

/**
 * Interface XmlConvertibleInterface
 * @package Horat1us
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
}