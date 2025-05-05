# Endpoints

listStores (GET /stores)
createStore (POST /stores)
getStore (GET /stores/{store_id})
deleteStore (DELETE /stores/{store_id})

listAuthorizationModels (GET /stores/{store_id}/authorization-models)
createAuthorizationModel (POST /stores/{store_id}/authorization-models)
getAuthorizationModel (GET /stores/{store_id}/authorization-models/{authorization_model_id})

listChanges (GET /stores/{store_id}/changes)
read (POST /stores/{store_id}/read)
write (POST /stores/{store_id}/write)

batchCheck (POST /stores/{store_id}/batch-check)
check (POST /stores/{store_id}/check)
expand (POST /stores/{store_id}/expand)
listObjects (POST /stores/{store_id}/list-objects)
listUsers (POST /stores/{store_id}/list-users)
streamedListObjects (POST /stores/{store_id}/streamed-list-objects)

readAssertions (GET /stores/{store_id}/assertions/{authorization_model_id})
writeAssertions (POST /stores/{store_id}/assertions/{authorization_model_id})
