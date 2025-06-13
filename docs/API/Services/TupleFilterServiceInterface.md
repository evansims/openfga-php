# TupleFilterServiceInterface

Service for filtering and deduplicating tuple operations. This service encapsulates the business logic for handling duplicate tuples in write and delete operations, ensuring that: - No duplicate tuples exist within writes or deletes - Delete operations take precedence over writes when conflicts occur - Order is preserved based on first occurrence

## Table of Contents

* [Namespace](#namespace)
* [Source](#source)
* [Related Classes](#related-classes)
* [Methods](#methods)

* [Other](#other)
    * [`filterDuplicates()`](#filterduplicates)

## Namespace

`OpenFGA\Services`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Services/TupleFilterServiceInterface.php)

## Related Classes

* [TupleFilterService](Services/TupleFilterService.md) (implementation)

## Methods

#### filterDuplicates

```php
public function filterDuplicates(TupleKeysInterface|null $writes, TupleKeysInterface|null $deletes): array

```

Filter duplicate tuples from writes and deletes collections. This method ensures that: 1. No duplicate tuples exist within the writes collection 2. No duplicate tuples exist within the deletes collection 3. If a tuple appears in both writes and deletes, it&#039;s removed from writes (delete takes precedence to ensure the final state is deletion)

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/TupleFilterServiceInterface.php#L33)

#### Parameters

| Name       | Type                                                                           | Description           |
| ---------- | ------------------------------------------------------------------------------ | --------------------- |
| `$writes`  | [`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md) &#124; `null` | The writes to filter  |
| `$deletes` | [`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md) &#124; `null` | The deletes to filter |

#### Returns

`array`
