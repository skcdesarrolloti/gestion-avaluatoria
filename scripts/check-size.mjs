import { readdirSync, statSync, readFileSync } from 'node:fs';
import { join } from 'node:path';
import { gzipSync } from 'node:zlib';

let failed = false;
function walk(path) {
    for (const item of readdirSync(path, { withFileTypes: true })) {
        const file = join(path, item.name);
        if (item.isDirectory()) { walk(file); continue; }
        if (!/\.(php|js|css|mjs)$/.test(file)) continue;
        const lines = readFileSync(file, 'utf8').split('\n').length;
        if (lines > 220 || statSync(file).size > 16384) {
            console.error(`Dividir archivo: ${file} (${lines} lineas)`);
            failed = true;
        }
    }
}
for (const dir of ['app', 'resources', 'routes', 'database', 'bin', 'scripts']) walk(dir);
let bytes = 0;
for (const file of ['public/assets/app.js', 'public/assets/app.css']) bytes += gzipSync(readFileSync(file)).length;
console.log(`CSS + JS comprimidos: ${(bytes / 1024).toFixed(1)} KB (limite: 80 KB)`);
if (bytes > 81920) failed = true;
process.exitCode = failed ? 1 : 0;
