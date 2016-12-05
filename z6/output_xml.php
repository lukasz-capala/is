<?php
header('Content-type: text/xml');
header('Content-Disposition: attachment; filename="summary.xml"');

$dom = new DOMDocument('1.0');
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;

$dom->load("results.xml");
echo $dom->saveXML();
unlink("results.xml");
?>