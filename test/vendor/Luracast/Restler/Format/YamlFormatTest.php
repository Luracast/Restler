<?php
namespace Luracast\Restler\Format;

/**
 * @covers Luracast\Restler\Format\YamlFormat
 */
class YamlFormatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var YamlFormat local instance
     */
    protected $object;

    /**
     * Setting the stage for our unit tests.
     */
    protected function setUp()
    {
        $this->object = new YamlFormat();
    }

    /**
     * This is where we clean up after testing, if necessary.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Luracast\Restler\Format\YamlFormat::encode
     */
    public function test_class_YamlFormat_method_encode()
    {
        $data = $humanReadable = null;
        $this->object->encode($data, $humanReadable);
    }

    /**
     * @covers Luracast\Restler\Format\YamlFormat::decode
     */
    public function test_class_YamlFormat_method_decode()
    {
        $data = null;
        $this->object->decode($data);
    }
}

