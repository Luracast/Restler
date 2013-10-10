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
        'radio' => array('wrapper' => array('label')),
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

    public static $bootstrap3 = array(
        'wrapper' => array('label', 'div'),
        'radio' => array(
            'outerWrapper' => array(
                'label' => array(
                    'class' => 'form-group'
                ),
            ),
            'style' => array(
                'class' => null
            ),
            'wrapper' => array(
                'label' => array(
                    'class' => 'radio-inline'
                ),
            )
        ),
        '*' => array(),
        'form' => array(
            'role' => 'form',
        ),
        'input' => array(
            'value' => '$value',
            'required' => '$required',
            'name' => '$name',
            'placeholder' => '$default',
            'pattern' => '$pattern',
            'min' => '$min',
            'max' => '$max',
            'class' => 'form-control',
        ),
        'textarea' => array(
            'value' => '$value',
            'required' => '$required',
            'name' => '$name',
            'placeholder' => '$default',
            'min' => '$min',
            'max' => '$max',
            'class' => 'form-control',
            'rows' => 3,
        ),
        'select' => array(
            'value' => '$value',
            'required' => '$required',
            'name' => '$name',
            'class' => 'form-control',
        ),
        'div' => array(
            'class' => 'form-group'
        ),
        'button' => array(
            'class' => 'btn btn-primary btn-lg',
        ),
    );

}