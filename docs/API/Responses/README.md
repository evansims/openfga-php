# Responses

[API Documentation](../README.md) > Responses

Response objects containing API results and metadata.

**Total Components:** 37

## Interfaces

| Name | Description |
|------|-------------|
| [`BatchCheckResponseInterface`](./BatchCheckResponseInterface.md) | Response containing the results of a batch authorization check. This response contains a map of c... |
| [`CheckResponseInterface`](./CheckResponseInterface.md) | Interface for permission check response objects. This interface defines the contract for response... |
| [`CreateAuthorizationModelResponseInterface`](./CreateAuthorizationModelResponseInterface.md) | Interface for authorization model creation response objects. This interface defines the contract ... |
| [`CreateStoreResponseInterface`](./CreateStoreResponseInterface.md) | Interface for store creation response objects. This interface defines the contract for responses ... |
| [`DeleteStoreResponseInterface`](./DeleteStoreResponseInterface.md) | Interface for store deletion response objects. This interface defines the contract for responses ... |
| [`ExpandResponseInterface`](./ExpandResponseInterface.md) | Interface for relationship expansion response objects. This interface defines the contract for re... |
| [`GetAuthorizationModelResponseInterface`](./GetAuthorizationModelResponseInterface.md) | Interface for authorization model retrieval response objects. This interface defines the contract... |
| [`GetStoreResponseInterface`](./GetStoreResponseInterface.md) | Interface for store retrieval response objects. This interface defines the contract for responses... |
| [`ListAuthorizationModelsResponseInterface`](./ListAuthorizationModelsResponseInterface.md) | Interface for authorization models listing response objects. This interface defines the contract ... |
| [`ListObjectsResponseInterface`](./ListObjectsResponseInterface.md) | Interface for object listing response objects. This interface defines the contract for responses ... |
| [`ListStoresResponseInterface`](./ListStoresResponseInterface.md) | Interface for stores listing response objects. This interface defines the contract for responses ... |
| [`ListTupleChangesResponseInterface`](./ListTupleChangesResponseInterface.md) | Interface for tuple changes listing response objects. This interface defines the contract for res... |
| [`ListUsersResponseInterface`](./ListUsersResponseInterface.md) | Interface for user listing response objects. This interface defines the contract for responses re... |
| [`ReadAssertionsResponseInterface`](./ReadAssertionsResponseInterface.md) | Interface for assertions reading response objects. This interface defines the contract for respon... |
| [`ReadTuplesResponseInterface`](./ReadTuplesResponseInterface.md) | Interface for tuple reading response objects. This interface defines the contract for responses r... |
| [`ResponseInterface`](./ResponseInterface.md) | Base interface for all OpenFGA API response objects. This interface establishes the foundational ... |
| [`StreamedListObjectsResponseInterface`](./StreamedListObjectsResponseInterface.md) | Response interface for streaming objects that a user has a specific relationship with. This respo... |
| [`WriteAssertionsResponseInterface`](./WriteAssertionsResponseInterface.md) | Interface for assertions writing response objects. This interface defines the contract for respon... |
| [`WriteTuplesResponseInterface`](./WriteTuplesResponseInterface.md) | Interface for tuple writing response objects. This interface defines the contract for responses r... |

## Classes

| Name | Description |
|------|-------------|
| [`BatchCheckResponse`](./BatchCheckResponse.md) | Response containing the results of a batch authorization check. This response contains a map of c... |
| [`CheckResponse`](./CheckResponse.md) | Response containing the result of an authorization check. This response indicates whether a user ... |
| [`CreateAuthorizationModelResponse`](./CreateAuthorizationModelResponse.md) | Response confirming successful creation of a new authorization model. This response provides the ... |
| [`CreateStoreResponse`](./CreateStoreResponse.md) | Response confirming successful creation of a new store. This response provides the details of the... |
| [`DeleteStoreResponse`](./DeleteStoreResponse.md) | Response confirming successful deletion of a store. This response is returned when a store has be... |
| [`ExpandResponse`](./ExpandResponse.md) | Response containing the expanded userset tree for a relationship query. This response provides a ... |
| [`GetAuthorizationModelResponse`](./GetAuthorizationModelResponse.md) | Response containing a specific authorization model from the store. This response provides the com... |
| [`GetStoreResponse`](./GetStoreResponse.md) | Response containing detailed information about a specific store. This response provides comprehen... |
| [`ListAuthorizationModelsResponse`](./ListAuthorizationModelsResponse.md) | Response containing a paginated list of authorization models. This response provides access to au... |
| [`ListObjectsResponse`](./ListObjectsResponse.md) | Response containing a list of objects that a user has a specific relationship with. This response... |
| [`ListStoresResponse`](./ListStoresResponse.md) | Response containing a paginated list of available stores. This response provides access to stores... |
| [`ListTupleChangesResponse`](./ListTupleChangesResponse.md) | Response containing a paginated list of tuple changes from the store. This response provides a co... |
| [`ListUsersResponse`](./ListUsersResponse.md) | Response containing a list of users that have a specific relationship with an object. This respon... |
| [`ReadAssertionsResponse`](./ReadAssertionsResponse.md) | Response containing test assertions associated with an authorization model. This response provide... |
| [`ReadTuplesResponse`](./ReadTuplesResponse.md) | Response containing a paginated list of relationship tuples. This response provides access to rel... |
| [`StreamedListObjectsResponse`](./StreamedListObjectsResponse.md) | Response containing streaming objects that a user has a specific relationship with. This response... |
| [`WriteAssertionsResponse`](./WriteAssertionsResponse.md) | Response confirming successful writing of test assertions. This response indicates that test asse... |
| [`WriteTuplesResponse`](./WriteTuplesResponse.md) | Response for tuple writing operations supporting both transactional and non-transactional modes. ... |

---

[← Back to API Documentation](../README.md)
