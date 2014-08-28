<?php
namespace Luracast\Restler\container;

/**
 * Exception for the ScopeAdapter which is thrown when an entry can't be found in the IoC Container.
 *
 * @category   Framework
 * @package    Restler
 * @author     Máté Kocsis <kocsismate90@gmail.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    3.0.0rc5
 */
class NotFoundException extends \Exception implements \Interop\Container\Exception\NotFoundException
{

}
