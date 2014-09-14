<?php
namespace Luracast\Restler\container;

use Interop\Container\ContainerInterface;
use Luracast\Restler\Scope;

/**
 * Adapter for the Scope resolution class in order to make Scope Container Interop-compliant.
 *
 * @category   Framework
 * @package    Restler
 * @author     Máté Kocsis <kocsismate90@gmail.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    3.0.0rc5
 */
class ScopeAdapter implements ContainerInterface
{
    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundException  No entry was found for this identifier.
     * @throws ContainerException Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($id)
    {
        try {
            $entry= Scope::get($id);
        } catch (\Exception $exception) {
            throw new ContainerException();
        }

        if ($entry === null) {
            throw new NotFoundException();
        }

        return $entry;
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return boolean
     */
    public function has($id)
    {
        return Scope::get($id) !== null;
    }
}
