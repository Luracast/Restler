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
        return $result;
    }

    public function formatError($statusCode, $message)
    {
        return array(
            'error' => array(
                'code' => $statusCode,
                'message' => $message
            )
        );
    }
}

