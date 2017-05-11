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
        $current = $this->getSource();
        $compared = $this->getTarget();

        if (
            $current->getXmlElementName() !== $compared->getXmlElementName()
            || empty($current->getXmlChildren()) && !empty($compared->getXmlChildren())
            || array_reduce(
                $current->getXmlProperties(),
                function (bool $carry, string $property) use ($compared, $current) : bool {
                    return $carry
                        || (!property_exists($compared, $property))
                        || $current->{$property} !== $compared->{$property};
                },
                false
            )
        ) {
            return clone $current;
        }


        $newChildren = Collection::from($current->getXmlChildren() ?? [])
            ->map(function ($child) use ($compared) {
                return array_reduce(
                    $compared->getXmlChildren() ?? [],
                    function ($carry, $comparedChild) use ($child) {
                        if ($carry) {
                            return $carry;
                        }

                        $source = $child instanceof XmlConvertibleInterface
                            ? $child
                            : XmlConvertibleObject::fromXml($child);

                        $target = $comparedChild instanceof XmlConvertibleInterface
                            ? $comparedChild
                            : XmlConvertibleObject::fromXml($comparedChild);

                        return $source->xmlDiff($target);
                    });
            })
            ->filter(function ($child) {
                return $child !== null;
            })
            ->array;

        if (empty($newChildren)) {
            return null;
        }

        $target = clone $current;
        $target->setXmlChildren($newChildren);

        return clone $target;
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