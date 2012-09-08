<?php
namespace Luracast\Restler\Format;

/**
 * @covers Luracast\Restler\Format\XmlFormat
 */
class XmlFormatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var XmlFormat local instance
     */
    protected $object;

    /**
     * Setting the stage for our unit tests.
     */
    protected function setUp()
    {
        $this->object = new XmlFormat();
    }

    /**
     * This is where we clean up after testing, if necessary.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Luracast\Restler\Format\XmlFormat::encode
     */
    public function test_class_XmlFormat_method_encode()
    {
        $data = $humanReadable = null;
        $this->object->encode($data, $humanReadable);
    }

    /**
     * @covers Luracast\Restler\Format\XmlFormat::decode
     */
    public function test_class_XmlFormat_method_decode()
    {
        $data = null;
        $this->object->decode($data);
    }

    /**
     * @covers Luracast\Restler\Format\XmlFormat::isAssoc
     */
    public function test_class_XmlFormat_method_isAssoc()
    {
        $array = null;
        $this->object->isAssoc($array);
    }

    /**
     * @covers Luracast\Restler\Format\XmlFormat::toXML
     */
    public function test_class_XmlFormat_method_toXML()
    {
        $data = $rootNodeName = $humanReadable = $xml = null;
        $this->object->toXML($data, $rootNodeName, $humanReadable, $xml);
    }

    /**
     * @covers Luracast\Restler\Format\XmlFormat::toArray
     * @expectedException Luracast\Restler\Exception
     * @expectedExceptionMessage String could not be parsed as XML
     */
    public function test_class_XmlFormat_method_toArray()
    {
        $xml = $ns = $firstCall = null;
        $this->object->toArray($xml, $ns, $firstCall);
    }

    /**
     * @covers Luracast\Restler\Format\XmlFormat::exportCurrentSettings
     */
    public function test_class_XmlFormat_method_exportCurrentSettings()
    {
        $this->object->exportCurrentSettings();
    }
}

