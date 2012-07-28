<?php
/**
 * Validation classes should implement this interface
 * 
 * @author arulkumaran
 */
interface IValidate {

    /**
     * method used for validation.
     *
     * @param unknown_type $input
     *            data that needs to be validated
     * @param ValidationInfo $info
     *            information to be used for validation
     * @return boolean false in case of failure or fixed value in the expected
     *         type
     * @throws RestException 400 with information about the failed validation
     */
    public function validate($input, ValidationInfo $info);
}