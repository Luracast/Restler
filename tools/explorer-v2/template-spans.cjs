'use strict';
const acorn = require('acorn');
module.exports = function templateSpans(source) {
  const result = [];
  function visit(node) {
    if (!node || typeof node !== 'object') return;
    const e = node.type === 'ExpressionStatement' && node.expression;
    if (e && e.type === 'AssignmentExpression' && e.right.type === 'CallExpression' && source.slice(e.right.callee.start,e.right.callee.end) === 'Handlebars.template' && e.left.type === 'MemberExpression' && source.slice(e.left.object.start,e.left.object.end) === 'this["Handlebars"]["templates"]') {
      result.push({name:e.left.property.value,start:node.start,end:node.end});
      return;
    }
    for (const value of Object.values(node)) {
      if (Array.isArray(value)) value.forEach(visit);
      else if (value && typeof value === 'object') visit(value);
    }
  }
  visit(acorn.parse(source,{ecmaVersion:2020}));
  return result.sort((a,b)=>a.start-b.start);
};
