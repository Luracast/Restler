<?php
namespace Luracast\Restler;

/**
 * @covers Luracast\Restler\Responder
 */
class ResponderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Responder local instance
     */
    protected $object;

    /**
     * Setting the stage for our unit tests.
     */
    protected function setUp()
    {
        $this->object = new Responder();
    }

    /**
     * This is where we clean up after testing, if necessary.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Luracast\Restler\Responder::formatResponse
     */
    public function test_class_Responder_method_formatResponse()
    {
        $result = null;
        $this->object->formatResponse($result);
    }

    /**
     * @covers Luracast\Restler\Responder::formatError
     */
    public function test_class_Responder_method_formatError()
    {
        $statusCode = $message = null;
        $this->object->formatError($statusCode, $message);
    }
}

