<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Integration;

use Buzz\Client\FileGetContents;
use Nyholm\Psr7\Factory\Psr17Factory;
use OpenFGA\Client;
use OpenFGA\Models\{Collections\UserTypeFilters, UserTypeFilter};

use function is_string;
use function OpenFGA\{tuple, tuples};

describe('End-to-End Workflow', function (): void {
    beforeEach(function (): void {
        $this->responseFactory = new Psr17Factory;
        $this->httpClient = new FileGetContents($this->responseFactory);
        $this->httpRequestFactory = $this->responseFactory;
        $this->httpStreamFactory = $this->responseFactory;
        $this->url = getenv('FGA_API_URL') ?: 'http://openfga:8080';

        $this->client = new Client(
            url: $this->url,
            httpClient: $this->httpClient,
            httpResponseFactory: $this->responseFactory,
            httpStreamFactory: $this->httpStreamFactory,
            httpRequestFactory: $this->httpRequestFactory,
        );
    });

    test('complete document management workflow', function (): void {
        $storeName = 'doc-workflow-' . bin2hex(random_bytes(5));
        $store = $this->client->createStore(name: $storeName)
            ->rethrow()
            ->unwrap();
        $storeId = $store->getId();
        $dsl = '
        model
          schema 1.1

        type user

        type team
          relations
            define member: [user]

        type folder
          relations
            define owner: [user]
            define editor: [user, team#member] or owner
            define viewer: [user, team#member] or editor

        type document
          relations
            define owner: [user]
            define editor: [user, team#member] or owner or editor from parent
            define viewer: [user, team#member] or editor or viewer from parent
            define parent: [folder]
    ';

        $model = $this->client->dsl($dsl)->rethrow()->unwrap();

        $createModelResponse = $this->client->createAuthorizationModel(
            store: $storeId,
            typeDefinitions: $model->getTypeDefinitions(),
        )->rethrow()->unwrap();
        $modelId = $createModelResponse->getModel();

        $organizationTuples = tuples(
            tuple('user:alice', 'member', 'team:engineering'),
            tuple('user:bob', 'member', 'team:engineering'),
            tuple('user:carol', 'member', 'team:design'),
            tuple('user:alice', 'owner', 'folder:engineering'),
            tuple('user:carol', 'owner', 'folder:design'),
            tuple('team:engineering#member', 'viewer', 'folder:engineering'),
            tuple('team:design#member', 'viewer', 'folder:design'),
            tuple('user:alice', 'owner', 'document:api-spec'),
            tuple('folder:engineering', 'parent', 'document:api-spec'),
            tuple('user:bob', 'editor', 'document:user-guide'),
            tuple('folder:engineering', 'parent', 'document:user-guide'),
            tuple('user:carol', 'owner', 'document:wireframes'),
            tuple('folder:design', 'parent', 'document:wireframes'),
        );

        $this->client->writeTuples(
            store: $storeId,
            model: $modelId,
            writes: $organizationTuples,
        )->rethrow()->unwrap();

        $aliceCanEditApiSpec = $this->client->check(
            store: $storeId,
            model: $modelId,
            tupleKey: tuple('user:alice', 'editor', 'document:api-spec'),
        )->rethrow()->unwrap();
        expect($aliceCanEditApiSpec->getAllowed())->toBeTrue();

        $bobCanViewApiSpec = $this->client->check(
            store: $storeId,
            model: $modelId,
            tupleKey: tuple('user:bob', 'viewer', 'document:api-spec'),
        )->rethrow()->unwrap();
        expect($bobCanViewApiSpec->getAllowed())->toBeTrue();

        $carolCannotViewApiSpec = $this->client->check(
            store: $storeId,
            model: $modelId,
            tupleKey: tuple('user:carol', 'viewer', 'document:api-spec'),
        )->rethrow()->unwrap();

        expect($carolCannotViewApiSpec->getAllowed())->toBeFalse(
            'Carol is in the design team and should not have viewer access to engineering documents',
        );

        $bobCanEditUserGuide = $this->client->check(
            store: $storeId,
            model: $modelId,
            tupleKey: tuple('user:bob', 'editor', 'document:user-guide'),
        )->rethrow()->unwrap();
        expect($bobCanEditUserGuide->getAllowed())->toBeTrue();

        $aliceCanEditUserGuide = $this->client->check(
            store: $storeId,
            model: $modelId,
            tupleKey: tuple('user:alice', 'editor', 'document:user-guide'),
        )->rethrow()->unwrap();
        expect($aliceCanEditUserGuide->getAllowed())->toBeTrue();

        $crossTeamTuple = tuples(
            tuple('user:carol', 'viewer', 'document:api-spec'),
        );

        $this->client->writeTuples(
            store: $storeId,
            model: $modelId,
            writes: $crossTeamTuple,
        )->rethrow()->unwrap();

        $carolCanNowViewApiSpec = $this->client->check(
            store: $storeId,
            model: $modelId,
            tupleKey: tuple('user:carol', 'viewer', 'document:api-spec'),
        )->rethrow()->unwrap();
        expect($carolCanNowViewApiSpec->getAllowed())->toBeTrue();

        $bobEditableObjects = $this->client->listObjects(
            store: $storeId,
            model: $modelId,
            type: 'document',
            relation: 'editor',
            user: 'user:bob',
        )->rethrow()->unwrap();

        $bobEditableList = $bobEditableObjects->getObjects();
        expect($bobEditableList)->toContain('document:user-guide');

        $apiSpecViewers = $this->client->listUsers(
            store: $storeId,
            model: $modelId,
            object: 'document:api-spec',
            relation: 'viewer',
            userFilters: new UserTypeFilters([
                new UserTypeFilter(type: 'user'),
            ]),
        )->rethrow()->unwrap();

        $viewerList = [];
        foreach ($apiSpecViewers->getUsers() as $user) {
            $object = $user->getObject();
            $viewerList[] = is_string($object) ? $object : (string) $object;
        }

        expect($viewerList)->toContain('user:alice');
        expect($viewerList)->toContain('user:bob');
        expect($viewerList)->toContain('user:carol');

        $revokeTuple = tuples(
            tuple('user:carol', 'viewer', 'document:api-spec'),
        );

        $this->client->writeTuples(
            store: $storeId,
            model: $modelId,
            deletes: $revokeTuple,
        )->rethrow()->unwrap();

        $carolCannotViewApiSpecAgain = $this->client->check(
            store: $storeId,
            model: $modelId,
            tupleKey: tuple('user:carol', 'viewer', 'document:api-spec'),
        )->rethrow()->unwrap();

        expect($carolCannotViewApiSpecAgain->getAllowed())->toBeFalse(
            'After revoking direct access, Carol still should not have viewer access to engineering documents',
        );

        $this->client->writeTuples(
            store: $storeId,
            model: $modelId,
            deletes: tuples(
                tuple('user:carol', 'owner', 'document:wireframes'),
            ),
        )->rethrow()->unwrap();

        $this->client->writeTuples(
            store: $storeId,
            model: $modelId,
            writes: tuples(
                tuple('user:alice', 'owner', 'document:wireframes'),
            ),
        )->rethrow()->unwrap();

        $aliceOwnsWireframes = $this->client->check(
            store: $storeId,
            model: $modelId,
            tupleKey: tuple('user:alice', 'owner', 'document:wireframes'),
        )->rethrow()->unwrap();
        expect($aliceOwnsWireframes->getAllowed())->toBeTrue();

        $carolNoLongerOwnsWireframes = $this->client->check(
            store: $storeId,
            model: $modelId,
            tupleKey: tuple('user:carol', 'owner', 'document:wireframes'),
        )->rethrow()->unwrap();

        expect($carolNoLongerOwnsWireframes->getAllowed())->toBeFalse(
            'After ownership transfer, Carol should no longer own the wireframes document',
        );

        $deleteResult = $this->client->deleteStore(store: $storeId);
        expect($deleteResult->succeeded())->toBeTrue();
    });

    test('complete project collaboration workflow', function (): void {
        $storeName = 'project-collab-' . bin2hex(random_bytes(5));
        $store = $this->client->createStore(name: $storeName)
            ->rethrow()
            ->unwrap();
        $storeId = $store->getId();
        $dsl = '
        model
          schema 1.1

        type user

        type project
          relations
            define owner: [user]
            define maintainer: [user]
            define contributor: [user]
            define viewer: [user] or contributor or maintainer or owner

        type issue
          relations
            define assignee: [user]
            define reporter: [user]
            define project: [project]
            define viewer: [user] or assignee or reporter or viewer from project
    ';

        $model = $this->client->dsl($dsl)->rethrow()->unwrap();

        $createModelResponse = $this->client->createAuthorizationModel(
            store: $storeId,
            typeDefinitions: $model->getTypeDefinitions(),
        )->rethrow()->unwrap();
        $modelId = $createModelResponse->getModel();

        $projectTuples = tuples(
            tuple('user:alice', 'owner', 'project:web-app'),
            tuple('user:bob', 'maintainer', 'project:web-app'),
            tuple('user:charlie', 'contributor', 'project:web-app'),
            tuple('user:diana', 'viewer', 'project:web-app'),
            tuple('user:alice', 'reporter', 'issue:bug-123'),
            tuple('user:bob', 'assignee', 'issue:bug-123'),
            tuple('project:web-app', 'project', 'issue:bug-123'),
            tuple('user:charlie', 'reporter', 'issue:feature-456'),
            tuple('user:charlie', 'assignee', 'issue:feature-456'),
            tuple('project:web-app', 'project', 'issue:feature-456'),
        );

        $this->client->writeTuples(
            store: $storeId,
            model: $modelId,
            writes: $projectTuples,
        )->rethrow()->unwrap();

        $aliceCanViewProject = $this->client->check(
            store: $storeId,
            model: $modelId,
            tupleKey: tuple('user:alice', 'viewer', 'project:web-app'),
        )->rethrow()->unwrap();
        expect($aliceCanViewProject->getAllowed())->toBeTrue();

        $dianaCanViewProject = $this->client->check(
            store: $storeId,
            model: $modelId,
            tupleKey: tuple('user:diana', 'viewer', 'project:web-app'),
        )->rethrow()->unwrap();
        expect($dianaCanViewProject->getAllowed())->toBeTrue();

        $dianaCanViewBug = $this->client->check(
            store: $storeId,
            model: $modelId,
            tupleKey: tuple('user:diana', 'viewer', 'issue:bug-123'),
        )->rethrow()->unwrap();
        expect($dianaCanViewBug->getAllowed())->toBeTrue();

        $bobCanViewBug = $this->client->check(
            store: $storeId,
            model: $modelId,
            tupleKey: tuple('user:bob', 'viewer', 'issue:bug-123'),
        )->rethrow()->unwrap();
        expect($bobCanViewBug->getAllowed())->toBeTrue();

        $charlieIssues = $this->client->listObjects(
            store: $storeId,
            model: $modelId,
            user: 'user:charlie',
            relation: 'viewer',
            type: 'issue',
        )->rethrow()->unwrap();

        $charlieIssueList = $charlieIssues->getObjects();
        expect($charlieIssueList)->toContain('issue:bug-123');
        expect($charlieIssueList)->toContain('issue:feature-456');

        $userFilter = new UserTypeFilter(type: 'user');
        $userFilters = new UserTypeFilters;
        $userFilters->add($userFilter);

        $projectViewers = $this->client->listUsers(
            store: $storeId,
            model: $modelId,
            object: 'project:web-app',
            relation: 'viewer',
            userFilters: $userFilters,
        )->rethrow()->unwrap();

        $viewerList = [];
        foreach ($projectViewers->getUsers() as $user) {
            $object = $user->getObject();
            $viewerList[] = is_string($object) ? $object : (string) $object;
        }

        expect($viewerList)->toContain('user:alice');
        expect($viewerList)->toContain('user:bob');
        expect($viewerList)->toContain('user:charlie');
        expect($viewerList)->toContain('user:diana');

        $this->client->writeTuples(
            store: $storeId,
            model: $modelId,
            deletes: tuples(
                tuple('user:charlie', 'contributor', 'project:web-app'),
            ),
        )->rethrow()->unwrap();

        $this->client->writeTuples(
            store: $storeId,
            model: $modelId,
            writes: tuples(
                tuple('user:charlie', 'maintainer', 'project:web-app'),
            ),
        )->rethrow()->unwrap();

        $charlieStillCanView = $this->client->check(
            store: $storeId,
            model: $modelId,
            tupleKey: tuple('user:charlie', 'viewer', 'project:web-app'),
        )->rethrow()->unwrap();
        expect($charlieStillCanView->getAllowed())->toBeTrue();

        $this->client->deleteStore(store: $storeId);
    });
});
