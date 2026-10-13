const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

execSync('node build-cms-bootstrap.cjs', { stdio: 'inherit' });
execSync('node build-cms-site.cjs', { stdio: 'inherit' });

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
