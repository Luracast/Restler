<?php
namespace Luracast\Restler;

/**
 * @covers Luracast\Restler\Util
 */
class UtilTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Util local instance
     */
    protected $object;

    /**
     * Setting the stage for our unit tests.
     */
    protected function setUp()
    {
        $this->object = new Util();
    }

    /**
     * This is where we clean up after testing, if necessary.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Luracast\Restler\Util::removeCommonPath
     */
    public function test_class_Util_method_removeCommonPath()
    {
        $fromPath = $usingPath = null;
        $char = '%';
        $this->object->removeCommonPath($fromPath, $usingPath, $char);
    }

    /**
     * @covers Luracast\Restler\Util::getRequestMethod
     */
    public function test_class_Util_method_getRequestMethod()
    {
        $_SERVER['REQUEST_METHOD'] = null;
        $this->object->getRequestMethod();
    }

    /**
     * @covers Luracast\Restler\Util::setProperties
     * @expectedException Luracast\Restler\RestException
     * @expectedExceptionMessage Class '' not found
     */
    public function test_class_Util_method_setProperties()
    {
        $className = null;
        $metadata = $instance = null;
        $this->object->setProperties($className, $metadata, $instance);
    }

}
