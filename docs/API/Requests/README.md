# Requests

[API Documentation](../README.md) > Requests

Request objects for all OpenFGA API operations.

**Total Components:** 37

## Interfaces

| Name | Description |
|------|-------------|
| [`BatchCheckRequestInterface`](./BatchCheckRequestInterface.md) | Request for performing multiple authorization checks in a single batch. This request allows check... |
| [`CheckRequestInterface`](./CheckRequestInterface.md) | Interface for authorization check request specifications. This interface defines the contract for... |
| [`CreateAuthorizationModelRequestInterface`](./CreateAuthorizationModelRequestInterface.md) | Interface for creating new authorization models in OpenFGA. This interface defines the contract f... |
| [`CreateStoreRequestInterface`](./CreateStoreRequestInterface.md) | Interface for creating a new OpenFGA store. This interface defines the contract for requests that... |
| [`DeleteStoreRequestInterface`](./DeleteStoreRequestInterface.md) | Interface for deleting an OpenFGA store. This interface defines the contract for requests that pe... |
| [`ExpandRequestInterface`](./ExpandRequestInterface.md) | Interface for expanding relationship graphs in OpenFGA. This interface defines the contract for r... |
| [`GetAuthorizationModelRequestInterface`](./GetAuthorizationModelRequestInterface.md) | Interface for retrieving a specific authorization model. This interface defines the contract for ... |
| [`GetStoreRequestInterface`](./GetStoreRequestInterface.md) | Interface for retrieving information about an OpenFGA store. This interface defines the contract ... |
| [`ListAuthorizationModelsRequestInterface`](./ListAuthorizationModelsRequestInterface.md) | Interface for listing authorization models in a store. This interface defines the contract for re... |
| [`ListObjectsRequestInterface`](./ListObjectsRequestInterface.md) | Interface for listing objects that a user has access to. This interface defines the contract for ... |
| [`ListStoresRequestInterface`](./ListStoresRequestInterface.md) | Interface for listing available OpenFGA stores. This interface defines the contract for requests ... |
| [`ListTupleChangesRequestInterface`](./ListTupleChangesRequestInterface.md) | Interface for listing historical changes to relationship tuples. This interface defines the contr... |
| [`ListUsersRequestInterface`](./ListUsersRequestInterface.md) | Interface for listing users who have a specific relation to an object. This interface defines the... |
| [`ReadAssertionsRequestInterface`](./ReadAssertionsRequestInterface.md) | Interface for reading test assertions from an authorization model. This interface defines the con... |
| [`ReadTuplesRequestInterface`](./ReadTuplesRequestInterface.md) | Interface for reading relationship tuples from an OpenFGA store. This interface defines the contr... |
| [`RequestInterface`](./RequestInterface.md) | Base interface for all OpenFGA API request objects. This interface defines the core contract that... |
| [`StreamedListObjectsRequestInterface`](./StreamedListObjectsRequestInterface.md) | Request interface for streaming objects that a user has a specific relationship with. This reques... |
| [`WriteAssertionsRequestInterface`](./WriteAssertionsRequestInterface.md) | Interface for writing test assertions to an authorization model. This interface defines the contr... |
| [`WriteTuplesRequestInterface`](./WriteTuplesRequestInterface.md) | Interface for writing relationship tuples to an OpenFGA store. This interface defines the contrac... |

## Classes

| Name | Description |
|------|-------------|
| [`BatchCheckRequest`](./BatchCheckRequest.md) | Request for performing multiple authorization checks in a single batch. This request allows check... |
| [`CheckRequest`](./CheckRequest.md) | Request for performing authorization checks in OpenFGA. This request determines whether a user ha... |
| [`CreateAuthorizationModelRequest`](./CreateAuthorizationModelRequest.md) | Request for creating a new authorization model in OpenFGA. Authorization models define the permis... |
| [`CreateStoreRequest`](./CreateStoreRequest.md) | Request for creating a new OpenFGA store. Stores provide data isolation for different application... |
| [`DeleteStoreRequest`](./DeleteStoreRequest.md) | Request for permanently deleting a store and all its data. This request removes the entire store,... |
| [`ExpandRequest`](./ExpandRequest.md) | Request for expanding a relationship to show all users who have that relationship. This request r... |
| [`GetAuthorizationModelRequest`](./GetAuthorizationModelRequest.md) | Request for retrieving a specific authorization model by its ID. This request fetches the complet... |
| [`GetStoreRequest`](./GetStoreRequest.md) | Request for retrieving store information by its ID. This request fetches the details of a specifi... |
| [`ListAuthorizationModelsRequest`](./ListAuthorizationModelsRequest.md) | Request for listing all authorization models in a store. This request retrieves a paginated list ... |
| [`ListObjectsRequest`](./ListObjectsRequest.md) | Request for listing objects that a user has a specific relationship with. This request finds all ... |
| [`ListStoresRequest`](./ListStoresRequest.md) | Request for listing all available stores with pagination support. This request retrieves a pagina... |
| [`ListTupleChangesRequest`](./ListTupleChangesRequest.md) | Request for listing changes to relationship tuples over time. This request retrieves a chronologi... |
| [`ListUsersRequest`](./ListUsersRequest.md) | Request for listing users who have a specific relationship with an object. This request finds all... |
| [`ReadAssertionsRequest`](./ReadAssertionsRequest.md) | Request for reading test assertions associated with an authorization model. This request retrieve... |
| [`ReadTuplesRequest`](./ReadTuplesRequest.md) | Request for reading relationship tuples that match specified criteria. This request retrieves tup... |
| [`StreamedListObjectsRequest`](./StreamedListObjectsRequest.md) | Request for streaming objects that a user has a specific relationship with. This request finds al... |
| [`WriteAssertionsRequest`](./WriteAssertionsRequest.md) | Request for writing test assertions to validate authorization model behavior. This request stores... |
| [`WriteTuplesRequest`](./WriteTuplesRequest.md) | Request for writing and deleting relationship tuples in OpenFGA. This request enables batch creat... |

---

[← Back to API Documentation](../README.md)
