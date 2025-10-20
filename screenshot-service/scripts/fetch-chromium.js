const fs = require('fs');
const path = require('path');
const https = require('https');

const version = process.env.npm_package_config_chromium_version || process.env.CHROMIUM_VERSION || 'v141.0.0';
const arch = process.env.npm_package_config_chromium_arch || process.env.CHROMIUM_ARCH || 'x64';
const zipName = `chromium-${version}-layer.${arch}.zip`;
const url = `https://github.com/Sparticuz/chromium/releases/download/${version}/${zipName}`;
const outDir = path.join('.serverless');
const outPath = path.join(outDir, zipName);

fs.mkdirSync(outDir, { recursive: true });

function download(url, dest) {
  return new Promise((resolve, reject) => {
    const file = fs.createWriteStream(dest);
    const opts = { headers: { 'User-Agent': 'serverless-downloader' } };

    const handle = (u) => https.get(u, opts, res => {
      if (res.statusCode >= 300 && res.statusCode < 400 && res.headers.location) {
        // follow redirect
        res.resume();
        return handle(res.headers.location);
      }
      if (res.statusCode !== 200) {
        res.resume();
        return reject(new Error(`HTTP ${res.statusCode} for ${u}`));
      }
      res.pipe(file);
      file.on('finish', () => file.close(resolve));
    }).on('error', reject);

    handle(url);
  });
}

(async () => {
  if (fs.existsSync(outPath)) {
    console.log(`✔ Found ${outPath}, skipping download`);
    process.exit(0);
  }
  console.log(`↓ Downloading ${url}`);
  try {
    await download(url, outPath);
    console.log(`✔ Saved to ${outPath}`);
  } catch (e) {
    console.error('Download failed:', e.message);
    process.exit(1);
  }
})();
