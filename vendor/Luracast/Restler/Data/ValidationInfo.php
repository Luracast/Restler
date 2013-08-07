<?php
namespace Luracast\Restler\Data;

use Luracast\Restler\CommentParser;
use Luracast\Restler\Defaults;
use Luracast\Restler\Util;

/**
 * ValueObject for validation information. An instance is created and
 * populated by Restler to pass it to iValidate implementing classes for
 * validation
 *
 * @category   Framework
 * @package    Restler
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    3.0.0rc4
 */
class ValidationInfo implements iValueObject
{
    /**
     * Name of the variable being validated
     *
     * @var string variable name
     */
    public $name;

    /**
     * @var bool is it required or not
     */
    public $required;

    /**
     * @var string body or header or query where this parameter is coming from
     * in the http request
     */
    public $from;

    /**
     * Data type of the variable being validated.
     * It will be mostly string
     *
     * @var string|array multiple types are specified it will be of
     *      type array otherwise it will be a string
     */
    public $type;

    /**
     * When the type is array, this field is used to define the type of the
     * contents of the array
     *
     * @var string|null when all the items in an array are of certain type, we
     * can set this property. It will be null if the items can be of any type
     */
    public $contentType;

    /**
     * Should we attempt to fix the value?
     * When set to false validation class should throw
     * an exception or return false for the validate call.
     * When set to true it will attempt to fix the value if possible
     * or throw an exception or return false when it cant be fixed.
     *
     * @var boolean true or false
     */
    public $fix = false;

    /**
     * @var array of children to be validated
     */
    public $children = null;

    // ==================================================================
    //
    // VALUE RANGE
    //
    // ------------------------------------------------------------------
    /**
     * Given value should match one of the values in the array
     *
     * @var array of choices to match to
     */
    public $choice;
    /**
     * If the type is string it will set the lower limit for length
     * else will specify the lower limit for the value
     *
     * @var number minimum value
     */
    public $min;
    /**
     * If the type is string it will set the upper limit limit for length
     * else will specify the upper limit for the value
     *
     * @var number maximum value
     */
    public $max;

    // ==================================================================
    //
    // REGEX VALIDATION
    //
    // ------------------------------------------------------------------
    /**
     * RegEx pattern to match the value
     *
     * @var string regular expression
     */
    public $pattern;

    // ==================================================================
    //
    // CUSTOM VALIDATION
    //
    // ------------------------------------------------------------------
    /**
     * Rules specified for the parameter in the php doc comment.
     * It is passed to the validation method as the second parameter
     *
     * @var array custom rule set
     */
    public $rules;

    /**
     * Specifying a custom error message will override the standard error
     * message return by the validator class
     *
     * @var string custom error response
     */
    public $message;

    // ==================================================================
    //
    // METHODS
    //
    // ------------------------------------------------------------------

    /**
     * Name of the method to be used for validation.
     * It will be receiving two parameters $input, $rules (array)
     *
     * @var string validation method name
     */
    public $method;

    /**
     * Instance of the API class currently being called. It will be null most of
     * the time. Only when method is defined it will contain an instance.
     * This behavior is for lazy loading of the API class
     *
     * @var null|object will be null or api class instance
     */
    public $apiClassInstance = null;

    public static function numericValue($value)
    {
        return ( int )$value == $value
            ? ( int )$value
            : floatval($value);
    }

    public static function arrayValue($value)
    {
        return is_array($value) ? $value : array(
            $value
        );
    }

    public static function stringValue($value)
    {
        return is_array($value)
            ? implode(',', $value)
            : ( string )$value;
    }

    public function __toString()
    {
        return ' new ValidationInfo() ';
    }

    private function getProperty(array &$from, $property)
    {
        $p = Util::nestedValue($from, $property);
        if ($p) {
            unset($from[$property]);
        }
        $p2 = Util::nestedValue($from, 'properties', $property);
        if ($p2) {
            unset($from['properties'][$property]);
        }
        if ($property == 'type' && $p == 'array' && $p2) {
            $this->contentType = $p2;
            return $p;
        }
        $r = $p2 ? : $p ? : null;
        if($property == 'choice' && $r && !is_array($r)){
            return array($r);
        }
        return $r;
    }
    public function __construct(array $info)
    {
        $properties = get_object_vars($this);
        unset($properties['contentType']);
        foreach($properties as $property => $value) {
            $this->{$property} = $this->getProperty($info, $property);
        }
        $this->rules = Util::nestedValue($info,'properties') ?: $info;
        unset($this->rules['properties']);
        return;
        $this->name = Util::nestedValue($info, 'name') ? : 'Unknown';
        $this->required = (bool) Util::nestedValue($info, 'required');
        $this->from = Util::nestedValue($info, 'from') ? : 'query';
        $this->children = Util::nestedValue($info, 'children');
        if($this->children)
            unset($info['children']);
        $this->rules =
            Util::nestedValue($info, CommentParser::$embeddedDataName) ? : $info;
        $rules = &$this->rules;
        unset($rules[CommentParser::$embeddedDataName]);
        $this->type = Util::nestedValue($info, 'type') ? : 'mixed';
        $this->rules ['fix'] = $this->fix
            = isset ($rules ['fix']) && $rules ['fix'] == 'true';
        unset ($rules ['fix']);
        if (isset ($rules ['min'])) {
            $this->rules ['min'] = $this->min
                = self::numericValue($rules ['min']);
            unset ($rules ['min']);
        }
        if (isset ($rules ['max'])) {
            $this->rules ['max'] = $this->max
                = self::numericValue($rules ['max']);
            unset ($rules ['max']);
        }
        if (isset ($rules ['pattern'])) {
            $this->rules ['pattern'] = $this->pattern
                = is_array($rules ['pattern'])
                ? implode(',', $rules ['pattern'])
                : ( string )$rules ['pattern'];
            unset ($rules ['pattern']);
        }
        if (isset ($rules ['choice'])) {
            $this->rules ['choice'] = $this->choice
                = is_array($rules ['choice'])
                ? $rules ['choice'] : array($rules ['choice']);
            unset ($rules ['choice']);
        }
        foreach ($rules as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
        if (is_string($this->type) && $this->type == 'integer') {
            $this->type = 'int';
        }
    }

    /**
     * Magic Method used for creating instance at run time
     */
    public static function __set_state(array $info)
    {
        $o = new self ($info);
        return $o;
    }
}

