<?php

declare(strict_types=1);

namespace BladePDF\SpatieLaravelPdf\Exceptions;

use InvalidArgumentException;

final class UnsupportedReadinessTimeoutException extends InvalidArgumentException
{
    public static function forTimeout(int $timeout): self
    {
        return new self(sprintf(
            'BladePDF supports the waitUntilReady() expression, but not its per-render timeout of %d ms. '
            .'Omit the timeout argument and configure BladePDF render and HTTP timeouts instead.',
            $timeout,
        ));
    }
}
