<?php
/**
 * Created by PhpStorm.
 * User: horat1us
 * Date: 5/11/17
 * Time: 6:18 PM
 */

namespace Horat1us\Services;


use Horat1us\XmlConvertibleInterface;

/**
 * Class XmlEqualityService
 * @package Horat1us\Services
 */
class XmlEqualityService
{
    /**
     * @var XmlConvertibleInterface
     */
    public $first;

    /**
     * @var XmlConvertibleInterface
     */
    public $second;

    /**
     * XmlEqualityService constructor.
     * @param XmlConvertibleInterface|null $first
     * @param XmlConvertibleInterface|null $second
     */
    public function __construct(
        XmlConvertibleInterface $first = null,
        XmlConvertibleInterface $second = null
    )
    {
        $this->first = $first;
        $this->second = $second;
    }

    public function compare(): bool
    {
        if (
            !$this->first instanceof XmlConvertibleInterface
            || !$this->second instanceof XmlConvertibleInterface
        ) {
            return false;
        }

        $document = new \DOMDocument();
        $document->appendChild($this->first->toXml($document));
        $current = $document->saveXML();

        $document = new \DOMDocument();
        $document->appendChild($this->second->toXml($document));
        $compared = $document->saveXML();

        return $current === $compared;
    }
}