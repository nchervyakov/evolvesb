<?php
error_reporting(E_ERROR & ~E_NOTICE);
$root = dirname(__DIR__);
$loader = require $root.'/vendor/autoload.php';
$loader->add('', $root.'/classes/');

$pixie = new \App\Pixie();
$pixie->bootstrap($root);
$data = include __DIR__.'/../database/data/data.php';
if (!is_array($data)) {
   return;
}
$result = \App\DataImport\EvolveScaterProductImporter::create($pixie)->import($data);
var_dump($result);