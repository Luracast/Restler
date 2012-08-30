<?php
namespace Luracast\Restler\Format;

/**
 * @covers Luracast\Restler\Format\MustacheFormat
 */
class MustacheFormatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MustacheFormat local instance
     */
    protected $object;

    /**
     * Setting the stage for our unit tests.
     */
    protected function setUp()
    {
        $this->object = new MustacheFormat();
    }

    /**
     * This is where we clean up after testing, if necessary.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Luracast\Restler\Format\MustacheFormat::encode
     */
    public function test_class_MustacheFormat_method_encode()
    {
        $_SERVER['REQUEST_METHOD'] = $_SERVER['SERVER_NAME'] = $_SERVER['SERVER_PORT'] = $_SERVER['REQUEST_URI'] = null;
        $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__);
        $_SERVER['SCRIPT_NAME'] = '/../../../../raw/mustacheformat/templates/';
        $data = $humanReadable = null;
        $this->object->restler = new \Luracast\Restler\Restler();
        $this->object->restler->apiMethodInfo = $this->object->restler->mapUrlToMethod();
//        $this->object->restler->handle();
        $this->object->encode($data, $humanReadable);
    }

    /**
     * @covers Luracast\Restler\Format\MustacheFormat::decode
     * @expectedException Luracast\Restler\RestException
     * @expectedExceptionMessage MustacheFormat is write only
     */
    public function test_class_MustacheFormat_method_decode()
    {
        $data = null;
        $this->object->decode($data);
    }
}
