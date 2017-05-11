<?php
/**
 * Created by PhpStorm.
 * User: horat1us
 * Date: 5/11/17
 * Time: 6:32 PM
 */

namespace Horat1us\Services;


use Horat1us\Services\Traits\PropertiesDifferenceTrait;
use Horat1us\XmlConvertibleInterface;
use Horat1us\XmlConvertibleObject;

/**
 * Class XmlIntersectionService
 * @package Horat1us\Services
 */
class XmlIntersectionService
{
    use PropertiesDifferenceTrait;

    /**
     * @var XmlConvertibleInterface
     */
    protected $source;

    /**
     * @var XmlConvertibleInterface
     */
    protected $target;

    /**
     * XmlIntersectionService constructor.
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
     * @return null|XmlConvertibleInterface
     */
    public function intersect()
    {
        if ($this->getIsCommonDifferent()) {
            return null;
        }

        $newChildren = array_uintersect(
            $this->getTarget()->getXmlChildren() ?? [],
            $this->getSource()->getXmlChildren() ?? [],
            [$this, 'compare']
        );

        return clone $this->getSource()->setXmlChildren($newChildren);
    }

    public function getIsCommonDifferent() :bool
    {
        return $this->getTarget()->getXmlElementName() !== $this->getSource()->getXmlElementName()
            || $this->getIsDifferentProperties();
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