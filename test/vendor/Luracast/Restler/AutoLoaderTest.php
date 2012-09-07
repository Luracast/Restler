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
        if (!in_array(
                $bootstrap = stream_resolve_include_path('bootstrap.php'),
                get_included_files()
            ))
            include $bootstrap;

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

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_class_exists_autoloading()
    {
        $this->assertTrue(class_exists("CommentParser"));
        $this->assertTrue(class_exists("Defaults"));
        $this->assertTrue(class_exists("EventEmitter"));
        $this->assertTrue(class_exists("HumanReadableCache"));
        $this->assertTrue(class_exists("JsonFormat"));
        $this->assertTrue(class_exists("YamlFormat"));
        $this->assertTrue(class_exists("Responder"));
        $this->assertTrue(class_exists("Resources"));
        $this->assertTrue(class_exists("RestException"));
        $this->assertTrue(class_exists("Restler"));
        $this->assertTrue(class_exists("Util"));
    }

}
