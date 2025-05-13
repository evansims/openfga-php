$client->models()->create (POST /stores/{store_id}/authorization-models)
$client->models()->list (GET /stores/{store_id}/authorization-models)
$client->models()->get (GET /stores/{store_id}/authorization-models/{authorization_model_id})

$client->assertions()->get (GET /stores/{store_id}/assertions/{authorization_model_id})
$client->assertions()->put (PUT /stores/{store_id}/assertions/{authorization_model_id})

$client->stores()->create (POST /stores)
$client->stores()->list (GET /stores)
$client->stores()->get (GET /stores/{store_id})
$client->stores()->delete (DELETE /stores/{store_id})

$client->tuples()->changes (GET /stores/{store_id}/changes)
$client->tuples()->read (GET /stores/{store_id}/read)
$client->tuples()->write (PUT /stores/{store_id}/write)

$client->queries()->check (POST /stores/{store_id}/check)
$client->queries()->expand (POST /stores/{store_id}/expand)
$client->queries()->listObjects (POST /stores/{store_id}/list-objects)
$client->queries()->listUsers (POST /stores/{store_id}/list-users)
