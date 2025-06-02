# ListTupleChangesResponseInterface

Interface for tuple changes listing response objects. This interface defines the contract for responses returned when listing changes to relationship tuples in OpenFGA. The response includes a collection of tuple changes and pagination support for handling large change sets efficiently. Tuple change listing is essential for auditing authorization modifications, implementing change feeds, and tracking the evolution of relationship data over time.

## Namespace
`OpenFGA\Responses`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Responses/ListTupleChangesResponseInterface.php)

## Implements
* [`ResponseInterface`](ResponseInterface.md)

## Related Classes
* [ListTupleChangesResponse](Responses/ListTupleChangesResponse.md) (implementation)
* [ListTupleChangesRequestInterface](Requests/ListTupleChangesRequestInterface.md) (request)



## Methods

                                    
#### getChanges


```php
public function getChanges(): TupleChangesInterface<TupleChangeInterface>
```

Get the collection of tuple changes. Returns a type-safe collection containing the tuple change objects from the current page of results. Each change represents a modification (insert or delete) to the relationship data, including timestamps and operation details.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/ListTupleChangesResponseInterface.php#L46)


#### Returns
[`TupleChangesInterface`](Models/Collections/TupleChangesInterface.md)&lt;[`TupleChangeInterface`](Models/TupleChangeInterface.md)&gt; — The collection of tuple changes
#### getContinuationToken


```php
public function getContinuationToken(): string|null
```

Get the continuation token for pagination. Returns a token that can be used to retrieve the next page of results when the total number of tuple changes exceeds the page size limit. If null, there are no more results to fetch.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/ListTupleChangesResponseInterface.php#L57)


#### Returns
`string` &#124; `null` — The continuation token for fetching more results, or null if no more pages exist
