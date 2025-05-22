# ReadTuplesResponse


## Namespace
`OpenFGA\Responses`

## Implements
* [ReadTuplesResponseInterface](Responses/ReadTuplesResponseInterface.md)
* [ResponseInterface](Responses/ResponseInterface.md)



## Methods
### fromResponse

*<small>Implements Responses\ReadTuplesResponseInterface</small>*  

```php
public function fromResponse(Psr\Http\Message\ResponseInterface $response, [SchemaValidator](Schema/SchemaValidator.md) $validator): self
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$response` | `Psr\Http\Message\ResponseInterface` |  |
| `$validator` | `[SchemaValidator](Schema/SchemaValidator.md)` |  |

#### Returns
`self`

### getContinuationToken


```php
public function getContinuationToken(): ?string
```



#### Returns
`?string`

### getTuples


```php
public function getTuples(): [TuplesInterface](Models/Collections/TuplesInterface.md)
```



#### Returns
`[TuplesInterface](Models/Collections/TuplesInterface.md)`

### schema

*<small>Implements Responses\ReadTuplesResponseInterface</small>*  

```php
public function schema(): [SchemaInterface](Schema/SchemaInterface.md)
```



#### Returns
`[SchemaInterface](Schema/SchemaInterface.md)`

