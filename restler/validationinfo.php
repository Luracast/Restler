<?php
/**
 * Value Object for validation data
 * @author arulkumaran
 */
class ValidationInfo {
    public $name;
    public $type;
    public $rules;

    public static function __set_state($info)
    {
        $o = new self ();
        $o->name = $info ['name'];
        $o->rules = $info ['validate'];
        $o->type = isset ( $o->rules ['type'] ) ? 
            $o->rules ['type'] : $info ['type'];
        return $o;
    }
}