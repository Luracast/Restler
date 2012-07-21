<?php
class DefaultValidator implements IValidate {

    public function validate($input, ValidationInfo $info)
    {
        $error = isset ( $info->rules ['message'] ) ? 
            $info->rules ['message'] : 
            "invalid value was specified for '$info->name'";
        
        switch ($info->type) {
            case 'email' :
                $r = filter_var ( $input, FILTER_VALIDATE_EMAIL );
                if ($r) {
                    return $r;
                }
                break;
            case 'int' :
                $r = filter_var ( $input, FILTER_VALIDATE_INT, array (
                        'options' => $info->rules
                ) );
                if ($r !== false) {
                    return $r;
                }
                if($info->rules['fix']){
                    var_dump($input);
                    if(!is_numeric($input)){
                        break;
                    }
                    if (isset($info->rules['min_range']) && 
                            $input < $info->rules['min_range']){
                        return (int)$info->rules['min_range'];
                    }
                    if (isset($info->rules['max_range']) && 
                            $input > $info->rules['max_range']){
                        return (int)$info->rules['max_range'];
                    }
                }
                break;
        }
        throw new RestException ( 400, $error );
        return false;
    }
}