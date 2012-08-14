<?php
namespace Luracast\Restler\Data;

/**
 * Restler is using many ValueObjects across to make it easy for the developers
 * to use them with the help of code hinting etc.,
 *
 * @author arulkumaran
 */
interface iValueObject
{

    /**
     * This static method is called for creating an instance of the class by
     * passing the initiation values as an array.
     *
     * @static
     * @abstract
     *
     * @param array $properties
     *
     * @return IValueObject
     */
    public static function __set_state(array $properties);

    /**
     * This method provides a string representation for the instance
     *
     * @return string
     */
    public function __toString();
}
