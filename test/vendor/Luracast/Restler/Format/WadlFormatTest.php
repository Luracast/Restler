<?php
namespace Luracast\Restler\Format;

/**
 * @covers Luracast\Restler\Format\WadlFormat
 */
class WadlFormatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var WadlFormat local instance
     */
    protected $object;

    /**
     * Setting the stage for our unit tests.
     */
    protected function setUp()
    {
        $this->object = new WadlFormat();
    }

    /**
     * This is where we clean up after testing, if necessary.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Luracast\Restler\Format\WadlFormat::encode
     */
    public function test_class_WadlFormat_method_encode()
    {
        $data = $humanReadable = null;
        $_SERVER ['HTTP_HOST'] = $_SERVER ['SCRIPT_NAME'] = null;
        $this->object->restler = new \Luracast\Restler\Restler();
        $this->object->restler->apiMethodInfo = $this->object->restler->mapUrlToMethod();
        $this->object->restler->apiMethodInfo->methodName = null;
        $this->object->encode($data, $humanReadable);
    }

    /**
     * @covers Luracast\Restler\Format\WadlFormat::decode
     * @expectedException Luracast\Restler\RestException
     * @expectedExceptionMessage WSDL format is read only
     */
    public function test_class_WadlFormat_method_decode()
    {
        $data = null;
        $this->object->decode($data);
    }
}

