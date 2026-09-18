import { build } from 'esbuild';
import { cpSync, copyFileSync, mkdirSync, readFileSync, writeFileSync } from 'node:fs';
import { execFileSync } from 'node:child_process';

mkdirSync('public/assets', { recursive: true });
await build({ entryPoints: ['resources/js/app.js'], bundle: true, minify: true,
    format: 'esm', target: ['es2020'], outfile: 'public/assets/app.js', legalComments: 'eof' });
await build({ entryPoints: ['resources/js/legal-certificate-reader.js'], bundle: true, minify: true,
    format: 'esm', target: ['es2020'], outfile: 'public/assets/legal-certificate-reader.js', legalComments: 'eof' });
copyFileSync('node_modules/pdfjs-dist/build/pdf.worker.mjs', 'public/assets/pdf.worker.mjs');
mkdirSync('public/assets/tesseract/core', { recursive: true });
mkdirSync('public/assets/tesseract/lang', { recursive: true });
copyFileSync('node_modules/tesseract.js/dist/worker.min.js', 'public/assets/tesseract/worker.min.js');
cpSync('node_modules/tesseract.js-core', 'public/assets/tesseract/core', {
    recursive: true,
    filter: (source) => !source.endsWith('README.md') && !source.endsWith('package.json') && !source.endsWith('LICENSE'),
});
copyFileSync('node_modules/@tesseract.js-data/spa/4.0.0_best_int/spa.traineddata.gz',
    'public/assets/tesseract/lang/spa.traineddata.gz');
copyFileSync('node_modules/@tesseract.js-data/eng/4.0.0_best_int/eng.traineddata.gz',
    'public/assets/tesseract/lang/eng.traineddata.gz');
writeFileSync('public/assets/app.js', readFileSync('public/assets/app.js', 'utf8').replace(/[ \t]+$/gm, ''));
execFileSync(process.execPath, ['node_modules/@tailwindcss/cli/dist/index.mjs',
    '-i', 'resources/css/app.css', '-o', 'public/assets/app.css', '--minify'], { stdio: 'inherit' });
