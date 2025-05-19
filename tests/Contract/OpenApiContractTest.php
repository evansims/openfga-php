<?php

declare(strict_types=1);

use OpenFGA\Schema\SchemaInterface;

it('validates SDK classes against the OpenAPI spec', function (): void {
    $commit = getenv('OPENFGA_API_COMMIT') ?: 'main';
    $url = sprintf('https://raw.githubusercontent.com/openfga/api/%s/docs/openapiv2/apidocs.swagger.json', $commit);
    $json = file_get_contents($url);
    expect($json)->not->toBeFalse();

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
        expect($definitions)->toHaveKey($name);
        $class = 'OpenFGA\\Models\\' . $name;
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
