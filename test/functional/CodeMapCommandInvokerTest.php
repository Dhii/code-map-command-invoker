<?php

namespace Dhii\Invocation\FuncTest;

use ArrayIterator;
use Traversable;
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
     * @param array $methods         A list of methods to mock.
     * @param array $constructorArgs A list of arguments for the constructor.
     *
     * @return TestSubject
     */
    public function createInstance($methods = [], $constructorArgs = [])
    {
        $methods = is_array($methods)
                ? $this->mergeValues($methods, [])
                : $methods;

        $builder = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
            ->setMethods($methods);

        if (is_array($constructorArgs)) {
            $builder->setConstructorArgs($constructorArgs);
        }

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
     * Creates a traversable list.
     *
     * @since [*next-version*]
     *
     * @param array $array The array with elements for the traversable.
     *
     * @return Traversable The new Traversable.
     */
    public function createTraversable(array $array)
    {
        return new ArrayIterator($array);
    }

    /**
     * Tests that mapped functions may be invoked correctly.
     *
     * @since [*next-version*]
     */
    public function testInvoke()
    {
        $key1 = uniqid('key1');
        $key2 = uniqid('key2');
        $value1 = uniqid('value1');
        $value2 = uniqid('value2');
        $fn1 = function () { return func_get_args(); };
        $fn2 = function () use ($value2) { return $value2; };
        $map = [
            $key1 => $fn1,
            $key2 => $fn2,
        ];
        $subject = $this->createInstance(null, [$map]);

        $result1 = $subject->invoke($key1, [$value1]);
        $this->assertSame([$value1], $result1, 'First invocation (parameterized) returned wrong result');

        $result2 = $subject->invoke($key2);
        $this->assertSame($value2, $result2, 'Second invocation (not parameterized) return wrong result');
    }

    /**
     * Tests that mapped functions may be invoked correctly when args is a traversable list.
     *
     * @since [*next-version*]
     */
    public function testInvokeTraversable()
    {
        $key1 = uniqid('key1');
        $value1 = uniqid('value1');
        $fn1 = function () { return func_get_args(); };
        $_map = [
            $key1 => $fn1,
        ];
        $map = $this->createTraversable($_map);
        $subject = $this->createInstance(null, [$map]);

        $result1 = $subject->invoke($key1, [$value1]);
        $this->assertSame([$value1], $result1, 'Invocation returned wrong result');
    }

    /**
     * Tests that attempting to invoke a non-existing command throws the correct exception.
     *
     * @since [*next-version*]
     */
    public function testInvokeFailureNonExistingCommand()
    {
        $_map = []; // Nothing in here
        $map = $this->createTraversable($_map);
        $subject = $this->createInstance(null, [$map]);

        $this->setExpectedException('Dhii\Invocation\Exception\InvocationFailureExceptionInterface');
        $result1 = $subject->invoke(uniqid('code'));
    }
}
