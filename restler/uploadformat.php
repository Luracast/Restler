<?php
class UploadFormat implements iFormat
{
    /**
     * use it if you need to restrict uploads based on file type
     * setting it as an empty array allows all file types
     * default is to alow only png and jpeg images
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
     * @var int
     */
    public static $maximumFileSize = 1048576;
    /**
     * Your own validation function for validating each uploaded file
     * it can return false or throw an exception for invalid file
     * use anonymous function / closture in PHP 5.3 and above
     * use function name in other cases
     * @var Function
     */                             
    public static $customValidationFunction;
    
    const MIME = 'multipart/form-data';
    const EXTENSION = 'post';
    public function getMIMEMap ()
    {
        return array(self::EXTENSION => self::MIME);
    }
    public function getMIME ()
    {
        return self::MIME;
    }
    public function getExtension ()
    {
        return self::EXTENSION;
    }
    public function setMIME ($mime)
    {
        //do nothing
    }
    public function setExtension ($extension)
    {
        //do nothing
    }
    public function encode ($data, $humanReadable = FALSE)
    {
        throw new RestException(405, 'UploadFormat is read only');
    }
    public function decode ($data)
    {
        $doMimeCheck = ! empty(self::$allowedMimeTypes);
        $doSizeCheck = self::$maximumFileSize ? TRUE : FALSE;
        //validate
        foreach ($_FILES as $index => $file) {
            if ($file['error']) {
                //server is throwing an error
                //assume that the error is due to maximum size limit
                throw new RestException(413, "Uploaded file ({$file['name']}) is too big.");
            }
            $type =strtolower($file['type']);
            if ($doMimeCheck && ! in_array($type, self::$allowedMimeTypes)) {
                throw new RestException(403, "File type ({$type}) is not supported.");
            }
            if ($doSizeCheck && $file['size'] > self::$maximumFileSize) {
                throw new RestException(413, "Uploaded file ({$file['name']}) is too big.");
            }
            if(self::$customValidationFunction) { 
                if(!call_user_func(self::$customValidationFunction, $file)){
                    throw new RestException(403, "File ({$file['name']}) is not supported.");
                }
            }
        }
        //sort file order if needed;
        return $_FILES + $_POST;
    }
    public function __toString ()
    {
        return $this->getExtension();
    }
}