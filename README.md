# Simple trait that converts your object to DOMElement (XML)
[![Build Status](https://travis-ci.org/Horat1us/php-xml-convertible.svg?branch=master)](https://travis-ci.org/Horat1us/php-xml-convertible)
[![Code Coverage](https://scrutinizer-ci.com/g/Horat1us/php-xml-convertible/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Horat1us/php-xml-convertible/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Horat1us/php-xml-convertible/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Horat1us/php-xml-convertible/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/horat1us/php-xml-convertible/v/stable)](https://packagist.org/packages/horat1us/php-xml-convertible)
[![Latest Unstable Version](https://poser.pugx.org/horat1us/php-xml-convertible/v/unstable)](https://packagist.org/packages/horat1us/php-xml-convertible)
[![License](https://poser.pugx.org/horat1us/php-xml-convertible/license)](https://packagist.org/packages/horat1us/php-xml-convertible)
[![Total Downloads](https://poser.pugx.org/horat1us/php-xml-convertible/downloads)](https://packagist.org/packages/horat1us/php-xml-convertible)

1. [Trait](./src/XmlConvertible.php)  
2. [Interface](./src/XmlConvertibleInterface.php)
3. [Examples](./examples/)    

This trait automatically converts your object to XML representation (`DOMElement`).
All your public properties (you can override method [getXmlProperties](./src/XmlConvertible.php#L69))
will be converted to attributes. 

To declare children in your object you need to set [$xmlChildren](./src/XmlConvertible.php#L16) property.  
To change element name you need to set [$xmlElementName](./src/XmlConvertible.php#L23) property 
*(short class name will be used by default)*

## Install
```bash
composer require horat1us/php-xml-convertible
```

## Test
```bash
make test
```

## Usage

You should just declare your class to implement `Horat1us\XmlConvertibleInterface` and use `Horat1us\\XmlConvertible` trait:
```php
<?php
namespace Horat1us\Examples;

use Horat1us\XmlConvertible;
use Horat1us\XmlConvertibleInterface;

class Person implements XmlConvertibleInterface
{
    use XmlConvertible;

    public $name;

    public $surname;

    public static function fromJson(string $json): Person
    {
        $array = json_decode($json, true);

        $object = new static;
        $object->name = $array['name'] ?? null;
        $object->surname = $array['surname'] ?? null;

        return $object;
    }
}
```


## About

This trait and interface can be useful when you create object than parse something and
 represents XML structure, like:
 
#### XmlConvertible::toXml [Example 1](./examples/toXml.php) 
 ```php
<?php

require_once(dirname(__DIR__) . '/vendor/autoload.php');

use Horat1us\Examples\Person;

$document = new \DOMDocument;
$element = Person::fromJson('{"name": "Alexander", "surname": "Letnikow"}')->toXml($document);
$document->appendChild($element);
echo $document->saveXml();
 ```
Will output:
 ```xml
<?xml version="1.0"?>
<Person name="Alexander" surname="Letnikow"/>
 ```

#### XmlConvertible::fromXml [Example 2](./examples/fromXml.php) 
```php
<?php

require_once(dirname(__DIR__) . '/vendor/autoload.php');

use Horat1us\Examples\Person;

$xml = '<?xml version="1.0"?>
<Person name="Alexander" surname="Letnikow"><Head size="big" mind="small" /></Person>';

$document = new \DOMDocument;
$document->loadXML($xml);
$person = Person::fromXml($document);
echo print_r($person, true);
```
Will output:
```
Horat1us\Examples\Person Object
(
    [name] => Alexander
    [surname] => Letnikow
    [xmlChildren] => Array
        (
            [0] => Horat1us\XmlConvertibleObject Object
                (
                    [xmlChildren] => Array
                        (
                        )

                    [xmlElementName] => Head
                    [size] => big
                    [mind] => small
                )

        )

    [xmlElementName] => Person
)
```
**See tests to know more about all features.**

### License

This project is open-sourced software licensed under the [MIT license](./LICENSE)

