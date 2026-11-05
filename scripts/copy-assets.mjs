import { mkdir, copyFile } from 'node:fs/promises';
await mkdir('public/vendor/bootstrap', { recursive: true });
for (const [from, to] of [
  ['dist/css/bootstrap.min.css', 'bootstrap.min.css'],
  ['dist/js/bootstrap.bundle.min.js', 'bootstrap.bundle.min.js'],
  ['LICENSE', 'LICENSE'],
]) await copyFile(`node_modules/bootstrap/${from}`, `public/vendor/bootstrap/${to}`);
console.log('Bootstrap assets copied locally.');