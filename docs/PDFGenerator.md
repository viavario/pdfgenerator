# viavario\pdfgenerator\PDFGenerator  

PDFGenerator is a tool to easily generate pdf's or png's of any page. 
There will be an example in the /docs folder. 
In this example you will have to add your own API and endpoint URL.

Class to generate screenshots as PDF or PNG using headless Chrome on Amazon AWS Lambda.


## Requirements

For this package you will need a headless server. 
We have used  [Amazon AWS Lambda](https://aws.amazon.com/lambda/") we have also used [Puppeteer](https://pptr.dev/). However if you want to use a different headless browser, this is possible. Note that you will still have to do a lot of work manually.

<hr/>

## How to insall

```
composer require once viavario\pdfgenerator
```



## Methods

| Name | Description |
|------|-------------|
|[__construct](#pdfgenerator__construct)|Class constructor.|
|[displayHeaderFooter](#pdfgeneratordisplayheaderfooter)|Display the footer and header template.|
|[generate](#pdfgeneratorgenerate)|Generate a screenshot.|
|[omitBackground](#pdfgeneratoromitbackground)|Hides default white background and allows capturing screenshots with transparency. Defaults to false.|
|[preferCSSPageSize](#pdfgeneratorprefercsspagesize)|Give any CSS @page size declared in the page priority over what is declared in width and height or format options. Defaults to false, which will scale the content to fit the paper size.|
|[printBackground](#pdfgeneratorprintbackground)|Set to true to print backgrounds.|
|[setContent](#pdfgeneratorsetcontent)|Sets the content.|
|[setFilename](#pdfgeneratorsetfilename)|Sets the filename.|
|[setFooterTemplate](#pdfgeneratorsetfootertemplate)|Sets the Footer template.|
|[setFormat](#pdfgeneratorsetformat)|Sets the format of the page.|
|[setHeaderTemplate](#pdfgeneratorsetheadertemplate)|Sets the Header template.|
|[setHttpAuthentication](#pdfgeneratorsethttpauthentication)|Sets the HTTPAuthentication username and password.|
|[setMargins](#pdfgeneratorsetmargins)|Sets the margins for the page.|
|[setOrientation](#pdfgeneratorsetorientation)|Set the page orientation.|
|[setURL](#pdfgeneratorseturl)|Set the URL.|
|[setViewportSize](#pdfgeneratorsetviewportsize)|Sets the viewport height and viewport width.|




### PDFGenerator::__construct  

**Description**

```php
public __construct (string $endpoint, string $apiKey)
```

Class constructor. 

 

**Parameters**

* `(string) $endpoint`
: The endpoint URL  
* `(string) $apiKey`
: The API key for the AWS Lambda function

**Return Values**

`void`




<hr />


### PDFGenerator::displayHeaderFooter  

**Description**

```php
public displayHeaderFooter (bool $displayHeaderFooter)
```

Display the footer and header template. 

 

**Parameters**

* `(bool) $displayHeaderFooter`
: set to true to display the header and footer in the PDF document  

**Return Values**

`\PDFGenerator`




<hr />


### PDFGenerator::generate  

**Description**

```php
public generate (string $filename)
```

Generate a screenshot. 

 

**Parameters**

* `(string) $filename`
: The filename to write to (defaults to null which will cause the method to return binary data)  

**Return Values**

`mixed`

> If no filename is specified, then the generated file is returned as binary data.  
If a filename is specified, then true is returned when the file was written  
successfully, or false otherwise.


<hr />


### PDFGenerator::omitBackground  

**Description**

```php
public omitBackground (bool $omitBackground)
```

Hides default white background and allows capturing screenshots with transparency. Defaults to false. 

 

**Parameters**

* `(bool) $omitBackground`
: set to true to omit white backgrounds  

**Return Values**

`\PDFGenerator`




<hr />


### PDFGenerator::preferCSSPageSize  

**Description**

```php
public preferCSSPageSize (bool $preferCSSPageSize)
```

Give any CSS @page size declared in the page priority over what is declared in width and height or format
options. Defaults to false, which will scale the content to fit the paper size. 

 

**Parameters**

* `(bool) $preferCSSPageSize`
: set to true to enqble CSS page size  

**Return Values**

`\PDFGenerator`




<hr />


### PDFGenerator::printBackground  

**Description**

```php
public printBackground (bool $printBackground)
```

Set to true to print backgrounds. 

 

**Parameters**

* `(bool) $printBackground`
: Set to true to print backgrounds  

**Return Values**

`\PDFGenerator`




<hr />


### PDFGenerator::setContent  

**Description**

```php
public setContent (string $content)
```

Sets the content. 

 

**Parameters**

* `(string) $content`
: the HTML content to take a screenshot of  

**Return Values**

`\PDFGenerator`




<hr />


### PDFGenerator::setFilename  

**Description**

```php
public setFilename (string $filename)
```

Sets the filename. 

 

**Parameters**

* `(string) $filename`
: Set the filename for the output. Only extensions .pdf and .png are allowed.  

**Return Values**

`\PDFGenerator`




<hr />


### PDFGenerator::setFooterTemplate  

**Description**

```php
public setFooterTemplate (string $html)
```

Sets the Footer template. 

Should be valid HTML markup with following classes used to inject printing values into them:  
- date          formatted print date  
- title         document title  
- url           document location  
- pageNumber    current page number  
- totalPages    total pages in the document 

**Parameters**

* `(string) $html`
: HTML Markup  

**Return Values**

`\PDFGenerator`




<hr />


### PDFGenerator::setFormat  

**Description**

```php
public setFormat (void)
```

Sets the format of the page. 

The format options are:  
- Letter: 8.5in x 11in  
- Legal: 8.5in x 14in  
- Tabloid: 11in x 17in  
- Ledger: 17in x 11in  
- A0: 33.1in x 46.8in  
- A1: 23.4in x 33.1in  
- A2: 16.54in x 23.4in  
- A3: 11.7in x 16.54in  
- A4: 8.27in x 11.7in  
- A5: 5.83in x 8.27in  
- A6: 4.13in x 5.83in 

**Parameters**

`This function has no parameters.`

**Return Values**

`\PDFGenerator`




<hr />


### PDFGenerator::setHeaderTemplate  

**Description**

```php
public setHeaderTemplate (string $html)
```

Sets the Header template. 

Should be valid HTML markup with following classes used to inject printing values into them:  
- date          formatted print date  
- title         document title  
- url           document location  
- pageNumber    current page number  
- totalPages    total pages in the document 

**Parameters**

* `(string) $html`
: HTML Markup  

**Return Values**

`\PDFGenerator`




<hr />


### PDFGenerator::setHttpAuthentication  

**Description**

```php
public setHttpAuthentication (void)
```

Sets the HTTPAuthentication username and password. 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`\PDFGenerator`




<hr />


### PDFGenerator::setMargins  

**Description**

```php
public setMargins (string $margins, string $right, string $bottom, string $left)
```

Sets the margins for the page. 

 

**Parameters**

* `(string) $margins`
: Top margin, or all margins  
* `(string) $right`
: Right margin  
* `(string) $bottom`
: bottom margin  
* `(string) $left`
: Left margin  

**Return Values**

`\PDFGenerator`

> return this instance


<hr />


### PDFGenerator::setOrientation  

**Description**

```php
public setOrientation (string $orientation)
```

Set the page orientation. 

 

**Parameters**

* `(string) $orientation`
: PDFGenerator::ORIENTATION_LANDSCAPE or PDFGenerator::ORIENTATION_PORTRAIT  

**Return Values**

`\PDFGenerator`




<hr />


### PDFGenerator::setURL  

**Description**

```php
public setURL (string $url)
```

Set the URL. 

 

**Parameters**

* `(string) $url`
: the URL of the page to take a screenshot of  

**Return Values**

`\PDFGenerator`




<hr />


### PDFGenerator::setViewportSize  

**Description**

```php
public setViewportSize (void)
```

Sets the viewport height and viewport width. 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`\PDFGenerator`




<hr />





