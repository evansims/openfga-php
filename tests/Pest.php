<?php

declare(strict_types=1);

use OpenFGA\Tests\TestCase;

\define('OPENFGA_TESTS_DIR', __DIR__);

require_once implode(DIRECTORY_SEPARATOR, [OPENFGA_TESTS_DIR, '..', 'vendor', 'autoload.php']);

pest()->extend(TestCase::class)->in('Unit', 'Contract');
