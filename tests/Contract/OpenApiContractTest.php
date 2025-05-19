<?php

declare(strict_types=1);

use OpenFGA\Schema\SchemaInterface;
use OpenFGA\Schema\CollectionSchemaInterface;

it('validates SDK classes against the OpenAPI spec', function (): void {
    $commit = getenv('OPENFGA_API_COMMIT') ?: 'main';
    $url = sprintf('https://raw.githubusercontent.com/openfga/api/%s/docs/openapiv2/apidocs.swagger.json', $commit);
    $json = @file_get_contents($url);

    if ($json === false) {
        $error = error_get_last();
        $errorMsg = $error ? $error['message'] : 'Unknown error';
        test()->fail(sprintf("Failed to fetch OpenAPI spec from %s: %s", $url, $errorMsg));
    }

    $spec = json_decode($json, true);
    expect($spec)->not->toBeNull();

    $definitions = $spec['definitions'] ?? [];
    $operations = [];
    foreach ($spec['paths'] ?? [] as $path) {
        foreach ($path as $op) {
            if (isset($op['operationId'])) {
                $operations[] = $op['operationId'];
            }
        }
    }

    $root = dirname(__DIR__, 2);

    foreach (glob($root . '/src/Models/*.php') as $file) {
        $name = basename($file, '.php');
        if (str_contains($name, 'Interface') || str_starts_with($name, 'Abstract')) {
            continue;
        }

        $class = 'OpenFGA\\Models\\' . $name;

        // Skip collection wrappers that aren't defined in the OpenAPI spec
        if (is_subclass_of($class, CollectionSchemaInterface::class, true)) {
            continue;
        }
        expect($definitions)->toHaveKey($name);
        /** @var SchemaInterface $schema */
        $schema = $class::schema();
        $schemaProps = array_map(static fn($p) => $p->name, $schema->getProperties());
        $specProps = array_keys($definitions[$name]['properties'] ?? []);
        foreach ($specProps as $prop) {
            expect($schemaProps)->toContain($prop);
        }
    }

    foreach (glob($root . '/src/Requests/*Request.php') as $file) {
        if (str_contains($file, 'Interface.php')) {
            continue;
        }
        $name = basename($file, 'Request.php');
        expect($operations)->toContain($name);
    }

    foreach (glob($root . '/src/Responses/*Response.php') as $file) {
        if (str_contains($file, 'Interface.php')) {
            continue;
        }
        $name = basename($file, '.php');
        expect($definitions)->toHaveKey($name);
    }
});
