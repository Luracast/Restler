<?php
namespace Luracast\Restler\Format;

use Luracast\Restler\RestException;

class WadlFormat implements iFormat
{
    /**
     * injected at runtime
     *
     * @var Restler
     */
    public $restler;
    const MIME = 'text/plain'; // 'application/vnd.sun.wadl+xml';
    const EXTENSION = 'wadl';

    public function getMIMEMap()
    {
        return array (
                self::EXTENSION => self::MIME
        );
    }

    public function setMIME($mime)
    {
        // do nothing
    }

    public function getMIME()
    {
        return self::MIME;
    }

    public function setExtension($extension)
    {
        // do nothing
    }

    public function getExtension()
    {
        return self::EXTENSION;
    }

    public function encode($data, $humanReadable = false)
    {
        /*
         * stdClass Object ( [className] => Rss [methodName] => get [arguments]
         * => Array ( ) [defaults] => Array ( ) [metadata] => Array (
         * [XmlFormat] => Array ( [root_name] => rss [attribute_names] => Array
         * ( [0] => version [1] => xmlns ) ) [param] => Array ( ) ) [methodFlag]
         * => 0 ) <application xmlns="http://research.sun.com/wadl/2006/10">
         * <doc xmlns:jersey="http://jersey.dev.java.net/"
         * jersey:generatedBy="Jersey: 0.10-ea-SNAPSHOT 08/27/2008 08:24 PM"/>
         * <resources base="http://localhost:9998/"> <resource
         * path="/helloworld"> <method name="GET" id="getClichedMessage">
         * <response> <representation mediaType="text/plain"/> </response>
         * </method> </resource> </resources> </application>
         */
        $info = $this->restler->serviceMethodInfo;
        XmlFormat::$nameSpaces = array (
                'xmlns' => 'http://wadl.dev.java.net/2009/02',
                'xmlns:xsd' => 'http://www.w3.org/2001/XMLSchema'
        );
        XmlFormat::$attributeNames = array (
                'xmlns',
                'xmlns:xsd',
                'base',
                'path',
                'name',
                'title',
                'id'
        );
        XmlFormat::$rootName = 'application';
        $data = array (); // ('xmlns' => 'http://wadl.dev.java.net/2009/02',
                          // 'xmlns:xsd' => 'http://www.w3.org/2001/XMLSchema');
        $data ['resources'] = array (
                'base' => 'http://' . $_SERVER ['HTTP_HOST'] .
                    $_SERVER ['SCRIPT_NAME']
        );
        $resource = array (
                'path' => $this->restler->url,
                'method' => array (
                        'name' => $this->restler->requestMethod,
                        'id' => $info->methodName,
                        'response' => array ()
                )
        );
        $data ['resources'] [] = $resource;
        $format = new XmlFormat ();

        return $format->encode ( $data, $humanReadable );
    }

    public function decode($data)
    {
        throw new RestException ( 500, 'WSDL format is read only' );
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
