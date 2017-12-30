<?php

namespace Dhii\Invocation;

use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\Exception\CreateOutOfRangeExceptionCapableTrait;
use Dhii\Invocation\Exception\CommandInvokerExceptionInterface;
use Dhii\Invocation\Exception\CreateInvocationFailureExceptionCapableTrait;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Util\Normalization\NormalizeArrayCapableTrait;
use Dhii\Util\Normalization\NormalizeStringCapableTrait;
use Traversable;

class CodeMapCommandInvoker extends AbstractBaseCodeMapCommandInvoker implements CommandInvokerInterface
{
    /* Functionality for invoking a callable by its code.
     *
     * @since [*next-version*]
     */
    use InvokeByCodeCapableTrait;

    /*
     * Adds internal code to callable mapping capabilities.
     *
     * @since [*next-version*]
     */
    use CodeMapAwareTrait;

    /* Functionality for mapping multiple callables to codes.
     *
     * @since [*next-version*]
     */
    use MapCallablesToCodesCapableTrait;

    /* Functionality for invoking a callable.
     *
     * @since [*next-version*]
     */
    use InvokeCallableCapableTrait;

    /* Functionality for translating and interpolating strings.
     *
     * @since [*next-version*]
     */
    use StringTranslatingTrait;

    /* A factory of Invalid Argument exceptions.
     *
     * @since [*next-version*]
     */
    use CreateInvalidArgumentExceptionCapableTrait;

    /* A factory of Invocation Failure exceptions.
     *
     * @since [*next-version*]
     */
    use CreateInvocationFailureExceptionCapableTrait;

    /* A factory of Out of Range exceptions.
     *
     * @since [*next-version*]
     */
    use CreateOutOfRangeExceptionCapableTrait;

    /* Functionality for string normalization.
     *
     * @since [*next-version*]
     */
    use NormalizeStringCapableTrait;

    /* Functionality for array normalization.
     *
     * @since [*next-version*]
     */
    use NormalizeArrayCapableTrait;

    /**
     * @since [*next-version*]
     *
     * @param $map array|Traversable The map, where keys are codes, and values are callables.
     *
     * @throws CommandInvokerExceptionInterface If something goes wrong
     */
    public function __construct($map)
    {
        $this->_construct();
        $this->_mapCallablesToCodes($map);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function invoke($code, $args = null)
    {
        $args = $args === null
            ? []
            : $args;

        return $this->_invoke($code, $args);
    }
}
