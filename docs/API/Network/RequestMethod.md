# RequestMethod

HTTP request methods supported by the OpenFGA API. This enum defines the specific HTTP methods used for communicating with the OpenFGA service, following standard HTTP semantics for different types of operations. Each method corresponds to specific types of API operations based on their intended semantic meaning and expected behavior. The OpenFGA API uses different HTTP methods to indicate the nature of the operation being performed, following RESTful principles: - GET for retrieving data without side effects - POST for creating resources or performing operations with side effects - PUT for updating or replacing existing resources - DELETE for removing resources from the system Using the appropriate HTTP method ensures proper caching behavior, idempotency characteristics, and compatibility with HTTP infrastructure components like proxies, load balancers, and CDNs.

## Namespace

`OpenFGA\Network`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Network/RequestMethod.php)

## Implements

* `UnitEnum`
* `BackedEnum`

## Constants

| Name     | Value    | Description                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  |
| -------- | -------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| `DELETE` | `DELETE` | DELETE method for removing resources. Used for operations that remove resources from the OpenFGA system, such as deleting stores, removing relationship tuples, or clearing authorization data. DELETE operations are idempotent, meaning that multiple identical requests have the same effect as a single request. Common OpenFGA operations using DELETE: - Deleting authorization stores - Removing relationship tuples - Clearing assertion data                                                        |
| `GET`    | `GET`    | GET method for retrieving data. Used for operations that retrieve information from the OpenFGA system without causing any side effects or state changes. GET requests are safe and idempotent, making them suitable for caching and repeated execution without concern for unintended consequences. Common OpenFGA operations using GET: - Listing authorization stores - Reading relationship tuples - Retrieving authorization models - Fetching store metadata                                            |
| `POST`   | `POST`   | POST method for creating resources and performing operations. Used for operations that create new resources or perform actions that may have side effects on the OpenFGA system. POST requests are neither safe nor idempotent, as each request may create new resources or trigger different system behaviors. Common OpenFGA operations using POST: - Performing authorization checks - Writing relationship tuples - Creating authorization models - Creating new stores - Expanding relationship queries |
| `PUT`    | `PUT`    | PUT method for updating or replacing resources. Used for operations that update existing resources or create resources with client-specified identifiers. PUT requests are idempotent, meaning that multiple identical requests result in the same final system state. Common OpenFGA operations using PUT: - Updating store metadata - Replacing authorization model configurations - Updating assertion data                                                                                               |

## Cases

| Name     | Value    | Description                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  |
| -------- | -------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| `DELETE` | `DELETE` | DELETE method for removing resources. Used for operations that remove resources from the OpenFGA system, such as deleting stores, removing relationship tuples, or clearing authorization data. DELETE operations are idempotent, meaning that multiple identical requests have the same effect as a single request. Common OpenFGA operations using DELETE: - Deleting authorization stores - Removing relationship tuples - Clearing assertion data                                                        |
| `GET`    | `GET`    | GET method for retrieving data. Used for operations that retrieve information from the OpenFGA system without causing any side effects or state changes. GET requests are safe and idempotent, making them suitable for caching and repeated execution without concern for unintended consequences. Common OpenFGA operations using GET: - Listing authorization stores - Reading relationship tuples - Retrieving authorization models - Fetching store metadata                                            |
| `POST`   | `POST`   | POST method for creating resources and performing operations. Used for operations that create new resources or perform actions that may have side effects on the OpenFGA system. POST requests are neither safe nor idempotent, as each request may create new resources or trigger different system behaviors. Common OpenFGA operations using POST: - Performing authorization checks - Writing relationship tuples - Creating authorization models - Creating new stores - Expanding relationship queries |
| `PUT`    | `PUT`    | PUT method for updating or replacing resources. Used for operations that update existing resources or create resources with client-specified identifiers. PUT requests are idempotent, meaning that multiple identical requests result in the same final system state. Common OpenFGA operations using PUT: - Updating store metadata - Replacing authorization model configurations - Updating assertion data                                                                                               |

## Methods

#### hasRequestBody

```php
public function hasRequestBody(): bool

```

Check if this HTTP method typically expects a request body. Useful for client implementations to determine whether to include request body serialization and content-type headers.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/RequestMethod.php#L101)

#### Returns

`bool` — True if the method typically has a request body, false otherwise

#### isIdempotent

```php
public function isIdempotent(): bool

```

Check if this HTTP method is idempotent. Idempotent methods can be called multiple times with the same effect. This is useful for retry logic and caching decisions in HTTP clients.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/RequestMethod.php#L117)

#### Returns

`bool` — True if the method is idempotent, false otherwise

#### isSafe

```php
public function isSafe(): bool

```

Check if this HTTP method is safe. Safe methods do not modify server state and can be cached. This is important for HTTP middleware and caching strategies.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/RequestMethod.php#L133)

#### Returns

`bool` — True if the method is safe, false otherwise
