<?php
return <<<TEMPLATE
body {
	margin: 0 auto;
	font-family: Georgia, Palatino, serif;
	color: #444444;
	line-height: 1;
	max-width: 960px;
	padding: 0 30px 30px;
	background-color: #E6E2DF;
}

h1,h2,h3,h4 {
	color: #111111;
	font-weight: 400;
}

article h1,article h2,article h3,article h4,article h5,article p {
	margin-bottom: 24px;
	padding: 0;
}

article h1 {
	font-size: 48px;
}

article h2 {
	font-size: 36px;
	margin: 24px 0 6px;
	margin-top: .5em !important;
	border-top: 4px solid #E0E0E0 !important;
	padding-top: .5em !important;
}

article h3 {
	font-size: 24px;
}

article h4 {
	font-size: 21px;
}

article h5 {
	font-size: 18px;
}

a {
	color: #0099ff;
	padding: 0;
	vertical-align: baseline;
	margin: 0;
	text-decoration: none;
}

a:hover,a:active,a:focus {
	background-color: #0099ff;
	text-decoration: none;
	color: white;
}

ul,ol {
	padding: 4px 20px 0px;
	margin: 0;
}

li {
	line-height: 24px;
}

blockquote ul {
	margin: 0 auto;
	padding: 0;
	overflow: hidden;
}

blockquote ul li {
	text-align: left;
	float: left;
	list-style: none;
	padding: 4px;
	width: 220px;
}
TEMPLATE;
