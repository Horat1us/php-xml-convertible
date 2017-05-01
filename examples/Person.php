<?php
require_once(dirname(__DIR__) . '/vendor/autoload.php');

use Horat1us\XmlConvertible;
use Horat1us\XmlConvertibleInterface;

class Person implements XmlConvertibleInterface{
use XmlConvertible;

public $name;

public $surname;

public static function fromJson(string $json) :Person {
$array = json_decode($json, true);

$object = new static;
$object->name = $array['name'] ?? null;
$object->surname = $array['surname'] ?? null;

return $object;
}
}

$document = new \DOMDocument;
$element = Person::fromJson('{"name": "Alexander", "surname": "Letnikow"}')->toXml($document);
$document->appendChild($element);
echo $document->saveXml();