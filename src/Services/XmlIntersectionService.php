<?php
/**
 * Created by PhpStorm.
 * User: horat1us
 * Date: 5/11/17
 * Time: 6:32 PM
 */

namespace Horat1us\Services;


use Horat1us\XmlConvertibleInterface;
use Horat1us\XmlConvertibleObject;

class XmlIntersectionService
{
    /**
     * @var XmlConvertibleInterface
     */
    protected $source;

    /**
     * @var XmlConvertibleInterface
     */
    protected $target;


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
     * @return null|XmlConvertibleInterface
     */
    public function intersect()
    {
        $current = clone $this->getSource();
        $compared = clone $this->getTarget();

        if (
            $current->getXmlElementName() !== $compared->getXmlElementName()
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
            return null;
        }

        $newChildren = array_uintersect(
            $compared->getXmlChildren() ?? [],
            $current->getXmlChildren() ?? [],
            [$this, 'compare']
        );

        return $current->setXmlChildren($newChildren);
    }

    /**
     * @param $comparedChild
     * @param $currentChild
     * @return int
     */
    public function compare($comparedChild, $currentChild)
    {
        $source = $currentChild instanceof XmlConvertibleInterface
            ? $currentChild
            : XmlConvertibleObject::fromXml($currentChild);

        $target = $comparedChild instanceof XmlConvertibleInterface
            ? $comparedChild
            : XmlConvertibleObject::fromXml($comparedChild);

        $diff = $source->xmlIntersect($target);

        return $diff === null ? -1 : 0;
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