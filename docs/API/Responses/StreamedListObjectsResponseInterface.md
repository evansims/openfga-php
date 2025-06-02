# StreamedListObjectsResponseInterface

Response interface for streaming objects that a user has a specific relationship with. This response provides a Generator that yields object identifiers as they are streamed from the server. This allows for memory-efficient processing of large result sets without loading the entire dataset into memory at once.

## Namespace
`OpenFGA\Responses`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Responses/StreamedListObjectsResponseInterface.php)


## Related Classes
* [StreamedListObjectsResponse](Responses/StreamedListObjectsResponse.md) (implementation)
* [StreamedListObjectsRequestInterface](Requests/StreamedListObjectsRequestInterface.md) (request)



## Methods

                        
#### getObject


```php
public function getObject(): string
```

Get a single object identifier from a streamed response chunk.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/StreamedListObjectsResponseInterface.php#L53)


#### Returns
`string` — The object identifier
