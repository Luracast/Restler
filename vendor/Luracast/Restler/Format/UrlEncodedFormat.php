<?php
namespace Luracast\Restler\Format;

/**
 * URL Encoded String Format
 *
 * @category   Framework
 * @package    restler
 * @subpackage format
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 */
class UrlEncodedFormat extends Format
{
    const MIME = 'application/x-www-form-urlencoded';
    const EXTENSION = 'post';

    public function encode($data, $humanReadable = false)
    {
        return http_build_query($data);
    }

    public function decode($data)
    {
        parse_str($data, $r);
        return $r;
    }
}
