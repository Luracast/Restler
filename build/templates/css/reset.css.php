<?php
return <<<TEMPLATE
* { outline: 0; }
html, body { min-height: 100%;  }
body, ul, ol, dl { margin: 0; }
img { border: 0; }
article, aside, audio, footer, header, nav, section, video { display: block }
input[type="submit"]::-moz-focus-inner, input[type="button"]::-moz-focus-inner { border : 0px; }
input[type="search"] { -webkit-appearance: textfield; }
input[type="submit"] { -webkit-appearance:none; }
img.right { float: right; margin-left: 2em; clear: right; }
img.left { float: left; margin-right: 2em; clear: left; }
table { border-collapse: collapse; }
th { background: #000; color: #fff; }
td { padding: 1em; border: 1px solid black; }
TEMPLATE;

