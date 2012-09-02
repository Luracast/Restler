<?php
namespace Luracast\Restler;

/**
 * @covers Luracast\Restler\Restler
 */
class RestlerTest extends \PHPUnit_Framework_TestCase
{
    const TEST_HELPER_INFO = <<<'DOC'

In certain cases restler is designed te exit the application after
sending a response to the web client.

To be able to survive these exits and not die withe the application
as restler suggests phpunit uses test helpers extension for PHP.

Without this extension installed certain tests cannot be performed
and will therefor be skipped accordingly.

To install the extension you can use the accompanying Makefile by
running:

     make install-test-helpers

DOC;
    /**
     * @var Restler local instance
     */
    protected $object,
              $bag = array();
    static $status;

    /**
     * Setting the stage for our unit tests.
     */
    protected function setUp()
    {
        $this->object = new Restler();
    }

    /**
     * This is where we clean up after testing, if necessary.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Luracast\Restler\Restler::__destruct
     */
    public function test_class_Restler_method___destruct()
    {
        $this->object->__destruct();
    }

    /**
     * @covers Luracast\Restler\Restler::setApiClassPath
     */
    public function test_class_Restler_method_setApiClassPath()
    {
        $path = null;
        $this->object->setApiClassPath($path);
    }

    /**
     * @covers Luracast\Restler\Restler::setAPIVersion
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage version should be an integer
     */
    public function test_class_Restler_method_setAPIVersion()
    {
        $version = $minimum = $apiClassPath = null;
        $this->object->setAPIVersion($version, $minimum, $apiClassPath);
    }

    /**
     * @covers Luracast\Restler\Restler::setSupportedFormats
     * @expectedException Luracast\Restler\Exception
     * @expectedExceptionMessage  is not a valid Format Class.
     */
    public function test_class_Restler_method_setSupportedFormats()
    {
        $format = $format__ = null;
        $this->object->setSupportedFormats($format, $format__);
    }

    /**
     * @covers Luracast\Restler\Restler::addAPIClass
     * @expectedException Luracast\Restler\Exception
     * @expectedExceptionMessage API class  is missing.
     */
    public function test_class_Restler_method_addAPIClass()
    {
        $className = $resourcePath = null;
        $this->object->addAPIClass($className, $resourcePath);
    }

    /**
     * @covers Luracast\Restler\Restler::addAuthenticationClass
     * @expectedException Luracast\Restler\Exception
     * @expectedExceptionMessage API class  is missing.
     */
    public function test_class_Restler_method_addAuthenticationClass()
    {
        $className = $resourcePath = null;
        $this->object->addAuthenticationClass($className, $resourcePath);
    }

    /**
     * @covers Luracast\Restler\Restler::addErrorClass
     */
    public function test_class_Restler_method_addErrorClass()
    {
        $className = null;
        $this->object->addErrorClass($className);
    }

    /**
     * @covers Luracast\Restler\Restler::handleError
     */
    public function test_class_Restler_method_handleError()
    {
        $_SERVER['REQUEST_METHOD'] = $_SERVER['SERVER_NAME'] = $_SERVER['SERVER_PORT'] = $_SERVER['REQUEST_URI'] = null;
        if (function_exists('set_exit_overload')) {
            $bag = array();
            set_exit_overload(function ($prompt = null) use (&$bag) {
                $bag[] = md5($prompt);
                return false;
            });
            $this->object->handle();
            unset_exit_overload();
            $this->assertEquals('8efea7883d78029d32740c444384cffb', $bag[0]);
        }
        else
            $this->markTestSkipped(self::TEST_HELPER_INFO);
    }

    /**
     * @covers Luracast\Restler\Restler::init
     */
    public function test_class_Restler_method_init()
    {
        $_SERVER['SERVER_PORT'] = $_SERVER['SERVER_NAME'] = $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'] = $_SERVER['REQUEST_METHOD'] = null;
        $this->object->init();
    }

    /**
     * @covers Luracast\Restler\Restler::handle
     */
    public function test_class_Restler_method_handle()
    {
        if (function_exists('set_exit_overload')) {
            $bag = array();
            set_exit_overload(function ($prompt = null) use (&$bag) {
                $bag[] = md5($prompt);
                return false;
            });
            $this->object->handle();
            unset_exit_overload();
            $this->assertEquals('8efea7883d78029d32740c444384cffb', $bag[0]);
        }
        else
            $this->markTestSkipped(self::TEST_HELPER_INFO);
    }

    /**
     * @covers Luracast\Restler\Restler::sendData
     */
    public function test_class_Restler_method_sendData()
    {
        if (function_exists('set_exit_overload')) {
            $bag = array();
            set_exit_overload(function ($prompt = null) use (&$bag) {
                $bag[] = md5($prompt);
                return false;
            });
            $this->object->handle();
            unset_exit_overload();
            $this->assertEquals('8efea7883d78029d32740c444384cffb', $bag[0]);
        }
        else
            $this->markTestSkipped(self::TEST_HELPER_INFO);
    }

    /**
     * @covers Luracast\Restler\Restler::setStatus
     */
    public function test_class_Restler_method_setStatus()
    {
        $code = null;
        $this->object->setStatus($code);
    }

    /**
     * @covers Luracast\Restler\Restler::__get
     */
    public function test_class_Restler_method___get()
    {
        $name = null;
        $this->object->__get($name);
    }
}

//if (!function_exists(__NAMESPACE__.'\\header')) {
//    function header($h) {
//        $s = debug_backtrace(true);
//        $rt = function($a) {return isset($a['object'])
//            && $a['object'] instanceof RestlerTest;};
//        if (array_filter($s, $rt) && 0 === strpos($h, 'HTTP/1.1 ')) {
//            RestlerTest::$status = substr($h, 9);
//        }
//        return @\header($h);
//    }
//}

