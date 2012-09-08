<?php
return <<<TEMPLATE
{$_('require','header.html.php')}
{$_('Markdown',$_('require','index.md'))}<ol class="toc">
{$_('repeat_foreach', $links, ' <li><a href="$link_prefix$value->href" title="$value->description">
<span>$value->name
<small><i>$value->tagline</i></small>
</span> <span class="right">&nbsp;$count</span></a></li>
')}
</ol>
<p> </p>
<p> <img src="resources/restler_flow.png" alt="Restler Flow Diagram" title="Restler Flow" width="100%">
<strong>Restler Execution Flow</strong>
</p>
<p> </p>
<p> </p>
<p> </p>
{$_('require','footer.html.php')}
TEMPLATE;

