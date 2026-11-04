'use strict';
const fs=require('node:fs'),path=require('node:path');
const Handlebars=require('../explorer-v2/node_modules/handlebars');
const {minify}=require('../explorer-v2/node_modules/terser');
const spans=require('./template-spans.cjs'),configs=require('./config.cjs');
const root=path.resolve(__dirname,'../..');
(async()=>{
 for(const [id,config] of Object.entries(configs)){
  const client=path.join(root,config.root),dir=path.join(__dirname,id,'templates');
  const names=fs.readdirSync(dir).filter(x=>x.endsWith('.handlebars')).map(x=>x.replace('.handlebars',''));
  for(const filename of ['swagger-ui.js','swagger-ui.min.js']){
   const file=path.join(client,filename),original=fs.readFileSync(file,'utf8'),nodes=spans(original,names);
   if(nodes.length!==config.count||new Set(nodes.map(x=>x.name)).size!==config.count)throw Error('Incomplete template coverage '+id+' '+filename);
   let output=original;
   for(const node of nodes.reverse()){
    const compiled=Handlebars.precompile(fs.readFileSync(path.join(dir,node.name+'.handlebars'),'utf8'),{ignoreStandalone:true}).replace(/[ \t]+$/gm,'');
    let expression=`Handlebars.template(${compiled})`;
    if(filename.endsWith('.min.js'))expression=(await minify(expression,{compress:false,mangle:false,format:{semicolons:false}})).code.trimEnd().replace(/;$/,'');
    output=output.slice(0,node.start)+expression+output.slice(node.end);
   }
   fs.writeFileSync(file,output);
  }
  const runtime=`handlebars.runtime-${Handlebars.VERSION}.js`;
  fs.copyFileSync(require.resolve('../explorer-v2/node_modules/handlebars/dist/handlebars.runtime.js'),path.join(client,'lib',runtime));
  const index=path.join(client,'index.html');
  fs.writeFileSync(index,fs.readFileSync(index,'utf8').replace(/lib\/handlebars(?:\.runtime)?-[\w.]+\.js/g,'lib/'+runtime));
  for(const old of [config.old,...(config.unused||[])]){
   const file=path.join(client,'lib',old);if(fs.existsSync(file))fs.unlinkSync(file);
  }
  console.log(`Rebuilt ${id}: ${config.count} templates in each bundle with ${Handlebars.VERSION}`);
 }
})().catch(error=>{console.error(error);process.exitCode=1;});
