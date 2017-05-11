<?php
/**
 * Created by PhpStorm.
 * User: horat1us
 * Date: 5/11/17
 * Time: 6:41 PM
 */

namespace Horat1us\Services;

use Horat1us\Arrays\Collection;
use Horat1us\XmlConvertibleInterface;
use Horat1us\XmlConvertibleObject;


/**
 * Class XmlDifferenceService
 * @package Horat1us\Services
 */
class XmlDifferenceService
{
    /**
     * @var XmlConvertibleInterface
     */
    protected $source;

    /**
     * @var XmlConvertibleInterface
     */
    protected $target;


    /**
     * XmlDifferenceService constructor.
     * @param XmlConvertibleInterface $source
     * @param XmlConvertibleInterface $target
     */
    public function __construct(
        XmlConvertibleInterface $source,
        XmlConvertibleInterface $target
    )
    {
        $this
            ->setSource($source)
            ->setTarget($target);
    }

    /**
     * @return XmlConvertibleInterface|null
     */
    public function difference()
    {
        if ($this->getIsCommonDifferent()) {
            return clone $this->getSource();
        }

        $newChildren = $this->getDifferentChildren();
        if (empty($newChildren)) {
            return null;
        }

        $target = clone $this->getSource();
        $target->setXmlChildren($newChildren);

        return clone $target;
    }

    /**
     * Difference by element name, children count and properties
     */
    public function getIsCommonDifferent()
    {
        return $this->getSource()->getXmlElementName() !== $this->getTarget()->getXmlElementName()
            || empty($this->getSource()->getXmlChildren()) && !empty($this->getTarget()->getXmlChildren())
            || $this->getIsDifferentProperties();
    }

    /**
     * @return array
     */
    public function getDifferentChildren()
    {
        return Collection::from($this->getSource()->getXmlChildren() ?? [])
            ->map(function ($child) {
                return $this->transform($child);
            })
            ->map(function (XmlConvertibleInterface $child) {
                return $this->findDifference($child);
            })
            ->filter(function ($child) {
                return $child !== null;
            })
            ->array;
    }

    /**
     * Finding difference in properties
     *
     * @return bool
     */
    protected function getIsDifferentProperties()
    {
        foreach ($this->getSource()->getXmlProperties() as $property) {
            if (
                !property_exists($this->getTarget(), $property)
                || $this->getSource()->{$property} !== $this->getTarget()->{$property}
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param XmlConvertibleInterface|\DOMNode|\DOMDocument $object
     * @return XmlConvertibleInterface
     */
    protected function transform($object)
    {
        return $object instanceof XmlConvertibleInterface
            ? $object
            : XmlConvertibleObject::fromXml($object);
    }

    /**
     * @param XmlConvertibleInterface $child
     * @return XmlConvertibleInterface|null
     */
    protected function findDifference(
        XmlConvertibleInterface $child
    )
    {
        foreach ($this->getTarget()->getXmlChildren() ?? [] as $comparedChild) {
            $target = $this->transform($comparedChild);

            if ($difference = $child->xmlDiff($target)) {
                return $difference;
            }
        }

        return null;
    }

    /**
     * @return XmlConvertibleInterface
     */
    public function getSource(): XmlConvertibleInterface
    {
        return $this->source;
    }

    /**
     * @param XmlConvertibleInterface $source
     * @return $this
     */
    public function setSource(XmlConvertibleInterface $source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @return XmlConvertibleInterface
     */
    public function getTarget(): XmlConvertibleInterface
    {
        return $this->target;
    }

    /**
     * @param XmlConvertibleInterface $target
     * @return $this
     */
    public function setTarget(XmlConvertibleInterface $target)
    {
        $this->target = $target;

        return $this;
    }
}