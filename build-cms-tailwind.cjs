const fs = require('fs');
const path = require('path');
const postcss = require('postcss');
const { compile } = require('@tailwindcss/node');
const { Scanner } = require('@tailwindcss/oxide');
const cmsConfig = require('./postcss.cms.config.cjs');

const ENTRY_CSS = `
@layer theme, utilities;
@import "tailwindcss/theme.css" layer(theme);
@import "tailwindcss/utilities.css" layer(utilities);
`;

const sourceFile = path.resolve(__dirname, 'storage/app/cms/tailwind-source.html');
const output = path.resolve(__dirname, 'public/cms/cms-tailwind.css');

async function main() {
    const html = fs.existsSync(sourceFile) ? fs.readFileSync(sourceFile, 'utf8') : '';

    const compiler = await compile(ENTRY_CSS, {
        base: __dirname,
        onDependency: () => {},
    });

    const scanner = new Scanner({ sources: [] });
    const candidates = scanner.scanFiles([{ content: html, extension: 'html' }]);

    const rawCss = compiler.build(candidates);

    const scoped = await postcss(cmsConfig.plugins).process(rawCss, { from: undefined });

    fs.mkdirSync(path.dirname(output), { recursive: true });
    fs.writeFileSync(output, scoped.css);
    console.log(`[cms-tailwind] wrote ${output} (${candidates.length} candidates)`);
}

main().catch((error) => {
    console.error(error);
    process.exit(1);
});
