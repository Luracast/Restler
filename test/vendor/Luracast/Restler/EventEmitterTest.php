<?php
namespace Luracast\Restler;

class MockEventEmitter extends EventEmitter {
    public function go(array $triggers) {
        foreach ($triggers as $eventName => $params) {
            $this->trigger($eventName, $params);
        }
    }
}
/**
 * @covers Luracast\Restler\EventEmitter
 */
class EventEmitterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EventEmitter local instance
     */
    protected $object;

    /**
     * Setting the stage for our unit tests.
     */
    protected function setUp()
    {
        $this->object = new MockEventEmitter();
    }

    /**
     * This is where we clean up after testing, if necessary.
     */
    protected function tearDown()
    {
    }

    /**
     * Example implementation.
     */
    public function test_example_subscribe_to_multiple_events()
    {
        $self = $this;


        $this->object->on(array(
            'This' => function ($arg) use ($self) {
                $self->assertEquals('triggered-this', $arg);
            },
            'That' => function ($arg) use ($self) {
                $self->assertEquals('triggered-that', $arg);
            },
            'These' => function ($arg) use ($self) {
                $self->assertEquals('triggered-these', $arg);
            },
            'Those' => function ($arg) use ($self) {
                $self->assertEquals('triggered-those', $arg);
            },
        ));


        // Mocking the event triggers
        $this->object->go(array('onThis'  => 'triggered-this'));
        $this->object->go(array('onThat'  => 'triggered-that'));
        $this->object->go(array('onThese' => 'triggered-these'));
        $this->object->go(array('onThose' => 'triggered-those'));
    }

    /**
     * Example implementation.
     */
    public function test_example_subscribe_to_events_with_method_chaining()
    {
        $self = $this;

        MockEventEmitter::onThis(
            function ($arg) use ($self) {
                $self->assertEquals('triggered-this', $arg);
            }
        )->onThat(
            function ($arg) use ($self) {
                $self->assertEquals('triggered-that', $arg);
            }
        )->onThese(
            function ($arg) use ($self) {
                $self->assertEquals('triggered-these', $arg);
            }
        )->onThose(
            function ($arg) use ($self) {
                $self->assertEquals('triggered-those', $arg);
            }
        );


        // Mocking the event triggers
        $this->object->go(array('onThis'  => 'triggered-this'));
        $this->object->go(array('onThat'  => 'triggered-that'));
        $this->object->go(array('onThese' => 'triggered-these'));
        $this->object->go(array('onThose' => 'triggered-those'));
    }

    public function provideEventParameters()
    {
        return array(
            array('onTest',        array(1,2,3)),
            array('onTestAgain',   array('a','b','c','d')),
            array('onTestAnother', array('another')),
            array('onTestDone',    array()),
            array('onTestText',    'text'),
            array('onTestDigit',    364),
        );
    }

    /**
     * @dataProvider provideEventParameters
     */
    public function test_class_EventEmitter_on_method_subscribe($eventName, $expected)
    {
        $self = $this;
        $this->object->$eventName(function ($actual) use ($self, $expected) {
            $self->assertEquals($expected, $actual);
        });
        $this->object->go(array($eventName => $expected));
    }

    /**
     * @dataProvider provideEventParameters
     */
    public function test_class_EventEmitter_on_static_method_subscribe($eventName, $expected)
    {
        $self = $this;
        MockEventEmitter::$eventName(function ($actual) use ($self, $expected) {
            $self->assertEquals($expected, $actual);
        });
        $this->object->go(array($eventName => $expected));
    }

    /**
     * @covers Luracast\Restler\EventEmitter::__callStatic
     */
    public function test_class_EventEmitter_method___callStatic()
    {
        $eventName = 'onTest';
        $self = $this;
        $params = array(function ($arg) use ($self) {
            $self->assertEquals('triggered', $arg);
        });
        $this->object->__callStatic($eventName, $params);
        $this->object->go(array('onTest' => 'triggered'));
    }


    /**
     * @covers Luracast\Restler\EventEmitter::__call
     */
    public function test_class_EventEmitter_method___call()
    {
        $eventName = 'onTest';
        $self = $this;
        $params = array(function ($arg) use ($self) {
            $self->assertEquals('triggered', $arg);
        });
        $this->object->__call($eventName, $params);
        $this->object->go(array('onTest' => 'triggered'));
    }

    /**
     * @covers Luracast\Restler\EventEmitter::listen
     */
    public function test_class_EventEmitter_method_listen()
    {
        $eventName = 'onTest';
        $self = $this;
        $callback = function ($arg) use ($self) {
            $self->assertEquals('triggered', $arg);
        };
        $this->object->listen($eventName, $callback);
        $this->object->go(array('onTest' => 'triggered'));
    }

    /**
     * @covers Luracast\Restler\EventEmitter::on
     */
    public function test_class_EventEmitter_method_on()
    {
        $self = $this;
        $this->object->on(array(
            'Test' => function ($arg) use ($self) {
                $self->assertEquals('triggered', $arg);
            }
        ));
        $this->object->go(array('onTest' => 'triggered'));
    }
}
