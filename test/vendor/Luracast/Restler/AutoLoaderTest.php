<?php
namespace Luracast\Restler;

/**
 * @covers Luracast\Restler\AutoLoader
 */
class AutoLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AutoLoader local instance
     */
    protected $object;

    /**
     * Setting the stage for our unit tests.
     */
    protected function setUp()
    {
        $this->object = AutoLoader::instance();
    }

    /**
     * This is where we clean up after testing, if necessary.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Luracast\Restler\AutoLoader::instance
     */
    public function test_class_AutoLoader_method_instance()
    {
        $this->object->instance();
    }

    /**
     * @covers Luracast\Restler\AutoLoader::__invoke
     */
    public function test_class_AutoLoader_method___invoke()
    {
        $className = null;
        $this->object->__invoke($className);
    }
}
