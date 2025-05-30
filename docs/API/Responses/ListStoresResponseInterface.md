# ListStoresResponseInterface

Interface for stores listing response objects. This interface defines the contract for responses returned when listing authorization stores in OpenFGA. The response includes a collection of stores and pagination support for handling large numbers of stores efficiently. Store listing is useful for administrative operations, allowing you to discover and manage all stores within your OpenFGA instance.

## Namespace
`OpenFGA\Responses`

## Implements
* [ResponseInterface](Responses/ResponseInterface.md)



## Methods
### getContinuationToken


```php
public function getContinuationToken(): string|null
```

Get the continuation token for pagination. Returns a token that can be used to retrieve the next page of results when the total number of stores exceeds the page size limit. If null, there are no more results to fetch.


#### Returns
string | null
 The continuation token for fetching more results, or null if no more pages exist

### getStores


```php
public function getStores(): StoresInterface<StoreInterface>
```

Get the collection of stores. Returns a type-safe collection containing the store objects from the current page of results. Each store includes its metadata such as ID, name, and timestamps.


#### Returns
StoresInterface&lt;StoreInterface&gt;
 The collection of stores

