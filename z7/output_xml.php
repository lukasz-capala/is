<?php
header('Content-type: text/zip');
header('Content-Disposition: attachment; filename="summary.zip"');

$dom = new DOMDocument('1.0');
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;

$dom->load("results.xml");
$dom->save("summary.xml");

$zip = new ZipArchive();
$zip->open("summary.zip", ZipArchive::CREATE);
$zip->addFile("summary.xml", "summary.xml");
$zip->addFile("css/theme.css", "xmls.css");
$zip->close();
echo file_get_contents("summary.zip");

unlink("summary.xml");
unlink("theme.css");
unlink("summary.zip");
unlink("results.xml");
?>