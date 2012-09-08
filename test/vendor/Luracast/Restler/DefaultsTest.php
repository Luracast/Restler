<?php
namespace Luracast\Restler;

/**
 * @covers Luracast\Restler\Defaults
 */
class DefaultsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Defaults local instance
     */
    protected $object;

    /**
     * Setting the stage for our unit tests.
     */
    protected function setUp()
    {
        $this->object = new Defaults();
    }

    /**
     * This is where we clean up after testing, if necessary.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Luracast\Restler\Defaults::setProperty
     */
    public function test_class_Defaults_method_setProperty()
    {
        $name = $value = null;
        $this->object->setProperty($name, $value);
    }
}

