# WriteAssertionsResponseInterface

Interface for assertions writing response objects. This interface defines the contract for responses returned when writing assertions to an OpenFGA authorization model. Assertion writing responses typically contain no additional data beyond the successful HTTP status, indicating that the assertions have been successfully stored. Assertions are test cases that validate the behavior of authorization models by specifying expected permission check results, helping ensure model correctness.

## Namespace
`OpenFGA\Responses`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Responses/WriteAssertionsResponseInterface.php)

## Implements
* [ResponseInterface](ResponseInterface.md)

## Related Classes
* [WriteAssertionsResponse](Responses/WriteAssertionsResponse.md) (implementation)
* [WriteAssertionsRequestInterface](Requests/WriteAssertionsRequestInterface.md) (request)



