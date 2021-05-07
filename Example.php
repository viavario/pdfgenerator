<?php

include_once __DIR__.'/vendor/autoload.php';

use viavario\pdfgenerator\PDFGenerator;

$endpoint = 'https://testing.execute-api.eu-west-3.amazonaws.com/production/';
$apiKey = 'your-api-key';
$filename = 'screenshot.pdf';

$generator = new PDFGenerator($endpoint, $apiKey);
$generator->setURL('https://google.com')
    ->setFilename($filename)
    ->setMargins('1.5cm')
    ->setFormat('A4')
    ->setViewportSize(1920, 1080)
    ->setOrientation(PDFGenerator::ORIENTATION_PORTRAIT);

try {
    $tempfile = $generator->generate();
    header('Content-Type: application/pdf');
    echo $tempfile;
}
catch (\Exception $e) {
    echo $e->getMessage();
}
