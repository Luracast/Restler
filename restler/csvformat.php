<?php
/**
 * Comma Seperated Value Format for Restler Framework.
 * @category   Framework
 * @package    restler
 * @subpackage format
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 */
class CsvFormat implements IFormat {
    public static $export_headers = true;
    const MIME = 'text/csv';
    const EXTENSION = 'csv';

    public function getMIMEMap()
    {
        return array (
                self::EXTENSION => self::MIME 
        );
    }

    public function getMIME()
    {
        return self::MIME;
    }

    public function getExtension()
    {
        return self::EXTENSION;
    }

    public function setMIME($mime)
    {
        //ignore
    }

    public function setExtension($extension)
    {
        //ignore
    }

    /**
     * Encode the given data in csv format
     *
     * @param array $data
     *            resulting data that needs to
     *            be encoded in plist format
     * @param boolean $humanReadable
     *            set to true when restler
     *            is not running in production mode. Formatter has to
     *            make the encoded output more human readable
     * @return string encoded string
     */
    public function encode($data, $humanReadable = false)
    {
        
        
    }

    /**
     * Decode the given data from plist format
     *
     * @param string $data
     *            data sent from client to
     *            the api in the given format.
     * @return array associative array of the parsed data
     */
    public function decode($data)
    {
        $file = fopen ( 'data.csv', 'r' );
        $r = array ();
        $keys = fgetcsv ( $file );
        while ( ($line = fgetcsv ( $file )) !== false ) {
            $row = array ();
            foreach ($line as $key => $value) {
                $row [$keys [$key]] = is_numeric ( $value ) ? 
                floatval ( $value ) : $value;
            }
            $r [] = $row;
        }
        fclose ( $file );
    }

    public function __toString()
    {
        return $this->getExtension ();
    }

    public function setCharset($charset)
    {
        // TODO Auto-generated method stub
    }

    public function getCharset()
    {
        // TODO Auto-generated method stub
    }
}