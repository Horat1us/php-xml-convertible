<?php

namespace Horat1us\Services;


use Horat1us\Arrays\Collection;
use Horat1us\XmlConvertibleInterface;
use Horat1us\XmlConvertibleObject;

/**
 * Class XmlParserService
 * @package Horat1us\Services
 */
class XmlParserService
{
    /**
     * @var \DOMNode
     */
    protected $element;

    /**
     * @var XmlConvertibleInterface[]
     */
    protected $aliases;

    /**
     * XmlParserService constructor.
     * @param $document
     * @param array $aliases
     */
    public function __construct($document, array $aliases = [])
    {
        $this
            ->setAliases($aliases)
            ->setDocument($document);
    }

    public function __clone()
    {
        $this->element = clone $this->element;
    }

    /**
     * @return XmlConvertibleInterface
     */
    public function convert()
    {
        /** @var XmlConvertibleInterface $nodeObject */
        $nodeObject = Collection::from($this->aliases)
            ->reduce(function (XmlConvertibleInterface $carry, XmlConvertibleInterface $alias, string $key) {
                return $this->getIsAliasMatch($key, $alias) ? clone $alias : $carry;
            }, new XmlConvertibleObject($this->element->nodeName));

        if ($this->element->hasChildNodes()) {
            $this->convertChildren($nodeObject);
        }
        if ($this->element->hasAttributes()) {
            $this->convertAttributes($nodeObject);
        }

        return $nodeObject;
    }

    /**
     * @param XmlConvertibleInterface $object
     * @return $this
     */
    public function convertChildren(XmlConvertibleInterface &$object)
    {
        $children = [];
        $service = clone $this;

        foreach ($this->element->childNodes as $childNode) {
            $service->setDocument($childNode);
            $children[] = $service->convert();
        }
        $object->setXmlChildren($children);

        return $this;
    }

    public function convertAttributes(XmlConvertibleInterface &$object)
    {
        $properties = $object->getXmlProperties();
        /** @var \DOMAttr $attribute */
        foreach ($this->element->attributes as $attribute) {
            if (
                !$object instanceof XmlConvertibleObject
                && !in_array($attribute->name, $properties)
            ) {
                throw new \UnexpectedValueException(
                    get_class($object) . ' must have defined ' . $attribute->name . ' XML property',
                    4
                );
            }
            $object->{$attribute->name} = $attribute->value;
        }
    }

    /**
     * @param \DOMNode|\DOMDocument $document
     * @return $this
     */
    public function setDocument($document)
    {
        if ($document instanceof \DOMDocument) {
            return $this->setDocument($document->firstChild);
        }

        if (!$document instanceof \DOMNode) {
            throw new \InvalidArgumentException("Document must be instance of DOMElement or DOMNode");
        }
        $this->element = $document;

        return $this;
    }

    /**
     * @return \DOMElement
     */
    public function getDocument()
    {
        return $this->element;
    }

    /**
     * @param XmlConvertibleInterface[]|string $aliases
     * @return $this
     */
    public function setAliases(array $aliases = [])
    {
        $this->aliases = [];

        Collection::from($aliases)
            ->map(function ($alias) {
                return $this->mapAlias($alias);
            })
            ->forEach(function (XmlConvertibleInterface $alias, string $key) {
                $this->aliases[$this->mapKey($key, $alias)] = $alias;
            });

        return $this;
    }

    /**
     * @return XmlConvertibleInterface[]
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * @param string|XmlConvertibleInterface $element
     * @return XmlConvertibleInterface
     */
    protected function mapAlias($element)
    {
        if ($element instanceof XmlConvertibleInterface) {
            return $element;
        }
        if (!is_string($element)) {
            throw new \UnexpectedValueException(
                "All aliases must be instance or class implements " . XmlConvertibleInterface::class
            );
        }

        // Additional type-checking
        return $this->mapAlias(new $element);
    }

    /**
     * @param string|integer $key
     * @param XmlConvertibleInterface $element
     * @return string
     */
    protected function mapKey($key, XmlConvertibleInterface $element): string
    {
        return is_numeric($key)
            ? $element->getXmlElementName()
            : $key;
    }

    /**
     * @param string $key
     * @param XmlConvertibleInterface $element
     * @return bool
     */
    protected function getIsAliasMatch(string $key, XmlConvertibleInterface $element): bool
    {
        if ($this->element->nodeName !== $key) {
            return false;
        }

        return Collection::from($element->getXmlProperties())
            ->filter(function ($property) use ($element) {
                return empty($element->{$property});
            })
            ->reduce(function (bool $carry, $property) use ($element) {
                return $carry && $this->element->attributes->getNamedItem($property) != $element->{$property};
            }, true);
    }
}