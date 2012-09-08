<?php
namespace Luracast\Restler\Format;

/**
 * @covers Luracast\Restler\Format\JsonFormat
 */
class JsonFormatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var JsonFormat local instance
     */
    protected $object;

    /**
     * Setting the stage for our unit tests.
     */
    protected function setUp()
    {
        $this->object = new JsonFormat();
    }

    /**
     * This is where we clean up after testing, if necessary.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Luracast\Restler\Format\JsonFormat::encode
     */
    public function test_class_JsonFormat_method_encode()
    {
        $data = $humanReadable = null;
        $this->object->encode($data, $humanReadable);
    }

    /**
     * @covers Luracast\Restler\Format\JsonFormat::decode
     */
    public function test_class_JsonFormat_method_decode()
    {
        $data = null;
        $this->object->decode($data);
    }
}

