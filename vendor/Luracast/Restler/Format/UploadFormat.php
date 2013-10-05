<?php
namespace Luracast\Restler\Format;

use Luracast\Restler\RestException;

/**
 * Support for Multi Part Form Data and File Uploads
 *
 * @category   Framework
 * @package    Restler
 * @subpackage format
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    3.0.0rc4
 */
class UploadFormat extends Format
{
    const MIME = 'multipart/form-data';
    const EXTENSION = 'post';
    /**
     * use it if you need to restrict uploads based on file type
     * setting it as an empty array allows all file types
     * default is to allow only png and jpeg images
     *
     * @var array
     */
    public static $allowedMimeTypes = array('image/jpeg', 'image/png');
    /**
     * use it to restrict uploads based on file size
     * set it to 0 to allow all sizes
     * please note that it upload restrictions in the server
     * takes precedence so it has to be lower than or equal to that
     * default value is 1MB (1024x1024)bytes
     * usual value for the server is 8388608
     *
     * @var int
     */
    public static $maximumFileSize = 1048576;
    /**
     * Your own validation function for validating each uploaded file
     * it can return false or throw an exception for invalid file
     * use anonymous function / closure in PHP 5.3 and above
     * use function name in other cases
     *
     * @var Callable
     */
    public static $customValidationFunction;

    public function encode($data, $humanReadable = false)
    {
        throw new RestException(500, 'UploadFormat is read only');
    }

    public function decode($data)
    {
        $doMimeCheck = !empty(self::$allowedMimeTypes);
        $doSizeCheck = self::$maximumFileSize ? TRUE : FALSE;
        //validate
        foreach ($_FILES as $file) {
            if (is_array($file['error'])) {
                foreach ($file['error'] as $i => $error) {
                    $innerFile = array();
                    foreach ($file as $property => $value) {
                        $innerFile[$property] = $value[$i];
                    }
                    if ($innerFile['name']) static::checkFile($innerFile, $doMimeCheck, $doSizeCheck);
                }
            } else {
                if ($file['name']) static::checkFile($file, $doMimeCheck, $doSizeCheck);
            }
        }
        //sort file order if needed;
        return $_FILES + $_POST;
    }

    protected static function checkFile($file, $doMimeCheck = false, $doSizeCheck = false)
    {
        if ($file['error']) {
            //server is throwing an error
            //assume that the error is due to maximum size limit
            throw new RestException(413, "Uploaded file ({$file['name']}) is too big.");
        }
        if ($doMimeCheck && !in_array($file['type'],
                self::$allowedMimeTypes)
        ) {
            throw new RestException(403, "File type ({$file['type']}) is not supported.");
        }
        if ($doSizeCheck && $file['size'] > self::$maximumFileSize) {
            throw new RestException(413, "Uploaded file ({$file['name']}) is too big.");
        }
        if (self::$customValidationFunction) {
            if (!call_user_func(self::$customValidationFunction, $file)) {
                throw new RestException(403, "File ({$file['name']}) is not supported.");
            }
        }
    }

    function isWritable()
    {
        return false;
    }

}
