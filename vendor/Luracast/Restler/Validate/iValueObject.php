<?php
namespace Luracast\Restler\Validate;

/**
 * Restler is using many ValueObjects across to make it easy for the developers
 * to use them with the help of code hinting etc.,
 *
 * @author arulkumaran
 */
interface iValueObject {

    /**
     * This static method is called for creating an instance of the class by
     * passing the initation values as an array.
     * @return IValueObject
     */
    public static function __set_state(array $properties);
    /**
     * This method provides a string representation for the instance
     * @return string
     */
    public function __toString();
}
