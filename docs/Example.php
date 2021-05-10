<?php

include_once __DIR__.'/vendor/autoload.php';

use viavario\pdfgenerator\PDFGenerator;

// This is invoke URL of the API Gateway or the endpoint returned by the
// serverless deploy command without `capture` at the end
$endpoint = 'https://##########.execute-api.eu-west-3.amazonaws.com/dev/';
$apiKey = '<your-api-key>'; // Your API Key configured in the API Gateway
$filename = 'screenshot.pdf'; // Change to screenshot.png to get a PNG image

$generator = new PDFGenerator($endpoint, $apiKey);
$generator->setURL('https://google.com')
    ->setFilename($filename)
    ->setMargins('1.5cm')
    ->setFormat('A4')
    // The screenshot service automatically increases the height of the viewport
    // to take a full page screenshot
    ->setViewportSize(1920, 1080)
    ->setOrientation(PDFGenerator::ORIENTATION_PORTRAIT);

try {
    $tempfile = $generator->generate();
    // Change the Content-Type to image/png if you changed the extension of the
    // filename to .png
    header('Content-Type: application/pdf');
    echo $tempfile;
}
catch (\Exception $e) {
    echo $e->getMessage();
}
