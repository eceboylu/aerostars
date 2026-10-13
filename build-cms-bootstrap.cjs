const fs = require('fs');
const path = require('path');
const postcss = require('postcss');
const cmsConfig = require('./postcss.cms.config.cjs');

const input = path.resolve(__dirname, 'node_modules/bootstrap/dist/css/bootstrap.css');
const output = path.resolve(__dirname, 'resources/css/cms-bootstrap.css');

const css = fs.readFileSync(input, 'utf8');

postcss(cmsConfig.plugins)
    .process(css, { from: input, to: output })
    .then((result) => {
        fs.writeFileSync(output, result.css);
        console.log(`[cms-bootstrap] wrote ${output}`);
    });
