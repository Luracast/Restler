<?php
include_once dirname(dirname(__FILE__)).'/markdown/markdown.php';
return <<<TEMPLATE
{$_('require','header.html.php')}
<div class="right"><small>$count</small></div>
{$_(Markdown,$_('require','readme.md.php'))}
{$_('require','footer.html.php')}
TEMPLATE;

