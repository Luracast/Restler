<?php


namespace Luracast\Restler\UI;


class Bootstrap3Form extends HtmlForm
{
    const form = 'form[role=form id=$id# name=$name# method=$method# action=$action# enctype=$enctype#]';
    const input = '.form-group.$error#>label{$label#}+input.form-control[id=$id# name=$name# value=$value# type=$type# required=$required# autofocus=$autofocus# placeholder=$default# accept=$accept# disabled=$disabled#]+small.help-block>{$message#}';
    const password = self::input;
    const textarea = '.form-group>label{$label#}+textarea.form-control[id=$id# name=$name# required=$required# autofocus=$autofocus# placeholder=$default# rows=3 disabled=$disabled#]{$value#}+small.help-block>{$message#}';
    const radio = 'fieldset>legend{$label#}>.radio*options>label>input.radio[name=$name# value=$value# type=radio checked=$selected# required=$required# disabled=$disabled#]{$text#}+p.help-block>{$message#}';
    const select = '.form-group>label{$label#}+select.form-control[id=$id# name=$name# multiple=$multiple# required=$required#]>option[value]+option[value=$value# selected=$selected# disabled=$disabled#]{$text#}*options';
    const submit = 'button.btn.btn-primary[id=$id# type=submit]{$label#} disabled=$disabled#';
    const fieldset = 'fieldset>legend{$label#}';
    const checkbox = '.checkbox>label>input[id=$id# name=$name# value=$value# type=checkbox checked=$selected# required=$required# autofocus=$autofocus# disabled=$disabled#]+{$label#}^p.help-block>{$error#}';
    //------------- TYPE BASED STYLES ---------------------//
    const checkbox_array = 'fieldset>legend{$label#}>.checkbox*options>label>input[name=$name# value=$value# type=checkbox checked=$selected# required=$required#]{$text#}';
    const select_array = '.form-group>label{$label#}+select.form-control[name=$name# multiple=$multiple# required=$required#] size=$options#>option[value=$value# selected=$selected#]{$text#}*options';
    //------------- CUSTOM STYLES ---------------------//
    const radio_inline = '.form-group>label{$label# : &nbsp;}+label.radio-inline*options>input.radio[name=$name# value=$value# type=radio checked=$selected# required=$required#]+{$text#}';
}
