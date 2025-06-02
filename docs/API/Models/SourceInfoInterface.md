# SourceInfoInterface

Represents source file information for OpenFGA model elements. Source information provides debugging and development context by tracking where model elements were originally defined. This is particularly valuable for: - Development tools that need to map runtime errors back to source files - IDE integrations that provide model editing and validation - Debugging complex authorization models with multiple source files - Error reporting that can point users to the exact source location - Model management tools that work across distributed definitions The source information is typically populated when authorization models are compiled from DSL files or other structured formats, allowing tools to maintain the connection between the runtime model and the original source definitions.

## Namespace
`OpenFGA\Models`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/SourceInfoInterface.php)

## Implements
* [`ModelInterface`](ModelInterface.md)
* `JsonSerializable`

## Related Classes
* [SourceInfo](Models/SourceInfo.md) (implementation)



## Methods

                                                
### List Operations
#### getFile


```php
public function getFile(): string
```

Get the source file path where the model element was defined. This provides debugging and tooling information about the original source file location for the model element. This is particularly useful for development tools, error reporting, and tracing model definitions back to their source.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/SourceInfoInterface.php#L39)


#### Returns
`string` — The source file path where the element was defined
### Other
#### jsonSerialize


```php
public function jsonSerialize(): array
```


[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/SourceInfoInterface.php#L45)


#### Returns
`array`
