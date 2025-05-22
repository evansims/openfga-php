# Getting Started

1. **Prepare an OpenFGA instance**.

   Either [Auth0 FGA](https://auth0.com/fine-grained-authorization) (managed) or [OpenFGA](https://openfga.dev/) (self-hosted) are supported.

   [Docker](https://openfga.dev/docs/getting-started/setup-openfga/docker) is the quickest way to get up and running for local development.

   ```bash
   docker pull openfga/openfga
   docker run -d -p 8080:8080 --name openfga openfga/openfga
   ```

2. **Install the SDK** using Composer.

   ```bash
   composer require evansims/openfga-php
   ```

   Please ensure your project has the [required dependencies](/README.md#requirements) installed.

3. **Create a client**.

   ```php
   use OpenFGA\Client;

   use function OpenFGA\Results\{fold, success, failure, unwrap};

   $client = new Client(url: 'http://localhost:8080');
   ```

   No authentication is used by default, but [pre-shared keys](/docs/Authentication.md#pre-shared-keys) and [client credentials](/docs/Authentication.md#client-credentials) are supported.

## Next Steps

1. **First, you'll need to [create a store](/docs/Stores.md).** Stores are isolated environments that contain their own authorization model and data, allowing you to manage permissions separately for different applications, tenants, or environments.

2. **Then, you'll need to [create an authorization model](/docs/AuthorizationModels.md).** Models define the structure of your access control system: what types of objects exist, what relationships (like viewer or owner) they support, and how access is granted. They’re the blueprint that tells the system how to interpret and enforce permissions.

3. **Next, you'll need to [create some relationship tuples](/docs/RelationshipTuples.md).** Tuples are the building blocks of authorization. They represent the actual permissions granted to users or groups for specific objects.

- With relationship tuples in place, you can then [query](/docs/Queries.md) for specific users' access to certain resources, based on your authorization model.

- It's also a good idea to create [assertions](/docs/Assertions.md) to validate that your access rules behave as expected and catch mistakes early.
