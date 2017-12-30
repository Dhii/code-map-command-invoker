<?php

namespace Dhii\Invocation\UnitTest;

use OutOfRangeException;
use Xpmock\TestCase;
use Dhii\Invocation\AbstractBaseCodeMapCommandInvoker as TestSubject;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class AbstractBaseCodeMapCommandInvokerTest extends TestCase
{
    /**
     * The name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\Invocation\AbstractBaseCodeMapCommandInvoker';

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
        $methods = $this->mergeValues($methods, []);

        $builder = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
            ->setMethods($methods);

        if (is_array($constructorArgs)) {
            $builder->setConstructorArgs($constructorArgs);
        }
        $disableOriginalConstructor && $builder->disableOriginalConstructor();

        $mock = $builder->getMockForAbstractClass();

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
     * Creates a mock that both extends a class and implements interfaces.
     *
     * This is particularly useful for cases where the mock is based on an
     * internal class, such as in the case with exceptions. Helps to avoid
     * writing hard-coded stubs.
     *
     * @since [*next-version*]
     *
     * @param string $className      Name of the class for the mock to extend.
     * @param string $interfaceNames Names of the interfaces for the mock to implement.
     *
     * @return object The object that extends and implements the specified class and interfaces.
     */
    public function mockClassAndInterfaces($className, $interfaceNames = [])
    {
        $paddingClassName = uniqid($className);
        $definition = vsprintf('abstract class %1$s extends %2$s implements %3$s {}', [
            $paddingClassName,
            $className,
            implode(', ', $interfaceNames),
        ]);
        eval($definition);

        return $this->getMockForAbstractClass($paddingClassName);
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

        $subject = $this->createInstance(['_invokeByCode']);
        $_subject = $this->reflect($subject);

        $subject->expects($this->exactly(1))
            ->method('_invokeByCode')
            ->with($code, $args)
            ->will($this->returnValue($return));

        $result = $_subject->_invoke($code, $args);
        $this->assertSame($return, $result, 'Invocation did not produce expected result');
    }

    /**
     * Tests that the `invoke()` method fails as expected when the command cannot be invoked.
     *
     * @since [*next-version*]
     */
    public function testInvokeFailureOutOfRange()
    {
        $args = [uniqid('arg1')];
        $code = uniqid('code');
        $return = uniqid('return');
        $eOutOfRange = new OutOfRangeException('No callable for code, or not callable.');
        $testExceptionInterface = 'Dhii\Invocation\Exception\InvocationFailureExceptionInterface';

        $subject = $this->createInstance(['_invokeByCode', '_createInvocationFailureException']);
        $_subject = $this->reflect($subject);

        $subject->expects($this->exactly(1))
            ->method('_invokeByCode')
            ->will($this->throwException($eOutOfRange));
        $subject->expects($this->exactly(1))
            ->method('_createInvocationFailureException')
            ->will($this->returnCallback(function ($message) use ($testExceptionInterface) {
                return $this->mockClassAndInterfaces(
                    'Exception',
                    [$testExceptionInterface]
                );
            }));

        $this->setExpectedException($testExceptionInterface);
        $result = $_subject->_invoke($code, $args);
    }
}
