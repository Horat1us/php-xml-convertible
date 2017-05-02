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