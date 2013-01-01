<?php
namespace Luracast\Restler;

/**
 * @covers Luracast\Restler\Resources
 */
class ResourcesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Resources local instance
     */
    protected $object;

    /**
     * Setting the stage for our unit tests.
     */
    protected function setUp()
    {
        $this->object = new Resources();
        $this->object->restler = new \Luracast\Restler\Restler();
    }

    /**
     * This is where we clean up after testing, if necessary.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Luracast\Restler\Resources::index
     */
    public function test_class_Resources_method_index()
    {
        $this->object->restler = new \Luracast\Restler\Restler();
        $this->object->index();
        $this->markTestSkipped('Nah lets skip this one for now.');
    }

    /**
     * @covers Luracast\Restler\Resources::get
     * @expectedException Luracast\Restler\RestException
     * @expectedExceptionMessage Not Found
     */
    public function test_class_Resources_method_get()
    {
        $name = null;
        $this->object->get($name);
    }
}

