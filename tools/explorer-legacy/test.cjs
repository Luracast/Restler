'use strict';
const fs=require('node:fs'),path=require('node:path'),vm=require('node:vm'),assert=require('node:assert/strict');
const compiler=require('../explorer-v2/node_modules/handlebars');
const spans=require('./template-spans.cjs'),configs=require('./config.cjs');
const root=path.resolve(__dirname,'../..');
const common={name:'search',defaultValue:'example',type:'string',dataType:'string',paramType:'query',description:'<em>Description</em>',produces:['application/json','text/plain'],consumes:['application/json'],basePath:'/api',apiVersion:'1.0',info:{title:'Sample API',description:'<p>Docs</p>',contact:'dev@example.com',license:'MIT',licenseUrl:'/license',termsOfServiceUrl:'/terms'},id:'pets',url:'/pets',method:'get',httpMethod:'get',parentId:'pets',resourceName:'pets',nickname:'findPets',path:'/pets',notes:'<p>Notes</p>',summary:'List pets',parameters:[{}],responseMessages:[{}],responseClass:'Pet',responseClassSignature:'<b>Pet</b>',signature:'<b>Pet</b>',sampleJSON:'{"name":"cat"}',code:200,message:'<em>OK</em>',allowableValues:{descriptiveValues:[{value:'cat',isDefault:true},{value:'dog'}]}};
function helper(param,opts){return (param.type==='array'||param.allowMultiple)?opts.fn(this):opts.inverse(this);}
for(const [id,config] of Object.entries(configs)){
 const dir=path.join(__dirname,id),client=path.join(root,config.root);
 const names=fs.readdirSync(path.join(dir,'templates')).map(x=>x.replace('.handlebars','')).sort();
 const index=fs.readFileSync(path.join(client,'index.html'),'utf8');
 const runtime=index.match(/src='lib\/(handlebars[^']+)'/)[1];
 for(const bundleName of ['swagger-ui.js','swagger-ui.min.js']){
  const source=fs.readFileSync(path.join(client,bundleName),'utf8'),nodes=spans(source,names);
  assert.equal(nodes.length,config.count);
  const context=vm.createContext({});vm.runInContext(fs.readFileSync(path.join(client,'lib',runtime),'utf8'),context);
  context.Handlebars.templates={};context.Handlebars.registerHelper('isArray',helper);
  for(const node of nodes)vm.runInContext(`Handlebars.templates[${JSON.stringify(node.name)}]=Handlebars.template(${source.slice(node.argStart,node.argEnd)});`,context);
  const cases=[];
  for(const name of names){
   for(const [variant,data] of [['default',common],['empty', {...common,defaultValue:'',info:null,produces:null,consumes:null}],['body',{...common,isBody:true,required:true}],['file',{...common,isFile:true}],['array',{...common,type:'array',allowMultiple:true,required:true}],['readonly',{...common,isReadOnly:true}],['no-default-body',{...common,isBody:true,defaultValue:''}],['body-file',{...common,isBody:true,isFile:true}],['oauth',{...common,oauth:{scopes:[{scope:'read',description:'Read pets'}]}}],['minimal',{...common,info:{},apiVersion:'',description:'',notes:'',responseMessages:null,parameters:null,type:null,responseClass:null,responseClassSignature:null}]])cases.push({name,label:name+'-'+variant,data});
  }
  const actual=Object.fromEntries(cases.map(c=>[c.label,context.Handlebars.templates[c.name](c.data)]));
  const baselineFile=path.join(dir,'expected-html.json');
  if(process.argv.includes('--record')){
   assert.equal(runtime,config.old,'Only record against the original library filename');fs.writeFileSync(baselineFile,JSON.stringify(actual,null,2)+'\n');
   console.log(`Recorded ${id} ${bundleName} ${cases.length} cases`);
  }else if(process.argv.includes('--reconcile')){
   const next=compiler.create();next.registerHelper('isArray',helper);let different=0;
   for(const c of cases){const output=next.compile(fs.readFileSync(path.join(dir,'templates',c.name+'.handlebars'),'utf8'),{ignoreStandalone:true})(c.data);if(output!==actual[c.label]){different++;if(c.label.endsWith('-default')){const i=[...output].findIndex((x,i)=>x!==actual[c.label][i]);console.log(id,c.name,'DIFFERENCE at',i,'OLD',JSON.stringify(actual[c.label].slice(Math.max(0,i-50),i+500)),'NEW',JSON.stringify(output.slice(Math.max(0,i-50),i+500)));}}}
   console.log(id,bundleName,different,'source cases differ');
  }else{
   assert.equal(context.Handlebars.VERSION,'4.7.10');assert.deepEqual(actual,JSON.parse(fs.readFileSync(baselineFile,'utf8')));
   assert.equal(typeof context.Handlebars.compile,'undefined');
   let invoked=false;const inherited=Object.create({name(){invoked=true;return 'INHERITED-MARKER';}});
   const out=context.Handlebars.templates.param_readonly(inherited);assert(!out.includes('INHERITED-MARKER'));assert(!invoked);
   assert(context.Handlebars.templates.param_readonly({...common,name:'<script>bad()</script>'}).includes('&lt;script&gt;'));
   assert.throws(()=>context.Handlebars.template({compiler:[4,'legacy'],main(){return 'bad';}}));
   console.log(`PASS ${id} ${bundleName}: ${cases.length} output cases and prototype/escaping/revision checks`);
  }
 }
}
