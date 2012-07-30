<?php
/**
 * ValueObject for validation information
 * @author arulkumaran
 */
class ValidationInfo implements IValueObject {
    /**
     * Name of the variable being validated
     * 
     * @var string variable name
     */
    public $name;
    /**
     * Data type of the variable being validated.
     * It will be mostly string
     * 
     * @var string array multiple types are specified it will be of
     *      type array otherwise it will be a string
     */
    public $type;
    
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
     * @var number maximim value
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
     * Rules specified for the parameter in the phpdoc comment.
     * It is passed to the validation method as the second parameter
     *
     * @var array custom rule set
     */
    public $rules;
    
    /**
     * Specifying a custom error message will override the standard error
     * message return by the validator class
     *
     * @var string custom error respose
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

    public static function numericValue($value)
    {
        return ( int ) $value == $value 
            ? ( int ) $value 
            : floatval ( $value );
    }

    public static function arrayValue($value)
    {
        return is_array ( $value ) ? $value : array (
                $value 
        );
    }

    public static function stringValue($value)
    {
        return is_array ( $value ) 
            ? implode ( ',', $value ) 
            : ( string ) $value;
    }

    /**
     * Magic Method used for creating instance at run time
     */
    public static function __set_state(array $info)
    {
        $o = new self ();
        $o->name = $info ['name'];
        $o->rules = $rules = $info ['validate'];
        $o->type = $info ['type'];
        $o->rules ['fix'] = $o->fix 
            = isset ( $rules ['fix'] ) && $rules ['fix'] == 'true';
        unset ( $rules ['fix'] );
        if (isset ( $rules ['min'] )) {
            $o->rules ['min'] = $o->min = self::numericValue ( $rules ['min'] );
            unset ( $rules ['min'] );
        }
        if (isset ( $rules ['max'] )) {
            $o->rules ['max'] = $o->max = self::numericValue ( $rules ['max'] );
            unset ( $rules ['max'] );
        }
        if (isset ( $rules ['pattern'] )) {
            $o->rules ['pattern'] = $o->pattern 
                = is_array ( $rules ['pattern'] ) 
                ? implode ( ',', $rules ['pattern'] ) 
                : ( string ) $rules ['pattern'];
            unset ( $rules ['pattern'] );
        }
        if (isset ( $rules ['choice'] )) {
            $o->rules ['choice'] = $o->choice 
                = is_array ( $rules ['choice'] ) 
                ? $rules ['choice'] : array (
                    $rules ['pattern'] 
                );
            unset ( $rules ['pattern'] );
        }
        foreach ($rules as $key => $value) {
            if (property_exists ( $o, $key )) {
                $o->{$key} = $value;
            }
        }
        $type = explode ( '|', $o->type );
        if (count ( $type > 1 )) {
            $o->type = $type;
        }
        if($o->type=='integer'){
            $o->type = 'int';
        }
        return $o;
    }
}