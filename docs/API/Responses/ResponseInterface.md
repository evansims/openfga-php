# ResponseInterface

Base interface for all OpenFGA API response objects. This interface establishes the foundational contract for all response objects returned by the OpenFGA API. It defines the standard method for transforming raw HTTP responses into structured, validated response objects that applications can work with safely. All concrete response implementations must provide a way to parse HTTP responses while handling errors appropriately and validating data according to their specific schemas.

## Namespace
`OpenFGA\Responses`




