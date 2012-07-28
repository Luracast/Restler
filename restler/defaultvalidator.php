<?php
class DefaultValidator implements IValidate {

    public function validate($input, ValidationInfo $info)
    {
        /*
        header("Content-type: text/plain");
        var_dump($info);
        exit;
         */
        $error = isset ( $info->rules ['message'] ) 
            ? $info->rules ['message'] 
            : "invalid value was specified for '$info->name'";
        
        if (isset ( $info->pattern )) {
            if (! preg_match ( $info->pattern, $input )) {
                throw new RestException ( 400, $error );
            }
        }
   
        if (isset ( $info->choice )) {
            if (!in_array ( $input, $info->choice )) {
                throw new RestException ( 400, $error );
            }
        }
        
        switch ($info->type) {
            case 'email' :
                $r = filter_var ( $input, FILTER_VALIDATE_EMAIL );
                if ($r) {
                    return $r;
                }
                break;
            case 'int':
            case 'float':
            case 'number':
            case 'integer':
                if (! is_numeric ( $input )) {
                    break;
                }
                $r = $info->numericValue ( $input );
                if (isset ( $info->min ) && $r < $info->min) {
                    if ($info->fix) {
                        $r = $info->min;
                    } else {
                        break;
                    }
                }
                if (isset ( $info->max ) && $r > $info->max) {
                    if ($info->fix) {
                        $r = $info->max;
                    } else {
                        break;
                    }
                }
                return $r;
                
            case 'string' :
                $r = strlen ( $input );
                if (isset ( $info->min ) && $r < $info->min) {
                    if ($info->fix) {
                        $input = str_pad ( $input, $info->min, $input );
                    } else {
                        break;
                    }
                }
                if (isset ( $info->max ) && $r > $info->max) {
                    if ($info->fix) {
                        $input = substr ( $input, 0, $info->max );
                    } else {
                        break;
                    }
                }
                return $input;
            
            default :
                return $input;
        }
        throw new RestException ( 400, $error );
        return false;
    }
}