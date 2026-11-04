'use strict';
const acorn=require('../explorer-v2/node_modules/acorn');
module.exports=function spans(source,names){
 const result=[];
 function visit(node){
  if(!node||typeof node!=='object')return;
  if(node.type==='AssignmentExpression'&&node.left.type==='MemberExpression'&&node.right.type==='CallExpression'){
   const name=node.left.computed?node.left.property.value:node.left.property.name;
   const arg=node.right.arguments[0];
   if(names.includes(name)&&arg&&['FunctionExpression','ObjectExpression'].includes(arg.type)){
    result.push({name,start:node.right.start,end:node.right.end,argStart:arg.start,argEnd:arg.end});return;
   }
  }
  for(const value of Object.values(node)){if(Array.isArray(value))value.forEach(visit);else if(value&&typeof value==='object')visit(value);}
 }
 visit(acorn.parse(source,{ecmaVersion:2020}));
 return result.sort((a,b)=>a.start-b.start);
};
