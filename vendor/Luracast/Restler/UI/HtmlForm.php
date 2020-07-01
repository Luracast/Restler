<?php


namespace Luracast\Restler\UI;


class HtmlForm
{
    const form = 'form[role=form id=$id# name=$name# method=$method# action=$action# enctype=$enctype#]';
    const input = '.row>section>label{$label#}^input[id=$id# name=$name# value=$value# type=$type# required=$required# autofocus=$autofocus# placeholder=$default# accept=$accept# disabled=$disabled#]';
    const password = self::input;
    const textarea = '.row>label{$label#}^textarea[id=$id# name=$name# required=$required# autofocus=$autofocus# placeholder=$default# rows=3 disabled=$disabled#]{$value#}';
    const radio = '.row>section>label{$label#}^span>label*options>input[id=$id# name=$name# value=$value# type=radio checked=$selected# required=$required# disabled=$disabled#]+{ $text#}';
    const select = '.row>label{$label#}^select[id=$id# name=$name# required=$required#]>option[value]+option[value=$value# selected=$selected# disabled=$disabled#]{$text#}*options';
    const submit = '.row>label{ &nbsp; }^button[id=$id# type=submit disabled=$disabled#]{$label#}';
    const fieldset = 'fieldset>legend{$label#}';
    const checkbox = '.row>label>input[id=$id# name=$name# value=$value# type=checkbox checked=$selected# required=$required# autofocus=$autofocus# accept=$accept# disabled=$disabled#]+{$label#}';
    //------------- TYPE BASED STYLES ---------------------//
    const checkbox_array = 'fieldset>legend{$label#}+section*options>label>input[name=$name# value=$value# type=checkbox checked=$selected# required=$required# autofocus=$autofocus# accept=$accept#]+{ $text#}';
    const select_array = 'label{$label#}+select[name=$name# required=$required# multiple style="height: auto;background-image: none; outline: inherit;"]>option[value=$value# selected=$selected#]{$text#}*options';
}
