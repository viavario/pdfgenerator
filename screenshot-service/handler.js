const puppeteer = require("puppeteer-core");
const chrome = require("@sparticuz/chrome-aws-lambda");
const merge = require("lodash.merge");

/**
 * Get and parse the POST body
 * @return  Object
 */
function getBody(event) {
    let body = event.httpMethod === 'POST' ? (Buffer.from(event.body, 'base64')).toString('utf-8') : null;
    let {queryStringParameters} = event;
    if (!queryStringParameters) {
        queryStringParameters = {};
    }

    let def = {
        pdfOptions: {
            scale: 1,
            displayHeaderFooter: false,
            headerTemplate: '', // date, title, url, pageNumber, totalPages
            footerTemplate: '', // date, title, url, pageNumber, totalPages
            printBackground: true,
            landscape: false,
            format: 'A4',
            margin: {
                top: '2cm',
                bottom: '3cm',
                left: '1.6cm',
                right: '1.6cm'
            },
            preferCSSPageSize: false,
            omitBackground: false,
        },
        content: '',
        url: queryStringParameters.url,
        filename: queryStringParameters.filename || 'file.pdf',
        width: queryStringParameters.width || 1200,
        height: queryStringParameters.height || 768,
        username: queryStringParameters.username,
        password: queryStringParameters.password
    };

    try {
        return merge(def, JSON.parse(body));
    } catch (e) {
        return false;
    }
}

const capture = async (event) => {
    let options = getBody(event);
    if (options === false) {
        return { statusCode: 403, body: 'Invalid JSON body' };
    }

    if (!options.content && !options.url) {
        return { statusCode: 403, body: 'Missing url or content' };
    }

    // Launch the browser
    const browser = await puppeteer.launch({
        executablePath: await chrome.executablePath,
        args: chrome.args,
        headless: chrome.headless,
        ignoreHTTPSErrors: true,
        defaultViewport: chrome.defaultViewport
    });

    // Open a new tab and set the size of the viewport
    const page = await browser.newPage();
    await page.setViewport({
        width: Number(options.width),
        height: Number(options.height)
    });

    // Wait until everything is loaded properly
    const loaded = page.waitForNavigation({
        waitUntil: ['load', 'domcontentloaded', 'networkidle0']
    });

    // Set the X-AWS-ScreenShot header
    await page.setExtraHTTPHeaders({
        "X-AWS-ScreenShot": "1"
    });

    // Set the username and password for HTTP Authentication
    if (options.username && options.password) {
        await page.setExtraHTTPHeaders({
            'Authorization': 'Basic ' + (Buffer.from(options.username + ':' + options.password)).toString('base64')
        });
    }

    // Set the content or navigate to the url
    if (options.content) {
        await page.setContent(options.content);
    } else {
        await page.goto(options.url);
    }
    await loaded;

    // Set the dimensions of the page in order to get a full page screenshot
    const dimensions = await page.evaluate(() => {
        return {
            width: document.documentElement.scrollWidth,
            height: document.documentElement.scrollHeight,
            deviceScaleFactor: window.devicePixelRatio
        };
    });
    
    await page.setViewport({
        width: Number(dimensions.width),
        height: Number(dimensions.height)
    });

    // Output the screenshot in the desired format
    let extension = options.filename.substr(options.filename.length - 3);
    if (extension === 'pdf') {
        // Output as PDF
        await page.emulateMediaType(null);
        const pdf = await page.pdf(options.pdfOptions);

        await page.close();
        await browser.close();

        return {
            isBase64Encoded: true,
            headers: {
                "Content-type": "application/pdf",
                "Accept-Ranges": "bytes",
                'Access-Control-Allow-Origin': '*',
                "Content-Disposition": `attachment; filename="${options.filename}"`
            },
            body: pdf.toString('base64')
        };
    } else if (extension === 'png') {
        // Output as PNG
        const screenshot = await page.screenshot({
            encoding: "base64",
            fullPage: true
        });

        await page.close();
        await browser.close();

        return {
            body: screenshot,
            headers: {
                "Content-Type": "image/png",
                'Access-Control-Allow-Origin': '*'
            },
            isBase64Encoded: true
        }
    }

    return { statusCode: 403, body: 'Invalid filename. Only files with extensions .pdf and .png are allowed.' };
}

module.exports = { capture };