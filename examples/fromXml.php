<?php

require_once(dirname(__DIR__) . '/vendor/autoload.php');

use Horat1us\Examples\Person;

$xml = '<?xml version="1.0"?>
<Person name="Alexander" surname="Letnikow"><Head size="big" mind="small" /></Person>';

$document = new \DOMDocument;
$document->loadXML($xml);
$person = Person::fromXml($document);
echo print_r($person, true);