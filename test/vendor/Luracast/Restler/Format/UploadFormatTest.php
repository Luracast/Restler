<?php
namespace Luracast\Restler\Format;

/**
 * @covers Luracast\Restler\Format\UploadFormat
 */
class UploadFormatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UploadFormat local instance
     */
    protected $object;

    /**
     * Setting the stage for our unit tests.
     */
    protected function setUp()
    {
        $this->object = new UploadFormat();
    }

    /**
     * This is where we clean up after testing, if necessary.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Luracast\Restler\Format\UploadFormat::encode
     * @expectedException Luracast\Restler\RestException
     * @expectedExceptionMessage UploadFormat is read only
     */
    public function test_class_UploadFormat_method_encode()
    {
        $data = $humanReadable = null;
        $this->object->encode($data, $humanReadable);
    }

    /**
     * @covers Luracast\Restler\Format\UploadFormat::decode
     */
    public function test_class_UploadFormat_method_decode()
    {
        $data = null;
        $this->object->decode($data);
    }
}
