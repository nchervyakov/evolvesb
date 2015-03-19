<?php
error_reporting(E_ERROR & ~E_NOTICE);
$root = dirname(__DIR__);
$loader = require $root.'/vendor/autoload.php';
$loader->add('', $root.'/classes/');

$pixie = new \App\Pixie();
$pixie->bootstrap($root);



$snappy = new \Knp\Snappy\Pdf($this->pixie->config->get('parameters.wkhtmltopdf_path'));
//$snappy->setOption('cookie', $_COOKIE);
$snappy->setOption('viewport-size', '800x600');
$snappy->setOption('toc', false);
$snappy->setOption('outline', false);
$snappy->setOption('outline-depth', 0);

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="receipt_'.$order->uid.'_'.date('Y.m.d').'.pdf"');
echo $snappy->getOutput('http://google.com');
