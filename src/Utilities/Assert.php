<?php

declare(strict_types=1);

namespace OpenFGA\Utilities;

use Exception;

use function in_array;
use function mb_trim;

final class Assert
{
    public static function Url(
        string $url,
        array $validSchemes = ['http', 'https'],
    ): Exception | bool {
        $url = mb_trim($url);

        if ('' === $url) {
            return false;
        }

        if (false === filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        if (false === parse_url($url)) {
            return false;
        }

        if (null === parse_url($url, PHP_URL_HOST)) {
            return false;
        }

        if (null === parse_url($url, PHP_URL_SCHEME)) {
            return false;
        }

        return in_array(parse_url($url, PHP_URL_SCHEME), $validSchemes, true);
    }
}
