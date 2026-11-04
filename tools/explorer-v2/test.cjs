'use strict';
const fs = require('node:fs');
const path = require('node:path');
const assert = require('node:assert/strict');
const {JSDOM, VirtualConsole} = require('jsdom');
const client = path.resolve(__dirname, '../../src/Explorer/v2/client');
const index = fs.readFileSync(path.join(client, 'index.html'), 'utf8');
const runtime = index.match(/src='(lib\/handlebars[^']+)'/)[1];
const dom = new JSDOM('<div id="header"><input id="input_baseUrl"><input id="input_apiKey"></div><div id="swagger-ui-container"></div>', {url:'http://localhost/explorer/', runScripts:'outside-only', virtualConsole: new VirtualConsole()});
const w = dom.window;
for (const file of ['lib/jquery-1.8.0.min.js', 'lib/jquery.slideto.min.js', 'lib/jquery.wiggle.min.js', 'lib/jquery.ba-bbq.min.js', 'lib/underscore-min.js', 'lib/backbone-min.js', runtime, 'lib/highlight.7.3.pack.js', 'lib/jsoneditor.min.js', 'lib/marked.js', 'swagger-ui.js']) {
  w.eval(fs.readFileSync(path.join(client, file), 'utf8'));
}
new w.SwaggerUi.Views.ParameterView({model:{}}); // Registers the production isArray helper.
const common = {name:'search', valueId:'query-value', keyName:'API key', type:'string', schema:{type:'string'}, default:'example', paramType:'query', description:'<em>Description</em>', produces:['application/json','text/plain'], consumes:['application/json'], contentTypeId:'type', parameterContentTypeId:'parameter-type', responseContentTypeId:'response-type', basePath:'/api', url:'http://localhost/swagger.json', info:{title:'Sample API',description:'<p>Docs</p>',version:'1.0', contact:{name:'Developer',email:'dev@example.com'},license:{name:'MIT',url:'/license'}}, id:'pets', summary:'<b>Pets</b>', method:'get', parentId:'pets', encodedParentId:'pets', nickname:'findPets', path:'/pets', parameters:[{}], responseMessages:[{}], signature:'<b>Pet</b>',sampleJSON:'{"name":"cat"}',code:200,message:'<em>OK</em>',headers:{'X-Count':{description:'Count',type:'integer'}},allowableValues:{descriptiveValues:[{value:'cat',isDefault:true},{value:'dog'}]}};
const cases = [];
for (const name of Object.keys(w.Handlebars.templates).sort()) {
  cases.push({name, label:name, data:common});
  if (name.startsWith('param')) {
    cases.push({name, label:name+'-body', data:{...common,isBody:true,required:true}});
    cases.push({name, label:name+'-array', data:{...common,type:'array',default:['cat','dog'],required:false}});
    cases.push({name, label:name+'-file', data:{...common,isFile:true,default:''}});
  }
  if (name.includes('content_type')) cases.push({name,label:name+'-fallback',data:{...common,produces:null,consumes:null}});
}
const output = Object.fromEntries(cases.map(c => [c.label,w.Handlebars.templates[c.name](c.data)]));
const snapshot = path.join(__dirname,'expected-html.json');
if (process.argv.includes('--record')) {
  assert.equal(w.Handlebars.VERSION,'2.0.0','Only record the original upstream-compatible baseline');
  fs.writeFileSync(snapshot,JSON.stringify(output,null,2)+'\n');
  console.log(`Recorded ${cases.length} original template outputs`);
  w.close();
  process.exit(0);
}
assert.equal(w.Handlebars.VERSION,'4.7.10');
assert.equal(typeof w.Handlebars.compile,'undefined','Ship runtime only');
assert.deepEqual(output,JSON.parse(fs.readFileSync(snapshot,'utf8')));
console.log(`PASS ${cases.length} legacy template output comparisons`);
const inherited = Object.create({keyName:'INHERITED-MARKER'});
assert(!w.Handlebars.templates.apikey_button_view(inherited).includes('INHERITED-MARKER'));
let called=false;
const methods=Object.create({keyName(){called=true;return 'INHERITED-METHOD';}});
assert(!w.Handlebars.templates.apikey_button_view(methods).includes('INHERITED-METHOD'));
assert.equal(called,false);
assert.throws(()=>w.Handlebars.template({compiler:[6,'legacy'],main(){return 'unsafe';}}));
assert(w.Handlebars.templates.apikey_button_view({keyName:'<img src=x onerror=bad()>'}).includes('&lt;img'));
console.log('PASS inherited properties/methods blocked, escaping and compiler checks retained');
// Render the actual legacy Swagger UI from a local in-memory document. No network.
const spec={swagger:'2.0',info:{title:'Local fixture',version:'1.0'},host:'localhost',basePath:'/api',schemes:['http'],paths:{'/pets':{get:{tags:['pets'],operationId:'findPets',summary:'List pets',parameters:[{name:'search',in:'query',type:'string',required:true},{name:'kind',in:'query',type:'array',items:{type:'string'},enum:['cat','dog']}],responses:{200:{description:'OK',schema:{type:'array',items:{$ref:'#/definitions/Pet'}}}}}}},definitions:{Pet:{type:'object',properties:{name:{type:'string'}}}}};
(async()=>{
  const ui=new w.SwaggerUi({url:'http://localhost/swagger.json',spec,dom_id:'swagger-ui-container',validatorUrl:null,onFailure:error=>{throw new Error(String(error));}});
  ui.load();
  for(let i=0;i<100 && !w.document.querySelector('.operation');i++) await new Promise(r=>setTimeout(r,10));
  assert(w.document.querySelector('#api_info').textContent.includes('Local fixture'));
  assert(w.document.querySelector('.operation'), 'Operation rendered');
  assert(w.document.querySelector('input[name="search"]'), 'Required input rendered');
  assert(w.document.querySelector('select[name="kind"][multiple]'), 'Array enumeration rendered');
  w.$.fx.off = true;
  assert.equal(w.document.querySelector('.operation .content').style.display, 'none');
  w.$('.toggleEndpointList').first().trigger('click');
  w.$('.toggleOperation').first().trigger('click');
  assert.notEqual(w.document.querySelector('.operation .content').style.display, 'none', 'Operation expands');
  // JSDOM has no layout; jQuery :visible cannot drive the second toggle click.
  w.Docs.collapseOperation(w.$('.operation .content'));
  assert.equal(w.document.querySelector('.operation .content').style.display, 'none', 'Operation collapses');
  console.log('PASS full Swagger UI DOM rendering, controls and expansion');
  w.close();
})().catch(error=>{w.close();console.error(error);process.exitCode=1;});
