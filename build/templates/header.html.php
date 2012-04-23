<?php return <<<TEMPLATE
<!DOCTYPE html>
<html>
<head>
<title>Luracast Restler $version Live Examples:- $title</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
{$_(repeat_foreach, $styles,'
<link rel="stylesheet" type="text/css" href="$link_prefix$value" />
')} {$_(repeat_foreach, $scripts,'
<script type="text/javascript" src="$link_prefix$value"></script>
')}
<script type="text/javascript">
	$(document).ready(
			function() {
				//$('a[target*=_blank]').facebox();
				$("abbr").click(
						function() {
							$('#codeviewer').load(
									'{$link_prefix}resources/getsource.php?file='
											+ $(this).attr("title"),
									function() {
										$("pre#php").snippet("php", {
											style : "acid",
											showNum : false
										});
										jQuery.facebox({
											div : '#codeviewer'
										});
									});
						});
				var curURL = window.location.pathname.split('/examples/')[1];
				if (curURL == '')
					curURL = 'index.html';
				$("a").each(function() {
					if ($(this).attr("href").indexOf(curURL) > -1) {
						$(this).addClass("active");
					}
				});

				$('#right tag').popover({
					html : true,
					placement : 'left',
					trigger : 'manual',
					title : 'Tagged Examples',
					content : '...'
				}).click(function(e) {
					$('#right tag').popover('hide');
					$(this).popover('show');
					e.preventDefault();
					e.stopPropagation ? e.stopPropagation() : e.cancelBubble = true;
				});

				$(document).click(function(e) {
					$('#right tag').popover('hide');
				});
			})
</script>
{$_('require','fixie.html.php')}
</head>
<body>
	{$_('require','menu.html.php')}
	<div id="codeviewer" style="display: none;"></div>
	<nav id="left">
		<img src="{$link_prefix}resources/Restler3.gif" width="126"
			height="126" title="Luracast Restler $version" />
		<h3>
			<a href="$link_prefix$main->href">$main->name</a>
		</h3>
		<ul>
			{$_('repeat_foreach', $links, '
			<li><a href="$link_prefix$value->href" title="$value->tagline">$value->name</a></li>')}
		</ul>
	</nav>
	<nav id="right">
		<h3><a href="$link_prefix$main->href">Examples by Tag</a></h3>
		<ul class="tags">$main->tagStr</ul>
	</nav>
	<article id="page">
TEMPLATE;
