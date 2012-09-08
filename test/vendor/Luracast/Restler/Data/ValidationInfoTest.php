<?php
namespace Luracast\Restler\Data;

/**
 * @covers Luracast\Restler\Data\ValidationInfo
 */
class ValidationInfoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ValidationInfo local instance
     */
    protected $object;

    /**
     * Setting the stage for our unit tests.
     */
    protected function setUp()
    {
        $this->object = new ValidationInfo(array());
    }

    /**
     * This is where we clean up after testing, if necessary.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Luracast\Restler\Data\ValidationInfo::numericValue
     */
    public function test_class_ValidationInfo_method_numericValue()
    {
        $value = null;
        $this->object->numericValue($value);
    }

    /**
     * @covers Luracast\Restler\Data\ValidationInfo::arrayValue
     */
    public function test_class_ValidationInfo_method_arrayValue()
    {
        $value = array();
        $this->object->arrayValue($value);
    }

    /**
     * @covers Luracast\Restler\Data\ValidationInfo::stringValue
     */
    public function test_class_ValidationInfo_method_stringValue()
    {
        $value = '';
        $this->object->stringValue($value);
    }

    /**
     * @covers Luracast\Restler\Data\ValidationInfo::__toString
     */
    public function test_class_ValidationInfo_method___toString()
    {
        $this->object->__toString();
    }

    /**
     * @covers Luracast\Restler\Data\ValidationInfo::__set_state
     */
    public function test_class_ValidationInfo_method___set_state()
    {
        $info = array();
        $this->object->__set_state($info);
    }
}

