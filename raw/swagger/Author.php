<?php
/**
 * Describe the purpose of this class/interface/trait
 *
 * @category   Framework
 * @package    restler
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    3.0.0
 */
class Author
{
    public $name = "Unknown";
    public $email = 'user@example.com';
    public $age = 28;
    public $books = array();

    public function __construct()
    {
        $this->books[] = new Book();
    }
}
