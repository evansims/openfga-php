<?php

declare(strict_types=1);

use OpenFGA\Client;
use OpenFGA\Models\{Collections\UserTypeFilters, UserTypeFilter};

use function OpenFGA\Models\{tuple, tuples};

beforeEach(function (): void {
    $this->responseFactory = new Nyholm\Psr7\Factory\Psr17Factory();
    $this->httpClient = new Buzz\Client\FileGetContents($this->responseFactory);
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
    // 1. Create a store for this workflow
    $storeName = 'doc-workflow-' . bin2hex(random_bytes(5));
    $store = $this->client->createStore(name: $storeName)
        ->rethrow()
        ->unwrap();
    $storeId = $store->getId();

    // 2. Create a comprehensive authorization model
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

    // 3. Set up organizational structure
    $organizationTuples = tuples(
        // Teams
        tuple('user:alice', 'member', 'team:engineering'),
        tuple('user:bob', 'member', 'team:engineering'),
        tuple('user:carol', 'member', 'team:design'),

        // Folders
        tuple('user:alice', 'owner', 'folder:engineering'),
        tuple('user:carol', 'owner', 'folder:design'),
        tuple('team:engineering#member', 'viewer', 'folder:engineering'),
        tuple('team:design#member', 'viewer', 'folder:design'),

        // Documents and their folder relationships
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

    // 4. Test permission inheritance through folder hierarchy

    // Alice owns engineering folder, should be able to edit documents in it
    $aliceCanEditApiSpec = $this->client->check(
        store: $storeId,
        model: $modelId,
        tupleKey: tuple('user:alice', 'editor', 'document:api-spec'),
    )->rethrow()->unwrap();
    expect($aliceCanEditApiSpec->getAllowed())->toBeTrue();

    // Bob is an engineering team member, should be able to view engineering docs
    $bobCanViewApiSpec = $this->client->check(
        store: $storeId,
        model: $modelId,
        tupleKey: tuple('user:bob', 'viewer', 'document:api-spec'),
    )->rethrow()->unwrap();
    expect($bobCanViewApiSpec->getAllowed())->toBeTrue();

    // Carol is design team, should NOT be able to view engineering docs
    $carolCannotViewApiSpec = $this->client->check(
        store: $storeId,
        model: $modelId,
        tupleKey: tuple('user:carol', 'viewer', 'document:api-spec'),
    )->rethrow()->unwrap();

    // The test is correct - Carol should NOT have access
    expect($carolCannotViewApiSpec->getAllowed())->toBeFalse(
        'Carol is in the design team and should not have viewer access to engineering documents',
    );

    // 5. Test role-based access at document level

    // Bob is direct editor of user-guide
    $bobCanEditUserGuide = $this->client->check(
        store: $storeId,
        model: $modelId,
        tupleKey: tuple('user:bob', 'editor', 'document:user-guide'),
    )->rethrow()->unwrap();
    expect($bobCanEditUserGuide->getAllowed())->toBeTrue();

    // Alice can still edit user-guide through folder ownership
    $aliceCanEditUserGuide = $this->client->check(
        store: $storeId,
        model: $modelId,
        tupleKey: tuple('user:alice', 'editor', 'document:user-guide'),
    )->rethrow()->unwrap();
    expect($aliceCanEditUserGuide->getAllowed())->toBeTrue();

    // 6. Test cross-team document sharing scenario

    // Add Carol as a viewer to an engineering document
    $crossTeamTuple = tuples(
        tuple('user:carol', 'viewer', 'document:api-spec'),
    );

    $this->client->writeTuples(
        store: $storeId,
        model: $modelId,
        writes: $crossTeamTuple,
    )->rethrow()->unwrap();

    // Now Carol should be able to view the API spec
    $carolCanNowViewApiSpec = $this->client->check(
        store: $storeId,
        model: $modelId,
        tupleKey: tuple('user:carol', 'viewer', 'document:api-spec'),
    )->rethrow()->unwrap();
    expect($carolCanNowViewApiSpec->getAllowed())->toBeTrue();

    // 7. Test listing permissions

    // List all documents Bob can edit
    $bobEditableObjects = $this->client->listObjects(
        store: $storeId,
        model: $modelId,
        type: 'document',
        relation: 'editor',
        user: 'user:bob',
    )->rethrow()->unwrap();

    $bobEditableList = $bobEditableObjects->getObjects();
    expect($bobEditableList)->toContain('document:user-guide');

    // List all users who can view the API spec
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
        $viewerList[] = \is_string($object) ? $object : (string) $object;
    }

    expect($viewerList)->toContain('user:alice');
    expect($viewerList)->toContain('user:bob');
    expect($viewerList)->toContain('user:carol');

    // 8. Test permission revocation

    // Remove Carol's direct viewer access to API spec
    $revokeTuple = tuples(
        tuple('user:carol', 'viewer', 'document:api-spec'),
    );

    $this->client->writeTuples(
        store: $storeId,
        model: $modelId,
        deletes: $revokeTuple,
    )->rethrow()->unwrap();

    // Carol should no longer be able to view the API spec
    $carolCannotViewApiSpecAgain = $this->client->check(
        store: $storeId,
        model: $modelId,
        tupleKey: tuple('user:carol', 'viewer', 'document:api-spec'),
    )->rethrow()->unwrap();

    // Since Carol never had legitimate access, she still shouldn't have access
    expect($carolCannotViewApiSpecAgain->getAllowed())->toBeFalse(
        'After revoking direct access, Carol still should not have viewer access to engineering documents',
    );

    // 9. Test ownership transfers

    // Transfer ownership of wireframes from Carol to Alice
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

    // Verify ownership transfer
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

    // Carol's ownership was explicitly deleted, so she should no longer be an owner
    expect($carolNoLongerOwnsWireframes->getAllowed())->toBeFalse(
        'After ownership transfer, Carol should no longer own the wireframes document',
    );

    // 10. Cleanup - Delete the test store
    $deleteResult = $this->client->deleteStore(store: $storeId);
    expect($deleteResult->succeeded())->toBeTrue();
});

test('complete project collaboration workflow', function (): void {
    // Simulate a real project collaboration scenario
    $storeName = 'project-collab-' . bin2hex(random_bytes(5));
    $store = $this->client->createStore(name: $storeName)
        ->rethrow()
        ->unwrap();
    $storeId = $store->getId();

    // Create a project-focused authorization model
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

    // Set up project structure
    $projectTuples = tuples(
        // Project roles
        tuple('user:alice', 'owner', 'project:web-app'),
        tuple('user:bob', 'maintainer', 'project:web-app'),
        tuple('user:charlie', 'contributor', 'project:web-app'),
        tuple('user:diana', 'viewer', 'project:web-app'),

        // Issues
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

    // Test project-level permissions

    // All project members should be able to view the project
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

    // Test issue-level permissions through project inheritance

    // Diana (project viewer) should be able to view all project issues
    $dianaCanViewBug = $this->client->check(
        store: $storeId,
        model: $modelId,
        tupleKey: tuple('user:diana', 'viewer', 'issue:bug-123'),
    )->rethrow()->unwrap();
    expect($dianaCanViewBug->getAllowed())->toBeTrue();

    // Test direct issue permissions

    // Bob is assignee, should be able to view bug-123
    $bobCanViewBug = $this->client->check(
        store: $storeId,
        model: $modelId,
        tupleKey: tuple('user:bob', 'viewer', 'issue:bug-123'),
    )->rethrow()->unwrap();
    expect($bobCanViewBug->getAllowed())->toBeTrue();

    // Test listing capabilities

    // List all issues Charlie can view
    $charlieIssues = $this->client->listObjects(
        store: $storeId,
        model: $modelId,
        user: 'user:charlie',
        relation: 'viewer',
        type: 'issue',
    )->rethrow()->unwrap();

    $charlieIssueList = $charlieIssues->getObjects();
    expect($charlieIssueList)->toContain('issue:bug-123'); // Through project membership
    expect($charlieIssueList)->toContain('issue:feature-456'); // Direct assignment

    // List all project viewers
    $userFilter = new UserTypeFilter(type: 'user');
    $userFilters = new UserTypeFilters();
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
        $viewerList[] = \is_string($object) ? $object : (string) $object;
    }

    // All project members should be in the viewer list
    expect($viewerList)->toContain('user:alice');
    expect($viewerList)->toContain('user:bob');
    expect($viewerList)->toContain('user:charlie');
    expect($viewerList)->toContain('user:diana');

    // Test permission changes over project lifecycle

    // Promote Charlie from contributor to maintainer
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

    // Charlie should still be able to view the project (through maintainer role)
    $charlieStillCanView = $this->client->check(
        store: $storeId,
        model: $modelId,
        tupleKey: tuple('user:charlie', 'viewer', 'project:web-app'),
    )->rethrow()->unwrap();
    expect($charlieStillCanView->getAllowed())->toBeTrue();

    // Cleanup
    $this->client->deleteStore(store: $storeId);
});
