# TupleFilterService

Default implementation of TupleFilterServiceInterface. Provides efficient duplicate filtering for tuple operations using hash-based lookups to ensure O(n) complexity.

## Table of Contents

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [Other](#other)
  - [`filterDuplicates()`](#filterduplicates)

## Namespace

`OpenFGA\Services`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Services/TupleFilterService.php)

## Implements

- [`TupleFilterServiceInterface`](TupleFilterServiceInterface.md)

## Related Classes

- [TupleFilterServiceInterface](Services/TupleFilterServiceInterface.md) (interface)

## Methods

#### filterDuplicates

```php
public function filterDuplicates(
    ?OpenFGA\Models\Collections\TupleKeysInterface $writes,
    ?OpenFGA\Models\Collections\TupleKeysInterface $deletes,
): array

```

Filter duplicate tuples from writes and deletes collections. This method ensures that: 1. No duplicate tuples exist within the writes collection 2. No duplicate tuples exist within the deletes collection 3. If a tuple appears in both writes and deletes, it&#039;s removed from writes (delete takes precedence to ensure the final state is deletion)

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/TupleFilterService.php#L26)

#### Parameters

| Name       | Type                                                                           | Description           |
| ---------- | ------------------------------------------------------------------------------ | --------------------- |
| `$writes`  | [`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md) &#124; `null` | The writes to filter  |
| `$deletes` | [`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md) &#124; `null` | The deletes to filter |

#### Returns

`array`
