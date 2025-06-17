<?php

declare(strict_types=1);

use OpenFGA\Client;
use OpenFGA\Models\Condition;

$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
);

$storeId = $_ENV['FGA_STORE_ID'];
$modelId = $_ENV['FGA_MODEL_ID'];

// example: write
use function OpenFGA\{tuple, tuples};

// Conditional tuples - permissions with conditions
// Note: The condition 'business_hours' must be defined in your authorization model
$result = $client->writeTuples(
    store: $storeId,
    model: $modelId,
    writes: tuples(
        tuple(
            'user:contractor',
            'viewer',
            'document:confidential-report',
            new Condition(
                name: 'business_hours',
                expression: '', // Expression is defined in the model
                context: [
                    'timezone' => 'America/New_York',
                    'start_hour' => 9,
                    'end_hour' => 17,
                ],
            ),
        ),
    ),
);

if ($result->succeeded()) {
    echo "✓ Contractor can view confidential report during business hours\n";
}
// end-example: write

// example: check
// When checking permissions with conditions, provide context
$checkResult = $client->check(
    store: $storeId,
    model: $modelId,
    tuple: tuple('user:contractor', 'viewer', 'document:confidential-report'),
    context: (object) [
        'time_of_day' => new DateTimeImmutable('14:30'),
        'timezone' => 'America/New_York',
    ],
);

if ($checkResult->succeeded() && $checkResult->unwrap()->getAllowed()) {
    echo "✓ Access granted (within business hours)\n";
}
// end-example: check
