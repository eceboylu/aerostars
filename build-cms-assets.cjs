const fs = require('fs');
const path = require('path');
const postcss = require('postcss');
const { execSync } = require('child_process');
const cmsConfig = require('./postcss.cms.config.cjs');

async function scopeCssFile(inputFile, outputFile) {
    const input = path.resolve(__dirname, inputFile);
    const output = path.resolve(__dirname, outputFile);
    const css = fs.readFileSync(input, 'utf8');

    const result = await postcss(cmsConfig.plugins).process(css, { from: input, to: output });

    fs.writeFileSync(output, result.css);
    console.log(`[cms:css] wrote ${outputFile}`);
}

async function main() {
    await scopeCssFile('node_modules/bootstrap/dist/css/bootstrap.css', 'resources/css/cms-bootstrap.css');
    await scopeCssFile('resources/css/site.css', 'resources/css/cms-site.css');

    const tailwindOutput = path.resolve(__dirname, 'public/cms/cms-tailwind.css');

    if (!fs.existsSync(tailwindOutput)) {
        fs.mkdirSync(path.dirname(tailwindOutput), { recursive: true });
        fs.writeFileSync(tailwindOutput, '\n');
    }

    try {
        execSync('php artisan pages:compile-cms-assets', { stdio: 'inherit' });
    } catch (error) {
        console.warn('[cms:css] Tailwind CMS compile skipped, will catch up on next deploy:', error.message);
    }
}

main().catch((error) => {
    console.error(error);
    process.exit(1);
});
