<?php
/**
 * Default structure for HTTP response
 * @category   Framework
 * @package    restler
 * @subpackage result
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 */
class DefaultResponder implements IRespond {
    
    /**
     * Current Restler instance
     * Injected at runtime
     *
     * @var Restler
     */
    public $restler;
    public static $customMIMEType;
    public static $customMIMEVersion;

    private function setCustomMIMEHeader()
    {
        if (! isset ( $this->restler->serviceMethodInfo )) {
            return;
        }
        $metadata = $this->restler->serviceMethodInfo->metadata;
        if (! empty ( $metadata ['mime'] )) {
            self::$customMIMEType = $metadata ['mime'];
        }
        if (! empty ( $metadata ['version'] )) {
            self::$customMIMEVersion = $metadata ['version'];
        }
        if (! empty ( self::$customMIMEType )) {
            $header = 'Content-Type: ' . self::$customMIMEType;
            if (! empty ( self::$customMIMEVersion )) {
                $header .= '-v' . self::$customMIMEVersion;
            }
            $header .= '+' . $this->restler->responseFormat->getExtension ();
            header ( $header );
        }
    }

    public function formatResponse($result)
    {
        $this->setCustomMIMEHeader ();
        /*
         * $className = get_class($this->classInstance); header( 'Content-Type:
         * application/vnd.mycompany.' . strtolower($className) . '-v' .
         * $className::VERSION . "+" . $this->formatInstance->getExtension());
         */
        return $result;
    }

    public function formatError($statusCode, $message)
    {
        $this->setCustomMIMEHeader ();
        return array (
                'error' => array (
                        'code' => $statusCode,
                        'message' => $message 
                ) 
        );
    }
}