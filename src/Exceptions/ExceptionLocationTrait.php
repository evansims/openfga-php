<?php

declare(strict_types=1);

namespace OpenFGA\Exceptions;

use Exception;
use ReflectionClass;
use ReflectionException;

use function array_key_exists;

/**
 * Trait for capturing correct exception throw locations in debuggers.
 *
 * This trait provides a helper method that fixes the common issue where
 * enum-based exception factory methods show the wrong file/line location
 * in debuggers and stack traces. Instead of showing the factory method
 * location, it captures and sets the actual throw location.
 *
 * The trait uses reflection to modify the private file and line properties
 * of Exception objects based on the calling location captured via debug_backtrace().
 */
trait ExceptionLocationTrait
{
    /**
     * Capture and set the correct throw location for debugging.
     *
     * This method uses debug_backtrace() to find where the exception factory
     * method was called (typically where `throw` occurs) and updates the
     * exception's file and line properties accordingly. This ensures debuggers
     * show the actual throw location rather than the factory method location.
     *
     * @param Exception $exception  The exception to update with correct location
     * @param int       $skipFrames Number of stack frames to skip (default: 2)
     *
     * @throws ReflectionException If reflection operations fail
     *
     * @psalm-suppress UnusedMethodCall
     */
    private static function captureThrowLocation(Exception $exception, int $skipFrames = 2): void
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $skipFrames + 1);
        if (isset($trace[$skipFrames - 1])) {
            $reflection = new ReflectionClass($exception);
            $traceFrame = $trace[$skipFrames - 1];

            // PHPStan knows these keys always exist, but Psalm requires the checks
            // @phpstan-ignore-next-line function.alreadyNarrowedType
            if (array_key_exists('file', $traceFrame)) {
                $fileProperty = $reflection->getProperty('file');
                $fileProperty->setAccessible(true);
                $fileProperty->setValue($exception, $traceFrame['file']);
            }

            // @phpstan-ignore-next-line function.alreadyNarrowedType
            if (array_key_exists('line', $traceFrame)) {
                $lineProperty = $reflection->getProperty('line');
                $lineProperty->setAccessible(true);
                $lineProperty->setValue($exception, $traceFrame['line']);
            }
        }
    }
}
