# ListTupleChangesResponse


## Namespace
`OpenFGA\Responses`

## Implements
* [ListTupleChangesResponseInterface](Responses/ListTupleChangesResponseInterface.md)
* [ResponseInterface](Responses/ResponseInterface.md)



## Methods
### fromResponse

*<small>Implements Responses\ListTupleChangesResponseInterface</small>*  

```php
public function fromResponse(Psr\Http\Message\ResponseInterface $response, Psr\Http\Message\RequestInterface $request, [SchemaValidator](Schema/SchemaValidator.md) $validator): self
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$response` | `Psr\Http\Message\ResponseInterface` |  |
| `$request` | `Psr\Http\Message\RequestInterface` |  |
| `$validator` | `[SchemaValidator](Schema/SchemaValidator.md)` |  |

#### Returns
`self`

### getChanges


```php
public function getChanges(): [TupleChangesInterface](Models/Collections/TupleChangesInterface.md)
```



#### Returns
`[TupleChangesInterface](Models/Collections/TupleChangesInterface.md)`

### getContinuationToken


```php
public function getContinuationToken(): ?string
```



#### Returns
`?string`

### schema

*<small>Implements Responses\ListTupleChangesResponseInterface</small>*  

```php
public function schema(): [SchemaInterface](Schema/SchemaInterface.md)
```



#### Returns
`[SchemaInterface](Schema/SchemaInterface.md)`

