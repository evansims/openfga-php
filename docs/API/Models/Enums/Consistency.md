# Consistency

Consistency levels for OpenFGA authorization queries. This enum defines the available consistency levels that control the trade-off between data consistency and query performance in OpenFGA operations. Different consistency levels affect how fresh the data needs to be when processing authorization checks.

## Namespace

`OpenFGA\Models\Enums`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/Enums/Consistency.php)

## Implements

* `UnitEnum`
* `BackedEnum`

## Constants

| Name                 | Value                | Description                                                                                                                                                                                                                                     |
| -------------------- | -------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `HIGHER_CONSISTENCY` | `HIGHER_CONSISTENCY` | Prioritize data consistency over query performance. This option ensures that authorization checks are performed against the most up-to-date data, potentially at the cost of increased latency. Use when accuracy is more important than speed. |
| `MINIMIZE_LATENCY`   | `MINIMIZE_LATENCY`   | Prioritize query performance over data consistency. This option allows for faster authorization checks by potentially using slightly stale data. Use when speed is more important than having the absolute latest data.                         |
| `UNSPECIFIED`        | `UNSPECIFIED`        | Use the default consistency level determined by the OpenFGA server. This option delegates the consistency decision to the server&#039;s configuration, which may change based on deployment settings.                                           |

## Cases

| Name                 | Value                | Description                                                                                                                                                                                                                                     |
| -------------------- | -------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `HIGHER_CONSISTENCY` | `HIGHER_CONSISTENCY` | Prioritize data consistency over query performance. This option ensures that authorization checks are performed against the most up-to-date data, potentially at the cost of increased latency. Use when accuracy is more important than speed. |
| `MINIMIZE_LATENCY`   | `MINIMIZE_LATENCY`   | Prioritize query performance over data consistency. This option allows for faster authorization checks by potentially using slightly stale data. Use when speed is more important than having the absolute latest data.                         |
| `UNSPECIFIED`        | `UNSPECIFIED`        | Use the default consistency level determined by the OpenFGA server. This option delegates the consistency decision to the server&#039;s configuration, which may change based on deployment settings.                                           |

## Methods

### List Operations

#### getDescription

```php
public function getDescription(): string

```

Get a user-friendly description of this consistency level. Provides a descriptive explanation of what this consistency level means for query behavior and performance characteristics.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Enums/Consistency.php#L55)

#### Returns

`string` — A descriptive explanation of the consistency level

### Utility

#### prioritizesConsistency

```php
public function prioritizesConsistency(): bool

```

Check if this consistency level prioritizes data freshness. Useful for determining if a query will potentially have higher latency in exchange for more up-to-date data.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Enums/Consistency.php#L72)

#### Returns

`bool` — True if consistency is prioritized over performance, false otherwise

### Other

#### prioritizesPerformance

```php
public function prioritizesPerformance(): bool

```

Check if this consistency level prioritizes query performance. Useful for determining if a query will potentially use stale data in exchange for better performance.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Enums/Consistency.php#L88)

#### Returns

`bool` — True if performance is prioritized over consistency, false otherwise
