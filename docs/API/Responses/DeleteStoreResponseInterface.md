# DeleteStoreResponseInterface

Interface for store deletion response objects. This interface defines the contract for responses returned when deleting stores from OpenFGA. Store deletion responses typically contain no additional data beyond the successful HTTP status, indicating that the store has been marked for deletion. Store deletion is a destructive operation that removes all authorization data associated with the store, including relationship tuples and authorization models.

## Namespace
`OpenFGA\Responses`

## Implements
* [ResponseInterface](Responses/ResponseInterface.md)



