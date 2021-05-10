<?php

namespace viavario\pdfgenerator;

use GuzzleHttp\Client;

/**
 * Class to generate screenshots as PDF or PNG using headless Chrome on Amazon AWS Lambda.
 *
 * @author  Lien Dassen <lien.dassen@viavario.be>
 *
 * @version 1.0.1
 */
class PDFGenerator
{
    /**
     * Page orientation - Landscape.
     *
     * @var string
     */
    public const ORIENTATION_LANDSCAPE = 'landscape';

    /**
     * Page orientation - Portrait.
     *
     * @var string
     */
    public const ORIENTATION_PORTRAIT = 'portrait';

    /**
     * Margins for the PDF document.
     *
     * @var array<string, string>
     */
    protected $margins = [
        'top' => '1cm',
        'right' => '1cm',
        'bottom' => '1cm',
        'left' => '1cm',
    ];

    /**
     * API Key used for the AWS Lambda function.
     *
     * @var string
     */
    protected $apiKey = null;

    /**
     * Endpoint URL.
     *
     * @var string
     */
    protected $endpoint;

    /**
     * Display header and footer. Defaults to false.
     *
     * @var bool
     */
    protected $displayHeaderFooter = false;

    /**
     * HTML template for the print footer. Should use the same format as the headerTemplate.
     *
     * @var string
     */
    protected $footerTemplate = '';

    /**
     * HTML template for the print header. Should be valid HTML markup.
     * See https://pptr.dev/#?product=Puppeteer&version=v9.1.0&show=api-pagecontent for more information.
     *
     * @var string
     */
    protected $headerTemplate = '';

    /**
     * Paper orientation. Defaults to false.
     *
     * @var bool
     */
    protected $landscape = false;

    /**
     * Paper format. If set, takes priority over width or height options. Defaults to 'Letter'.
     *
     * @var string
     */
    protected $format = 'A4';

    /**
     * Give any CSS @page size declared in the page priority over what is declared in width and height or format
     * options. Defaults to false, which will scale the content to fit the paper size.
     *
     * @var bool
     */
    protected $preferCSSPageSize = false;

    /**
     * Hides default white background and allows capturing screenshots with transparency. Defaults to false.
     *
     * @var bool
     */
    protected $omitBackground = false;

    /**
     * contains the HTML content.
     *
     * @var string
     */
    protected $content = '';

    /**
     * Contains the URL where the content is.
     *
     * @var string
     */
    protected $url = '';

    /**
     * The name of the file generated.
     *
     * @var string
     */
    protected $filename = '';

    /**
     * The viewport height of the file.
     *
     * @var int
     */
    protected $viewPortheight = 1200;

    /**
     * The viewport width of the file.
     *
     * @var int
     */
    protected $viewPortWidth = 768;

    /**
     * The username for HTTP authentication.
     *
     * @var string
     */
    protected $username = '';

    /**
     * The password for HTTP authentication.
     *
     * @var string
     */
    protected $password = '';

    /**
     * Print background graphics. Defaults to false.
     *
     * @var bool
     */
    protected $printBackground = false;

    /**
     * Class constructor.
     *
     * @param string $endpoint The endpoint URL
     * @param string $apiKey   The API key for the AWS Lambda function (defaults to null)
     *
     * @return void
     */
    public function __construct(string $endpoint, string $apiKey = null)
    {
        $this->endpoint = $endpoint;
        $this->apiKey = $apiKey;
    }

    /**
     * Sets the margins for the page.
     *
     * @param string $margins Top margin, or all margins
     * @param string $right   Right margin
     * @param string $bottom  bottom margin
     * @param string $left    Left margin
     *
     * @return \viavario\pdfgenerator\PDFGenerator return this instance
     */
    public function setMargins(string $margins, string $right = null, string $bottom = null, string $left = null)
    {
        $this->margins = [
            'top' => $margins,
            'right' => $right ?? $margins,
            'bottom' => $bottom ?? $margins,
            'left' => $left ?? $margins,
        ];

        return $this;
    }

    /**
     * Sets the Footer template.
     *
     * Should be valid HTML markup with following classes used to inject printing values into them:
     *  - date          formatted print date
     *  - title         document title
     *  - url           document location
     *  - pageNumber    current page number
     *  - totalPages    total pages in the document
     *
     * @param string $html HTML Markup
     *
     * @return \viavario\pdfgenerator\PDFGenerator
     */
    public function setFooterTemplate(string $html)
    {
        $this->footerTemplate = trim($html);
        if ($this->footerTemplate) {
            $this->displayHeaderFooter(true);
        }

        return $this;
    }

    /**
     * Sets the Header template.
     *
     * Should be valid HTML markup with following classes used to inject printing values into them:
     *  - date          formatted print date
     *  - title         document title
     *  - url           document location
     *  - pageNumber    current page number
     *  - totalPages    total pages in the document
     *
     * @param string $html HTML Markup
     *
     * @return \viavario\pdfgenerator\PDFGenerator
     */
    public function setHeaderTemplate(string $html)
    {
        $this->headerTemplate = trim($html);
        if ($this->headerTemplate) {
            $this->displayHeaderFooter(true);
        }

        return $this;
    }

    /**
     * Display the footer and header template.
     *
     * @param bool $displayHeaderFooter set to true to display the header and footer in the PDF document
     *
     * @return \viavario\pdfgenerator\PDFGenerator
     */
    public function displayHeaderFooter(bool $displayHeaderFooter)
    {
        $this->displayHeaderFooter = $displayHeaderFooter;

        return $this;
    }

    /**
     * Set the page orientation.
     *
     * @param string $orientation PDFGenerator::ORIENTATION_LANDSCAPE or PDFGenerator::ORIENTATION_PORTRAIT
     *
     * @return \viavario\pdfgenerator\PDFGenerator
     */
    public function setOrientation(string $orientation)
    {
        $this->landscape = $orientation === self::ORIENTATION_LANDSCAPE;

        return $this;
    }

    /**
     * Sets the format of the page.
     *
     * The format options are:
     *  Letter: 8.5in x 11in
     *  Legal: 8.5in x 14in
     *  Tabloid: 11in x 17in
     *  Ledger: 17in x 11in
     *  A0: 33.1in x 46.8in
     *  A1: 23.4in x 33.1in
     *  A2: 16.54in x 23.4in
     *  A3: 11.7in x 16.54in
     *  A4: 8.27in x 11.7in
     *  A5: 5.83in x 8.27in
     *  A6: 4.13in x 5.83in
     * 
     * @param string $format    The page format for the PDF.
     *
     * @return \viavario\pdfgenerator\PDFGenerator
     */
    public function setFormat(string $format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     *  Give any CSS @page size declared in the page priority over what is declared in width and height or format
     * options. Defaults to false, which will scale the content to fit the paper size.
     *
     * @param bool $preferCSSPageSize Set to true to enqble CSS page size
     *
     * @return \viavario\pdfgenerator\PDFGenerator
     */
    public function preferCSSPageSize(bool $preferCSSPageSize)
    {
        $this->preferCSSPageSize = $preferCSSPageSize;

        return $this;
    }

    /**
     * Hides default white background and allows capturing screenshots with transparency. Defaults to false.
     *
     * @param bool $omitBackground Set to true to omit white backgrounds
     *
     * @return \viavario\pdfgenerator\PDFGenerator
     */
    public function omitBackground(bool $omitBackground)
    {
        $this->omitBackground = $omitBackground;

        return $this;
    }

    /**
     * Sets the content.
     *
     * @param string $content The HTML content to take a screenshot of
     *
     * @return \viavario\pdfgenerator\PDFGenerator
     */
    public function setContent(string $content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Set the URL.
     *
     * @param string $url The URL of the page to take a screenshot of
     *
     * @return \viavario\pdfgenerator\PDFGenerator
     */
    public function setURL(string $url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Sets the filename.
     *
     * @param string $filename Set the filename for the output. Only extensions .pdf and .png are allowed.
     *
     * @return \viavario\pdfgenerator\PDFGenerator
     */
    public function setFilename(string $filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Set the width and height of the viewport.
     *
     * @param   int  $viewportWidth  The width of the viewport
     * @param   int  $viewportHeight The height of the viewport
     *
     * @return \viavario\pdfgenerator\PDFGenerator
     */
    public function setViewportSize(int $viewportWidth, int $viewportHeight)
    {
        $this->viewPortheight = $viewPortheight;
        $this->viewPortWidth = $viewPortWidth;

        return $this;
    }

    /**
     * Sets the username and password for basic HTTP authentication.
     *
     * @param string $username  The username for basic HTTP authentication
     * @param string $password  The password for basic HTTP authentication
     *
     * @return \viavario\pdfgenerator\PDFGenerator
     */
    public function setHttpAuthentication(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;

        return $this;
    }

    /**
     * Set to true to print backgrounds.
     *
     * @param bool $printBackground Set to true to print backgrounds
     *
     * @return \viavario\pdfgenerator\PDFGenerator
     */
    public function printBackground(bool $printBackground)
    {
        $this->printBackground = $printBackground;

        return $this;
    }

    /**
     * Generate a screenshot.
     *
     * @param string $filename  The filename to write to (defaults to null which will cause the method to return
     *                          binary data)
     *
     * @return mixed If no filename is specified, then the generated file is returned as binary data.
     *               If a filename is specified, then true is returned when the file was written
     *               successfully, or false otherwise.
     */
    public function generate(string $filename = null)
    {
        $client = new Client([
            'base_uri' => $this->endpoint,
        ]);

        $body = [
            'pdfOptions' => [
                'displayHeaderFooter' => $this->displayHeaderFooter,
                'headerTemplate' => $this->headerTemplate,
                'footerTemplate' => $this->footerTemplate,
                'printBackground' => $this->printBackground,
                'landscape' => $this->landscape,
                'format' => $this->format,
                'margin' => $this->margins,
                'preferCSSPageSize' => $this->preferCSSPageSize,
                'omitBackground' => $this->omitBackground,
            ],
            'content' => $this->content,
            'url' => $this->url,
            'filename' => $this->filename,
            'width' => $this->viewPortWidth,
            'height' => $this->viewPortheight,
            'username' => $this->username,
            'password' => $this->password,
        ];

        $headers = [];
        if ($this->apiKey) {
            $headers['x-api-key'] = $this->apiKey;
        }

        $response = $client->request('POST', 'capture', [
            'body' => json_encode($body),
            'headers' => $headers,
        ]);

        if ($response->getStatusCode() === 200) {
            if ($filename) {
                return file_put_contents($filename, $response->getBody());
            }

            return $response->getBody();
        }

        return false;
    }
}
