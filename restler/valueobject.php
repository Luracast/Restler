<?php
class ValueObject implements IValueObject {

    public static function __set_state(array $properties)
    {
        $class = get_called_class ();
        $instance = new $class ();
        $vars = get_object_vars ( $instance );
        print_r ( $vars );
        foreach ($properties as $property => $value) {
            if (property_exists ( $instance, $property )) {
                // see if the property is accessible
                if (array_key_exists ( $property, $vars )) {
                    $instance->{$property} = $value;
                } else {
                    $method = 'set' . ucfirst ( $property );
                    if (method_exists ( $instance, $method )) {
                        call_user_func ( array (
                                $instance,
                                $method 
                        ), $property );
                    }
                }
            }
        }
        return $instance;
    }
}