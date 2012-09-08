<?php
namespace Luracast\Restler\Format;

class MockFormat extends Format {
    public function encode($data, $humanReadable = false)
    {}
    public function decode($data)
    {}
}
/**
 * @covers Luracast\Restler\Format\Format
 */
class FormatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Format local instance
     */
    protected $object;

    /**
     * Setting the stage for our unit tests.
     */
    protected function setUp()
    {
        $this->object = new MockFormat();
    }

    /**
     * This is where we clean up after testing, if necessary.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Luracast\Restler\Format\Format::getMIMEMap
     */
    public function test_class_Format_method_getMIMEMap()
    {
        $this->object->getMIMEMap();
    }

    /**
     * @covers Luracast\Restler\Format\Format::setMIME
     */
    public function test_class_Format_method_setMIME()
    {
        $mime = null;
        $this->object->setMIME($mime);
    }

    /**
     * @covers Luracast\Restler\Format\Format::setCharset
     */
    public function test_class_Format_method_setCharset()
    {
        $charset = null;
        $this->object->setCharset($charset);
    }

    /**
     * @covers Luracast\Restler\Format\Format::getCharset
     */
    public function test_class_Format_method_getCharset()
    {
        $this->object->getCharset();
    }

    /**
     * @covers Luracast\Restler\Format\Format::getMIME
     */
    public function test_class_Format_method_getMIME()
    {
        $this->object->getMIME();
    }

    /**
     * @covers Luracast\Restler\Format\Format::setExtension
     */
    public function test_class_Format_method_setExtension()
    {
        $extension = null;
        $this->object->setExtension($extension);
    }

    /**
     * @covers Luracast\Restler\Format\Format::getExtension
     */
    public function test_class_Format_method_getExtension()
    {
        $this->object->getExtension();
    }

    /**
     * @covers Luracast\Restler\Format\Format::__toString
     */
    public function test_class_Format_method___toString()
    {
        $this->object->__toString();
    }
}

