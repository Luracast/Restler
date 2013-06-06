<?php
namespace Luracast\Restler;

/**
 * Interface for creating response classes
 *
 * @category   Framework
 * @package    Restler
 * @subpackage result
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    3.0.0rc4
 */
interface iRespond
{
    /**
     * Result of an api call is passed to this method
     * to create a standard structure for the data
     *
     * @param mixed $result can be a primitive or array or object
     */
    public function formatResponse($result);

    /**
     * When the api call results in RestException this method
     * will be called to return the error message
     *
     * @param int    $statusCode
     * @param string $message
     * @param array  $details additional details about the error
     *
     * @return
     */
    public function formatError($statusCode, $message, array $details = array());
}

