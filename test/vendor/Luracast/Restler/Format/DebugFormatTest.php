<?php
namespace Luracast\Restler\Format;

/**
 * @covers Luracast\Restler\Format\DebugFormat
 */
class DebugFormatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DebugFormat local instance
     */
    protected $object;

    /**
     * Setting the stage for our unit tests.
     */
    protected function setUp()
    {
        $this->object = new DebugFormat();
    }

    /**
     * This is where we clean up after testing, if necessary.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Luracast\Restler\Format\DebugFormat::encode
     */
    public function test_class_DebugFormat_method_encode()
    {
        $data = $humanReadable = $wrapHtml = null;
        $this->object->encode($data, $humanReadable, $wrapHtml);
    }

    /**
     * @covers Luracast\Restler\Format\DebugFormat::decode
     * @expectedException Luracast\Restler\RestException
     * @expectedExceptionMessage DebugFormat is write only
     */
    public function test_class_DebugFormat_method_decode()
    {
        $data = null;
        $this->object->decode($data);
    }

    /**
     * @covers Luracast\Restler\Format\DebugFormat::header
     */
    public function test_class_DebugFormat_method_header()
    {
        $this->object->restler = new \Luracast\Restler\Restler();
        $this->object->header();
    }

    /**
     * @covers Luracast\Restler\Format\DebugFormat::footer
     */
    public function test_class_DebugFormat_method_footer()
    {
        $_SERVER['REQUEST_METHOD'] = $_SERVER['REQUEST_URI'] = $_SERVER['SERVER_PROTOCOL'] = null;
        $this->object->restler = new \Luracast\Restler\Restler();
        $this->object->footer();
    }

    /**
     * @covers Luracast\Restler\Format\DebugFormat::setCharset
     */
    public function test_class_DebugFormat_method_setCharset()
    {
        $charset = null;
        $this->object->setCharset($charset);
    }

    /**
     * @covers Luracast\Restler\Format\DebugFormat::getCharset
     */
    public function test_class_DebugFormat_method_getCharset()
    {
        $this->object->getCharset();
    }
}

