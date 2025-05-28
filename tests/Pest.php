<?php

declare(strict_types=1);

use OpenFGA\Tests\TestCase;

\define('OPENFGA_TESTS_DIR', __DIR__);

require_once implode(DIRECTORY_SEPARATOR, [OPENFGA_TESTS_DIR, '..', 'vendor', 'autoload.php']);

// Load RequestMethod enum for tests
if (!enum_exists(\OpenFGA\Network\RequestMethod::class)) {
    require_once implode(DIRECTORY_SEPARATOR, [OPENFGA_TESTS_DIR, '..', 'src', 'Network', 'RequestManager.php']);
}

pest()->extend(TestCase::class)->in(__DIR__);
