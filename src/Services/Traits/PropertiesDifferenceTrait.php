<?php

namespace Horat1us\Services\Traits;


use Horat1us\XmlConvertibleInterface;

/**
 * Class PropertiesDifferenceTrait
 * @package Horat1us\Services\Traits
 */
trait PropertiesDifferenceTrait
{
    /**
     * @return XmlConvertibleInterface
     */
    abstract public function getSource(): XmlConvertibleInterface;

    /**
     * @return XmlConvertibleInterface
     */
    abstract public function getTarget(): XmlConvertibleInterface;

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
}