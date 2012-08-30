<?php
namespace Luracast\Restler\Format;

/**
 * @covers Luracast\Restler\Format\UrlEncodedFormat
 */
class UrlEncodedFormatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UrlEncodedFormat local instance
     */
    protected $object;

    /**
     * Setting the stage for our unit tests.
     */
    protected function setUp()
    {
        $this->object = new UrlEncodedFormat();
    }

    /**
     * This is where we clean up after testing, if necessary.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Luracast\Restler\Format\UrlEncodedFormat::encode
     */
    public function test_class_UrlEncodedFormat_method_encode()
    {
        $data = array();
        $humanReadable = null;
        $this->object->encode($data, $humanReadable);
    }

    /**
     * @covers Luracast\Restler\Format\UrlEncodedFormat::decode
     */
    public function test_class_UrlEncodedFormat_method_decode()
    {
        $data = null;
        $this->object->decode($data);
    }
}
