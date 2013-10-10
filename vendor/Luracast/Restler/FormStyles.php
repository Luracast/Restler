<?php
namespace Luracast\Restler;

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
    public static $html5 = array(
        'wrapper' => array('span', 'label', 'div'),
        'radio' => array('label'),
        '*' => array(),
        'form' => array(
            'style' => 'padding: 10px; background-color: #eee; border:2px solid #ddd; width: 400px;'
        ),
        'input' => array(
            'value' => '$value',
            'required' => '$required',
            'name' => '$name',
            'placeholder' => '$default',
            'pattern' => '$pattern',
            'class' => 'input-small',
            'min' => '$min',
            'max' => '$max',
        ),
        'textarea' => array(
            'value' => '$value',
            'required' => '$required',
            'name' => '$name',
            'placeholder' => '$default',
            'class' => 'input-small',
            'min' => '$min',
            'max' => '$max',
        ),
        'select' => array(
            'value' => '$value',
            'required' => '$required',
            'name' => '$name',
        ),
        'div' => array(
            'style' => 'display: block;'
        ),
        'span' => array(
            'style' => 'display: inline-block; width: 80px; text-align: right;'
        ),
    );

}