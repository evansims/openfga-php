# StreamedListObjectsResponse

Response containing streaming objects that a user has a specific relationship with. This response processes a streaming HTTP response and yields object identifiers as they are received from the server. This allows for memory-efficient processing of large result sets without loading the entire dataset into memory.

## Namespace
`OpenFGA\Responses`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Responses/StreamedListObjectsResponse.php)

## Implements
* [StreamedListObjectsResponseInterface](StreamedListObjectsResponseInterface.md)

## Related Classes
* [StreamedListObjectsResponseInterface](Responses/StreamedListObjectsResponseInterface.md) (interface)
* [StreamedListObjectsRequest](Requests/StreamedListObjectsRequest.md) (request)



## Methods

                                                
### List Operations
#### getObject


```php
public function getObject(): string
```

Get a single object identifier from a streamed response chunk.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/StreamedListObjectsResponse.php#L125)


#### Returns
string
 The object identifier

### Other
#### fromResponse

*<small>Implements Responses\StreamedListObjectsResponseInterface</small>*  

```php
public function fromResponse(
    HttpResponseInterface $response,
    HttpRequestInterface $request,
    SchemaValidator $validator,
): Generator<int, StreamedListObjectsResponseInterface>
```

Create a streaming response from an HTTP response. Processes the streaming HTTP response and returns a Generator that yields individual object identifiers as they are received from the server.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/StreamedListObjectsResponseInterface.php#L42)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$response` | HttpResponseInterface | The HTTP response from the API |
| `$request` | HttpRequestInterface | The original HTTP request |
| `$validator` | SchemaValidator | Schema validator for response validation |

#### Returns
Generator&lt;int, [StreamedListObjectsResponseInterface](StreamedListObjectsResponseInterface.md)&gt;
 Generator yielding response objects

