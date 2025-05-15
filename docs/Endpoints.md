# Endpoints

listStores (GET /stores) ListStoresRequest(ListStoresOptions) -> ListStoresResponse
createStore (POST /stores) CreateStoreRequest(CreateStoreOptions) -> CreateStoreResponse
getStore (GET /stores/{store_id}) GetStoreRequest(GetStoreOptions) -> GetStoreResponse
deleteStore (DELETE /stores/{store_id}) DeleteStoreRequest(DeleteStoreOptions) -> DeleteStoreResponse

listAuthorizationModels (GET /stores/{store_id}/authorization-models) ListAuthorizationModelsRequest(ListAuthorizationModelsOptions) -> ListAuthorizationModelsResponse
createAuthorizationModel (POST /stores/{store_id}/authorization-models) CreateAuthorizationModelRequest(CreateAuthorizationModelOptions) -> CreateAuthorizationModelResponse
getAuthorizationModel (GET /stores/{store_id}/authorization-models/{authorization_model_id}) GetAuthorizationModelRequest(GetAuthorizationModelOptions) -> GetAuthorizationModelResponse

readAssertions (GET /stores/{store_id}/assertions/{authorization_model_id}) ReadAssertionsRequest(ReadAssertionsOptions) -> ReadAssertionsResponse
writeAssertions (PUT /stores/{store_id}/assertions/{authorization_model_id}) WriteAssertionsRequest(WriteAssertionsOptions) -> WriteAssertionsResponse

listTupleChanges (GET /stores/{store_id}/changes) ListTupleChangesRequest(ListTupleChangesOptions) -> ListTupleChangesResponse
readTuples (GET /stores/{store_id}/read) ReadTuplesRequest(ReadTuplesOptions) -> ReadTuplesResponse
writeTuples (PUT /stores/{store_id}/write) WriteTuplesRequest(WriteTuplesOptions) -> WriteTuplesResponse

check (POST /stores/{store_id}/check) CheckRequest(CheckOptions) -> CheckResponse
expand (POST /stores/{store_id}/expand) ExpandRequest(ExpandOptions) -> ExpandResponse
listObjects (POST /stores/{store_id}/list-objects) ListObjectsRequest(ListObjectsOptions) -> ListObjectsResponse
listUsers (POST /stores/{store_id}/list-users) ListUsersRequest(ListUsersOptions) -> ListUsersResponse

## Structure

/src/Requests/ListStoresRequest.php
/src/Responses/ListStoresResponse.php
/src/Options/ListStoresOptions.php
