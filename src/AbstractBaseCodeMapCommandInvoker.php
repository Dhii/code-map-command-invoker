<?php

namespace Dhii\Invocation;

use OutOfRangeException;
use Traversable;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use Dhii\Invocation\Exception\InvocationFailureExceptionInterface;

abstract class AbstractBaseCodeMapCommandInvoker implements CommandInvokerInterface
{
    /**
     * Parameter-less constructor.
     *
     * Invoke this in actual constructor.
     *
     * @since [*next-version*]
     */
    protected function _construct()
    {
        $this->callableCodeMap = [];
    }

    /**
     * Invoke a command.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable $command The command to invoke.
     *                                   Its string value will be used to find the mapped callable.
     * @param array|Traversable $args    The arguments to invoke the command with.
     *
     * @throws InvocationFailureExceptionInterface If the command could not be invoked.
     *
     * @return mixed The result of the invocation.
     */
    protected function _invoke($command, $args)
    {
        try {
            return $this->_invokeByCode($command, $args);
        } catch (OutOfRangeException $e) {
            throw $this->_createInvocationFailureException($this->__('Could not invoke callable'), null, $e, $this, $command, $args);
        }
    }

    /**
     * Invokes functionality by code.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable $code The code of the functionality to invoke.
     * @param array|Traversable $args The args to invoke with.
     *
     * @throws OutOfRangeException If no callable corresponds to the given code.
     *
     * @return mixed The result of the invocation.
     */
    abstract protected function _invokeByCode($code, $args);

    /**
     * Creates a new Invocation Failure Exception.
     *
     * @since [*next-version*]
     *
     * @param null|string|Stringable       $message  The error message, if any.
     * @param null|int|string|Stringable   $code     The error code, if any.
     * @param RootException|null           $previous The inner exception, if any.
     * @param CommandInvokerInterface|null $invoker  The problematic invoker, if any.
     * @param string|Stringable|null       $command  The command that failed, if any.
     * @param array|null                   $args     The command arguments, if any.
     *
     * @return InvocationFailureExceptionInterface The new exception.
     */
    abstract protected function _createInvocationFailureException(
        $message = null,
        $code = null,
        RootException $previous = null,
        CommandInvokerInterface $invoker = null,
        $command = null,
        $args = null
    );

    /**
     * Translates a string, and replaces placeholders.
     *
     * @since [*next-version*]
     * @see   sprintf()
     *
     * @param string $string  The format string to translate.
     * @param array  $args    Placeholder values to replace in the string.
     * @param mixed  $context The context for translation.
     *
     * @return string The translated string.
     */
    abstract protected function __($string, $args = [], $context = null);
}
