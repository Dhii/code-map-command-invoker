<?php

namespace Dhii\Invocation\UnitTest;

use Xpmock\TestCase;
use Dhii\Invocation\CodeMapCommandInvoker as TestSubject;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class CodeMapCommandInvokerTest extends TestCase
{
    /**
     * The name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\Invocation\CodeMapCommandInvoker';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @param array $methods                    A list of methods to mock.
     * @param array $constructorArgs            A list of arguments for the constructor.
     * @param bool  $disableOriginalConstructor If true, will not call the original constructor.
     *
     * @return TestSubject
     */
    public function createInstance($methods = [], $constructorArgs = [], $disableOriginalConstructor = true)
    {
        $methods = $this->mergeValues($methods, [
        ]);

        $builder = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
            ->setMethods($methods);

        if (is_array($constructorArgs)) {
            $builder->setConstructorArgs($constructorArgs);
        }
        $disableOriginalConstructor && $builder->disableOriginalConstructor();

        $mock = $builder->getMock();

        return $mock;
    }

    /**
     * Merges the values of two arrays.
     *
     * The resulting product will be a numeric array where the values of both inputs are present, without duplicates.
     *
     * @param array $destination The base array.
     * @param array $source      The array with more keys.
     *
     * @return array The array which contains unique values
     */
    public function mergeValues($destination, $source)
    {
        return array_keys(array_merge(array_flip($destination), array_flip($source)));
    }

    /**
     * Tests whether a valid instance of the test subject can be created.
     *
     * @since [*next-version*]
     */
    public function testCanBeCreated()
    {
        $subject = $this->createInstance();

        $this->assertInstanceOf(
            static::TEST_SUBJECT_CLASSNAME,
            $subject,
            'A valid instance of the test subject could not be created.'
        );
    }

    /**
     * Tests that the `invoke()` method works as expected.
     *
     * @since [*next-version*]
     */
    public function testInvoke()
    {
        $args = [uniqid('arg1')];
        $code = uniqid('code');
        $return = uniqid('return');

        $subject = $this->createInstance(['_invoke']);

        $subject->expects($this->exactly(1))
            ->method('_invoke')
            ->with($code, $args)
            ->will($this->returnValue($return));

        $result = $subject->invoke($code, $args);
        $this->assertSame($return, $result, 'Invocation did not produce expected result');
    }

    /**
     * Tests that `invoke()` correctly sets default for the args.
     *
     * @since [*next-version*]
     */
    public function testInvokeDefaultArgs()
    {
        $code = uniqid('code');
        $subject = $this->createInstance(['_invoke']);

        $subject->expects($this->exactly(1))
            ->method('_invoke')
            ->with($code, []);

        $subject->invoke($code);
    }

    /**
     * Tests that the constructor works as expected.
     *
     * @since [*next-version*]
     */
    public function testConstructor()
    {
        $map = [
            uniqid('code1') => function () {},
        ];
        $subject = $this->createInstance(['_mapCallablesToCodes', '_construct']);

        $subject->expects($this->exactly(1))
            ->method('_mapCallablesToCodes')
            ->with($map);

        $subject->expects($this->exactly(1))
            ->method('_construct');

        $subject->__construct($map);
    }
}
