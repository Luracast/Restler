<?php
namespace Luracast\Restler\container;

/**
 * General exception for the ScopeAdapter which is thrown when an error occures in the IoC Container.
 *
 * @category   Framework
 * @package    Restler
 * @author     Máté Kocsis <kocsismate90@gmail.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    3.0.0rc5
 */
class ContainerException extends \Exception implements \Interop\Container\Exception\ContainerException
{

}
