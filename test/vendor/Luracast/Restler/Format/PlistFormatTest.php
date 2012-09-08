<?php
namespace Luracast\Restler\Format;

/**
 * @covers Luracast\Restler\Format\PlistFormat
 */
class PlistFormatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PlistFormat local instance
     */
    protected $object;

    /**
     * Setting the stage for our unit tests.
     */
    protected function setUp()
    {
        $this->object = new PlistFormat();
    }

    /**
     * This is where we clean up after testing, if necessary.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Luracast\Restler\Format\PlistFormat::setMIME
     */
    public function test_class_PlistFormat_method_setMIME()
    {
        $mime = null;
        $this->object->setMIME($mime);
    }

    /**
     * @covers Luracast\Restler\Format\PlistFormat::encode
     */
    public function test_class_PlistFormat_method_encode()
    {
        $data = $humanReadable = null;
        $this->object->encode($data, $humanReadable);
    }

    /**
     * @covers Luracast\Restler\Format\PlistFormat::decode
     * @expectedException CFPropertyList\IOException
     * @expectedExceptionMessage <string>
     */
    public function test_class_PlistFormat_method_decode()
    {
        $data = null;
        $this->object->decode($data);
    }
}

