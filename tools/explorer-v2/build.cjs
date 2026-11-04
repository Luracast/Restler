'use strict';
const fs = require('node:fs');
const path = require('node:path');
const Handlebars = require('handlebars');
const client = path.resolve(__dirname, '../../src/Explorer/v2/client');
const bundlePath = path.join(client, 'swagger-ui.js');
const sources = fs.readdirSync(path.join(__dirname, 'templates')).filter(f => f.endsWith('.handlebars'));
const used = new Set();
const spans = require('./template-spans.cjs');
const original = fs.readFileSync(bundlePath, 'utf8');
let output = original;
for (const span of spans(original).reverse()) {
  const {name,start,end} = span;
  const filename = name + '.handlebars';
  if (!sources.includes(filename) || used.has(name)) throw new Error('Unexpected template ' + name);
  used.add(name);
  const compiled = Handlebars.precompile(fs.readFileSync(path.join(__dirname, 'templates', filename), 'utf8')).replace(/[ \t]+$/gm,'');
  const replacement = `this["Handlebars"]["templates"][${JSON.stringify(name)}] = Handlebars.template(${compiled});`;
  output = output.slice(0,start) + replacement + output.slice(end);
}
if (used.size !== 15 || used.size !== sources.length) throw new Error('Incomplete template replacement');
fs.writeFileSync(bundlePath, output);
const runtime = `handlebars.runtime-${Handlebars.VERSION}.js`;
fs.copyFileSync(require.resolve('handlebars/dist/handlebars.runtime.js'), path.join(client, 'lib', runtime));
const indexPath = path.join(client, 'index.html');
fs.writeFileSync(indexPath, fs.readFileSync(indexPath, 'utf8').replace(/lib\/handlebars(?:\.runtime)?-[\d.]+\.js/g, 'lib/' + runtime));
const old = path.join(client, 'lib/handlebars-2.0.0.js');
if (fs.existsSync(old)) fs.unlinkSync(old);
console.log(`Rebuilt ${used.size} templates with Handlebars ${Handlebars.VERSION}`);
