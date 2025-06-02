# ListStoresResponseInterface

Interface for stores listing response objects. This interface defines the contract for responses returned when listing authorization stores in OpenFGA. The response includes a collection of stores and pagination support for handling large numbers of stores efficiently. Store listing is useful for administrative operations, allowing you to discover and manage all stores within your OpenFGA instance.

## Namespace
`OpenFGA\Responses`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Responses/ListStoresResponseInterface.php)

## Implements
* [`ResponseInterface`](ResponseInterface.md)

## Related Classes
* [ListStoresResponse](Responses/ListStoresResponse.md) (implementation)
* [ListStoresRequestInterface](Requests/ListStoresRequestInterface.md) (request)



## Methods

                                    
#### getContinuationToken


```php
public function getContinuationToken(): string|null
```

Get the continuation token for pagination. Returns a token that can be used to retrieve the next page of results when the total number of stores exceeds the page size limit. If null, there are no more results to fetch.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/ListStoresResponseInterface.php#L45)


#### Returns
`string` &#124; `null` — The continuation token for fetching more results, or null if no more pages exist
#### getStores


```php
public function getStores(): StoresInterface<StoreInterface>
```

Get the collection of stores. Returns a type-safe collection containing the store objects from the current page of results. Each store includes its metadata such as ID, name, and timestamps.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/ListStoresResponseInterface.php#L55)


#### Returns
[`StoresInterface`](Models/Collections/StoresInterface.md)&lt;[`StoreInterface`](Models/StoreInterface.md)&gt; — The collection of stores
