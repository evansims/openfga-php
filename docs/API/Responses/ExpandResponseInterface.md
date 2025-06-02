# ExpandResponseInterface

Interface for relationship expansion response objects. This interface defines the contract for responses returned when expanding relationships in OpenFGA. An expand response contains a tree structure that shows all the users and usersets that have a particular relationship with an object, providing a comprehensive view of the authorization graph. Relationship expansion is useful for understanding complex authorization structures, debugging permission issues, and visualizing how relationships are resolved.

## Namespace
`OpenFGA\Responses`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Responses/ExpandResponseInterface.php)

## Implements
* [`ResponseInterface`](ResponseInterface.md)

## Related Classes
* [ExpandResponse](Responses/ExpandResponse.md) (implementation)
* [ExpandRequestInterface](Requests/ExpandRequestInterface.md) (request)

## Methods

#### getTree

```php
public function getTree(): UsersetTreeInterface|null
```

Get the expansion tree for the queried relationship. Returns a hierarchical tree structure that represents all users and usersets that have the specified relationship with the target object. The tree shows both direct relationships and computed relationships through other relations.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/ExpandResponseInterface.php#L45)

#### Returns
[`UsersetTreeInterface`](Models/UsersetTreeInterface.md) &#124; `null` — The relationship expansion tree, or null if no relationships found
