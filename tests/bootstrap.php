<?php

declare(strict_types=1);

/**
 * Test bootstrap file to load helper functions without polluting global autoload.
 * 
 * This file is loaded only during testing and includes the Helpers.php file
 * to make helper functions like tuple(), tuples(), etc. available to tests
 * without adding them to the main composer.json autoload section.
 */

require_once __DIR__ . '/../src/Helpers.php';