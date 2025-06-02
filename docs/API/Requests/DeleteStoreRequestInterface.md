# DeleteStoreRequestInterface

Interface for deleting an OpenFGA store. This interface defines the contract for requests that permanently remove an authorization store from OpenFGA. Deleting a store is an irreversible operation that removes all associated data including relationship tuples, authorization models, assertions, and configuration. Store deletion is typically used for: - Cleaning up test or development environments - Removing stores for discontinued projects or applications - Implementing data retention policies and compliance requirements - Freeing up resources and reducing storage costs Warning:** This operation is permanent and cannot be undone. All authorization data within the store will be lost, including relationship tuples, authorization models, and any custom configurations. Ensure you have proper backups and authorization before performing this operation.

## Namespace
`OpenFGA\Requests`

## Implements
* [RequestInterface](RequestInterface.md)



## Methods
### getRequest


```php
public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
```

Build a request context for HTTP execution. Transforms the request object into a standardized HTTP request context that can be executed by the OpenFGA HTTP client. This method handles all aspects of request preparation including parameter serialization, URL construction, header configuration, and body stream creation. The method validates that all required parameters are present and properly formatted, serializes complex objects to JSON, constructs the appropriate API endpoint URL, and creates the necessary HTTP message body streams.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$streamFactory` | StreamFactoryInterface | PSR-7 stream factory for creating request body streams from serialized data |

#### Returns
RequestContext
 The prepared request context containing HTTP method, URL, headers, and body ready for execution

### getStore


```php
public function getStore(): string
```

Get the ID of the store to delete. Returns the unique identifier of the store that will be permanently removed from OpenFGA. This operation will delete all data associated with the store, including relationship tuples, authorization models, and configuration settings. Important:** This is a destructive operation that cannot be reversed. Ensure you have the correct store ID and proper authorization before proceeding with the deletion.


#### Returns
string
 The unique identifier of the store to permanently delete

