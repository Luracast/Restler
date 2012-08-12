<?php
namespace Luracast\Restler;

/**
 * Special Exception for raising API errors
 * that can be used in API methods
 * @category   Framework
 * @package    restler
 * @subpackage exception
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 */
use Exception;

class RestException extends Exception
{
    public function __construct($httpStatusCode, $errorMessage = null)
    {
        parent::__construct ( $errorMessage, $httpStatusCode );
    }
}
