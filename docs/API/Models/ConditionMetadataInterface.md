# ConditionMetadataInterface

Defines metadata information for conditions in OpenFGA authorization models. ConditionMetadata provides organizational and debugging information about conditions, including the module where they&#039;re defined and source file information. This helps with model analysis, debugging, and development tooling when working with complex authorization conditions. Use this interface when building tools that need to inspect or manipulate condition metadata in authorization models.

## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](ModelInterface.md)
* JsonSerializable



## Methods
### getModule


```php
public function getModule(): string
```

Get the module name where the condition is defined. This provides organizational information about which module or namespace contains the condition definition, helping with debugging and understanding the model structure.


#### Returns
string
 The module name containing the condition

### getSourceInfo


```php
public function getSourceInfo(): SourceInfoInterface
```

Get source file information for debugging and tooling. This provides information about the source file where the condition was originally defined, which is useful for development tools, debugging, and error reporting.


#### Returns
SourceInfoInterface
 The source file information

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

