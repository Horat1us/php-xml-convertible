<?php

require_once(dirname(__DIR__) . '/vendor/autoload.php');

use Horat1us\Examples\Person;

$document = new \DOMDocument;
$element = Person::fromJson('{"name": "Alexander", "surname": "Letnikow"}')->toXml($document);
$document->appendChild($element);
echo $document->saveXml();