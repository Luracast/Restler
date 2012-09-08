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

')}{$_('repeat_foreach', $routes, ' {$value->spaced_httpMethod}{$value->spaced_url}⇠ $value->className::$value->methodName()
')}{$_('if',$examples,'

Try the following links in your browser

')}{$_('repeat_foreach', $examples, '$value->httpMethod [$key](index.php/$key)
:
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
$value->result
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

')}
$usage

$content
{$_('repeat_foreach', $local_files, '*[$key]: $value->path
')}{$_('repeat_foreach', $restler_files, '*[$key]: $value->path
')}

TEMPLATE;

