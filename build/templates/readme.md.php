<?php
return <<<TEMPLATE
$title <requires>$requires</requires>
{$_('dash',$title)}
{$_('repeat_foreach', $tags, '
<tag>$value</tag>')}
{$_('pad','
',$summary,'.')}{$_('pad','
',$description,'
')}> This API Server is made using the following php files/folders
{$_('repeat_foreach', $local_files,'
> * $key      ($value->type)')}{$_('repeat_foreach', $restler_files,'
> * $key      ($value->type)')}
{$_('if',$routes,'
This API Server exposes the following URIs

')}{$_('repeat_foreach', $routes, ' $value->httpMethod $value->url{$_(space,$routes_max-$_(strlen,$value->httpMethod.$value->url))}⇠ $value->className::$value->methodName()
')}{$_('if',$examples,'

Try the following links in your browser

')}{$_('repeat_foreach', $examples, '$value->httpMethod [$key]($key)
:
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
$value->result
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

')}{$_('if',$examples,'
If the above links fail, it could be due to missing `.htaccess` file or URL Rewriting is not supported in your server.
Try the following links instead

')}{$_('repeat_foreach', $examples, '$value->httpMethod [index.php/$key](index.php/$key)
:
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
$value->result
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

')}

{$_('repeat_foreach', $local_files, '*[$key]: $value->path
')}{$_('repeat_foreach', $restler_files, '*[$key]: $value->path
')}
TEMPLATE;

