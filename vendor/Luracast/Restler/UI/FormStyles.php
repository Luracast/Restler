<?php
namespace Luracast\Restler\UI;

/**
 * Utility class for providing preset styles for html forms
 *
 * @category   Framework
 * @package    Restler
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    3.0.0rc5
 */
class FormStyles
{
    public static $html = array(
        'form' => 'form[role=form id=$id# name=$name# method=$method# action=$action# enctype=$enctype# style="padding: 10px; background-color: #eee; border:2px solid #ddd; width:600px;"]',
        'input' => 'div[style="display: block;"]>span[style="display: inline-block; width: 280px; text-align: right;"]>label{$label#}^input[name=$name# value=$value# type=$type# required=$required# autofocus=$autofocus# placeholder=$default# accept=$accept#]',
        'textarea' => 'div[style="display: block;"]>span[style="display: inline-block; width: 280px; text-align: right;"]>label{$label#}^textarea[name=$name# value=$value# required=$required# autofocus=$autofocus# placeholder=$default# rows=3]',
        'radio' => 'div[style="display: block;"]>span[style="display: inline-block; width: 280px; text-align: right;"]>label{$label#}^span>label*options>input[name=$name# value=$value# type=radio checked=$selected# required=$required#]+{ $text#}',
        'select' => 'div[style="display: block;"]>span[style="display: inline-block; width: 280px; text-align: right;"]>label{$label#}^select[name=$name# required=$required#]>option[value]+option[value=$value# selected=$selected#]{$text#}*options',
        'submit' => 'div[style="display: block;"]>span[style="display: inline-block; width: 280px;"]>label{ &nbsp; }^button.btn.btn-primary[type=submit]{$label#}',
        'fieldset' => 'fieldset>legend{$label#}',
    );
    public static $bootstrap3 = array(
        'form' => 'form[role=form id=$id# name=$name# method=$method# action=$action# enctype=$enctype#]',
        'input' => '.form-group>label{$label#}+input.form-control[name=$name# value=$value# type=$type# required=$required# autofocus=$autofocus# placeholder=$default# accept=$accept#]',
        'textarea' => '.form-group>label{$label#}+textarea.form-control[name=$name# value=$value# required=$required# autofocus=$autofocus# placeholder=$default# rows=3]',
        'radio' => '.form-group>label{$label# : &nbsp;}+label.radio-inline*options>input.radio[name=$name# value=$value# type=radio checked=$selected# required=$required#]+{$text#}',
        'select' => '.form-group>label{$label#}+select.form-control[name=$name# required=$required#]>option[value]+option[value=$value# selected=$selected#]{$text#}*options',
        'submit' => 'button.btn.btn-primary[type=submit]{$label#}',
        'fieldset' => 'fieldset>legend{$label#}',
    );
    public static $foundation5 = array(
        'form' => 'form.large-6.column[id=$id# name=$name# method=$method# action=$action# enctype=$enctype#]',
        'input' => 'label{$label#}+input[name=$name# value=$value# type=$type# required=$required# autofocus=$autofocus# placeholder=$default# accept=$accept#]',
        'textarea' => 'label{$label#}+textarea[name=$name# value=$value# required=$required# autofocus=$autofocus# placeholder=$default# rows=3]',
        'radio' => 'label{$label# : &nbsp;}+label.radio-inline*options>input.radio[name=$name# value=$value# type=radio checked=$selected# required=$required#]+{$text#}',
        'select' => 'label{$label#}+select[name=$name# required=$required#]>option[value]+option[value=$value# selected=$selected#]{$text#}*options',
        'submit' => 'button.button[type=submit]{$label#}',
        'fieldset' => 'fieldset>legend{$label#}',
    );
}