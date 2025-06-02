# WriteTuplesResponseInterface

Interface for tuple writing response objects. This interface defines the contract for responses returned when writing relationship tuples to an OpenFGA store. Tuple writing responses typically contain no additional data beyond the successful HTTP status, indicating that the write and delete operations have been successfully applied. Tuple writing operations are transactional, meaning either all changes succeed or all changes are rolled back, ensuring data consistency in the authorization graph.

## Namespace
`OpenFGA\Responses`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Responses/WriteTuplesResponseInterface.php)

## Implements
* [ResponseInterface](ResponseInterface.md)



