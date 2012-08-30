<?php
namespace Luracast\Restler\Format;

/**
 * @covers Luracast\Restler\Format\AmfFormat
 */
class AmfFormatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AmfFormat local instance
     */
    protected $object;

    /**
     * Setting the stage for our unit tests.
     */
    protected function setUp()
    {
        $this->object = new AmfFormat();
    }

    /**
     * This is where we clean up after testing, if necessary.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Luracast\Restler\Format\AmfFormat::encode
     */
    public function test_class_AmfFormat_method_encode()
    {
        $data = $humanReadable = null;
        $this->object->encode($data, $humanReadable);
    }

    /**
     * @covers Luracast\Restler\Format\AmfFormat::decode
     * @expectedException ZendAmf\Exception\InvalidArgumentException
     * @expectedExceptionMessage Inputdata is not of type String
     */
    public function test_class_AmfFormat_method_decode()
    {
        $data = null;
        $this->object->decode($data);
    }
}
