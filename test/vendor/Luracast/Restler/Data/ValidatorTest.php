<?php
namespace Luracast\Restler\Data;

/**
 * @covers Luracast\Restler\Data\Validator
 */
class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Validator local instance
     */
    protected $object;

    /**
     * Setting the stage for our unit tests.
     */
    protected function setUp()
    {
        $this->object = new Validator();
    }

    /**
     * This is where we clean up after testing, if necessary.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Luracast\Restler\Data\Validator::validate
     */
    public function test_class_Validator_method_validate()
    {
        $input = $info = null;
        $info = new ValidationInfo(array());
        $this->object->validate($input, $info);
    }
}
