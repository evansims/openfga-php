# ResponseInterface

Base interface for all OpenFGA API response objects. This interface establishes the foundational contract for all response objects returned by the OpenFGA API. It defines the standard method for transforming raw HTTP responses into structured, validated response objects that applications can work with safely. All concrete response implementations must provide a way to parse HTTP responses while handling errors appropriately and validating data according to their specific schemas.

## Table of Contents

* [Namespace](#namespace)
* [Source](#source)
* [Related Classes](#related-classes)

## Namespace

`OpenFGA\Responses`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Responses/ResponseInterface.php)

## Related Classes

* [Response](Responses/Response.md) (implementation)
* [RequestInterface](Requests/RequestInterface.md) (request)
