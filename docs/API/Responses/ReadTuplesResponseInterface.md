# ReadTuplesResponseInterface

Interface for tuple reading response objects. This interface defines the contract for responses returned when reading relationship tuples from OpenFGA. The response includes a collection of tuples matching the query criteria and pagination support for handling large result sets efficiently. Tuple reading is essential for querying existing relationships, auditing authorization data, and implementing administrative interfaces for relationship management.

## Namespace
`OpenFGA\Responses`

## Implements
* [ResponseInterface](ResponseInterface.md)



## Methods
### getContinuationToken


```php
public function getContinuationToken(): string|null
```

Get the continuation token for pagination. Returns a token that can be used to retrieve the next page of results when the total number of matching tuples exceeds the page size limit. If null, there are no more results to fetch.


#### Returns
string|null
 The continuation token for fetching more results, or null if no more pages exist

### getTuples


```php
public function getTuples(): TuplesInterface<TupleInterface>
```

Get the collection of relationship tuples. Returns a type-safe collection containing the tuple objects that match the read query criteria. Each tuple represents a relationship between a user and an object through a specific relation.


#### Returns
TuplesInterface&lt;TupleInterface&gt;
 The collection of relationship tuples

