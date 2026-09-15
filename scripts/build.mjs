import { build } from 'esbuild';
import { mkdirSync, readFileSync, writeFileSync } from 'node:fs';
import { execFileSync } from 'node:child_process';

mkdirSync('public/assets', { recursive: true });
await build({ entryPoints: ['resources/js/app.js'], bundle: true, minify: true,
    format: 'esm', target: ['es2020'], outfile: 'public/assets/app.js', legalComments: 'eof' });
writeFileSync('public/assets/app.js', readFileSync('public/assets/app.js', 'utf8').replace(/[ \t]+$/gm, ''));
execFileSync(process.execPath, ['node_modules/@tailwindcss/cli/dist/index.mjs',
    '-i', 'resources/css/app.css', '-o', 'public/assets/app.css', '--minify'], { stdio: 'inherit' });
