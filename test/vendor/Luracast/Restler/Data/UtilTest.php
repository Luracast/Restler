<?php
namespace Luracast\Restler\Data;

/**
 * @covers Luracast\Restler\Data\Util
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
     * @covers Luracast\Restler\Data\Util::objectToArray
     */
    public function test_class_Util_method_objectToArray()
    {
        $object = null;
        $this->object->objectToArray($object);
    }
}
