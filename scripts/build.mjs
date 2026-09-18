import { build } from 'esbuild';
import { copyFileSync, mkdirSync, readFileSync, writeFileSync } from 'node:fs';
import { execFileSync } from 'node:child_process';

mkdirSync('public/assets', { recursive: true });
await build({ entryPoints: ['resources/js/app.js'], bundle: true, minify: true,
    format: 'esm', target: ['es2020'], outfile: 'public/assets/app.js', legalComments: 'eof' });
await build({ entryPoints: ['resources/js/legal-certificate-reader.js'], bundle: true, minify: true,
    format: 'esm', target: ['es2020'], outfile: 'public/assets/legal-certificate-reader.js', legalComments: 'eof' });
copyFileSync('node_modules/pdfjs-dist/build/pdf.worker.mjs', 'public/assets/pdf.worker.mjs');
writeFileSync('public/assets/app.js', readFileSync('public/assets/app.js', 'utf8').replace(/[ \t]+$/gm, ''));
execFileSync(process.execPath, ['node_modules/@tailwindcss/cli/dist/index.mjs',
    '-i', 'resources/css/app.css', '-o', 'public/assets/app.css', '--minify'], { stdio: 'inherit' });
