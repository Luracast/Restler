<?php
namespace Luracast\Restler;

/**
 * Default Responder to provide standard structure for all HTTP responses
 *
 * @category   Framework
 * @package    Restler
 * @subpackage result
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 */
class Responder implements iRespond
{
    /**
     * Current Restler instance
     * Injected at runtime
     *
     * @var Restler
     */
    public $restler;

    public function formatResponse($result)
    {
        //TODO: check Defaults::language and change result accordingly
        return $result;
    }


    /**
     * When the api call results in RestException this method
     * will be called to return the error message
     *
     * @param RestException $exception exception that has reasons for failure
     *
     * @return array
     */
    public function formatError(RestException $exception)
    {
        //TODO: check Defaults::language and change result accordingly
        return array(
            'error' => array(
                'code' => $exception->getCode(),
                'message' => $exception->getErrorMessage()
            ) + $exception->getDetails()
        );
    }
}

