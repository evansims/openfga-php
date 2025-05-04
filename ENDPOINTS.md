# Endpoints

GET /stores
POST /stores
GET /stores/{store_id}
DELETE /stores/{store_id}

GET /stores/{store_id}/authorization-models
POST /stores/{store_id}/authorization-models
GET /stores/{store_id}/authorization-models/{authorization_model_id}

GET /stores/{store_id}/changes
POST /stores/{store_id}/read
POST /stores/{store_id}/write

POST /stores/{store_id}/batch-check
POST /stores/{store_id}/check
POST /stores/{store_id}/expand
POST /stores/{store_id}/list-objects
POST /stores/{store_id}/list-users
POST /stores/{store_id}/streamed-list-objects

GET /stores/{store_id}/assertions/{authorization_model_id}
POST /stores/{store_id}/assertions/{authorization_model_id}
