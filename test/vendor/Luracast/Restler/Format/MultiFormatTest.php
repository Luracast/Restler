<?php
namespace Luracast\Restler\Format;

class MockMultiFormat extends MultiFormat {
    public function encode($data, $humanReadable = false)
    {}
    public function decode($data)
    {}
}
/**
 * @covers Luracast\Restler\Format\MultiFormat
 */
class MultiFormatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MultiFormat local instance
     */
    protected $object;

    /**
     * Setting the stage for our unit tests.
     */
    protected function setUp()
    {
        $this->object = new MockMultiFormat();
    }

    /**
     * This is where we clean up after testing, if necessary.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Luracast\Restler\Format\MultiFormat::getMIMEMap
     */
    public function test_class_MultiFormat_method_getMIMEMap()
    {
        $this->object->getMIMEMap();
    }

    /**
     * @covers Luracast\Restler\Format\MultiFormat::setMIME
     */
    public function test_class_MultiFormat_method_setMIME()
    {
        $mime = null;
        $this->object->setMIME($mime);
    }

    /**
     * @covers Luracast\Restler\Format\MultiFormat::setCharset
     */
    public function test_class_MultiFormat_method_setCharset()
    {
        $charset = null;
        $this->object->setCharset($charset);
    }

    /**
     * @covers Luracast\Restler\Format\MultiFormat::getCharset
     */
    public function test_class_MultiFormat_method_getCharset()
    {
        $this->object->getCharset();
    }

    /**
     * @covers Luracast\Restler\Format\MultiFormat::getMIME
     */
    public function test_class_MultiFormat_method_getMIME()
    {
        $this->object->getMIME();
    }

    /**
     * @covers Luracast\Restler\Format\MultiFormat::setExtension
     */
    public function test_class_MultiFormat_method_setExtension()
    {
        $extension = null;
        $this->object->setExtension($extension);
    }

    /**
     * @covers Luracast\Restler\Format\MultiFormat::getExtension
     */
    public function test_class_MultiFormat_method_getExtension()
    {
        $this->object->getExtension();
    }

    /**
     * @covers Luracast\Restler\Format\MultiFormat::__toString
     */
    public function test_class_MultiFormat_method___toString()
    {
        $this->object->__toString();
    }
}

