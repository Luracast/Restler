<?php
namespace Luracast\Restler\Data;

/**
 * @covers Luracast\Restler\Data\ValueObject
 */
class ValueObjectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ValueObject local instance
     */
    protected $object;

    /**
     * Setting the stage for our unit tests.
     */
    protected function setUp()
    {
        $this->object = new ValueObject();
    }

    /**
     * This is where we clean up after testing, if necessary.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Luracast\Restler\Data\ValueObject::__toString
     */
    public function test_class_ValueObject_method___toString()
    {
        $this->object->__toString();
    }

    /**
     * @covers Luracast\Restler\Data\ValueObject::__set_state
     */
    public function test_class_ValueObject_method___set_state()
    {
        $properties = array();
        $this->object->__set_state($properties);
    }

    /**
     * @covers Luracast\Restler\Data\ValueObject::__toArray
     */
    public function test_class_ValueObject_method___toArray()
    {
        $this->object->__toArray();
    }
}

