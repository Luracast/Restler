<?php


namespace Luracast\Restler\UI;


class Foundation5Form extends HtmlForm
{
    const form = 'form[id=$id# name=$name# method=$method# action=$action# enctype=$enctype#]';
    const input = 'label{$label#}+input[id=$id# name=$name# value=$value# type=$type# required=$required# autofocus=$autofocus# placeholder=$default# accept=$accept# disabled=$disabled#]';
    const password = self::input;
    const textarea = 'label{$label#}+textarea[id=$id# name=$name# required=$required# autofocus=$autofocus# placeholder=$default# rows=3 disabled=$disabled#]{$value#}';
    const radio = 'label{$label# : &nbsp;}+label.radio-inline*options>input.radio[name=$name# value=$value# type=radio checked=$selected# required=$required# disabled=$disabled#]+{$text#}';
    const select = 'label{$label#}+select[id=$id# name=$name# required=$required#]>option[value]+option[value=$value# selected=$selected# disabled=$disabled#]{$text#}*options';
    const submit = 'button.button[id=$id# type=submit disabled=$disabled#]{$label#}';
    const fieldset = 'fieldset>legend{$label#}';
    const checkbox = 'label>input[id=$id# name=$name# value=$value# type=checkbox checked=$selected# required=$required# autofocus=$autofocus# disabled=$disabled#]+{ $label#}';
    //------------- TYPE BASED STYLES ---------------------//
    const checkbox_array = 'fieldset>legend{$label#}+label*options>input[name=$name# value=$value# type=checkbox checked=$selected# required=$required# autofocus=$autofocus#]+{ $text#}';
    const select_array = 'label{$label#}+select[name=$name# required=$required# multiple style="height: auto;background-image: none; outline: inherit;"]>option[value=$value# selected=$selected#]{$text#}*options';
}
