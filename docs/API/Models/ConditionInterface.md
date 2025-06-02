# ConditionInterface

Represents a condition that enables dynamic authorization in OpenFGA. Conditions allow OpenFGA to make authorization decisions based on runtime context and parameters, enabling attribute-based access control (ABAC) patterns. Rather than relying solely on static relationships, conditions evaluate expressions against dynamic data to determine if access should be granted. Conditions consist of: - **Expression**: A logical expression that evaluates to true or false - **Parameters**: Typed parameters that can be passed at evaluation time - **Name**: A unique identifier for referencing the condition - **Metadata**: Optional information about the condition definition Common condition use cases: - Time-based access (business hours, expiration dates) - Location-based restrictions (IP address, geographic region) - Resource attributes (document classification, owner validation) - User context (department, clearance level, current project) - Environmental factors (device type, authentication method) Conditions are defined in authorization models and can be referenced by relationship tuples to create dynamic permission rules. When OpenFGA evaluates a conditional relationship, it passes the current context parameters to the condition expression for evaluation. This enables sophisticated authorization patterns like &quot;allow read access to documents during business hours&quot; or &quot;grant edit permissions only to users in the same department as the resource owner.&quot;

## Namespace
`OpenFGA\Models`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/ConditionInterface.php)

## Implements
* [ModelInterface](ModelInterface.md)
* JsonSerializable

## Related Classes
* [Condition](Models/Condition.md) (implementation)



## Methods

                                                                                    
### List Operations
#### getExpression


```php
public function getExpression(): string
```

Get the condition expression. This returns the logical expression that defines when this condition evaluates to true. The expression can reference parameters and context data to enable dynamic authorization decisions based on runtime information.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ConditionInterface.php#L55)


#### Returns
string
 The condition expression defining the evaluation logic

#### getMetadata


```php
public function getMetadata(): ConditionMetadataInterface|null
```

Get metadata about the condition definition. This provides additional information about where the condition was defined and how it should be processed, which is useful for tooling and debugging.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ConditionInterface.php#L65)


#### Returns
ConditionMetadataInterface&#124;null
 The condition metadata, or null if not provided

#### getName


```php
public function getName(): string
```

Get the name of the condition. This is a unique identifier for the condition within the authorization model, allowing it to be referenced from type definitions and other parts of the model.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ConditionInterface.php#L75)


#### Returns
string
 The unique name identifying this condition

#### getParameters


```php
public function getParameters(): ConditionParametersInterface<ConditionParameterInterface>|null
```

Get the parameters available to the condition expression. These parameters define the typed inputs that can be used within the condition expression, enabling dynamic evaluation based on contextual data provided during authorization checks.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ConditionInterface.php#L86)


#### Returns
ConditionParametersInterface&lt;ConditionParameterInterface&gt;&#124;null
 The condition parameters, or null if the condition uses no parameters

### Other
#### jsonSerialize


```php
public function jsonSerialize(): array<string, mixed>
```

Serialize the condition for JSON encoding. This method prepares the condition data for API requests or storage, ensuring all components are properly formatted according to the OpenFGA API specification.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ConditionInterface.php#L97)


#### Returns
array&lt;string, mixed&gt;
 The serialized condition data

