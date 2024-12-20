#!/usr/bin/env bash

# Create the .openapi directory structure if it doesn't exist
mkdir -p .openapi

rm -rf .openapi/build && mkdir .openapi/build

cp openapi.json .openapi/config.json

# Download the OpenFGA API specification if it doesn't exist
if ! [ -f .openapi/openapi.json ]; then
  curl -o .openapi/openapi.json https://raw.githubusercontent.com/openfga/api/30477608a587fbebea8940129703c11238530f71/docs/openapiv2/apidocs.swagger.json
fi

# Generate the PHP SDK using the OpenAPI Generator Docker image
docker run --rm \
  -v ${PWD}/.openapi:/.openapi \
  openapitools/openapi-generator-cli:v7.10.0 generate \
  -i .openapi/openapi.json \
  -o .openapi/build \
  -c .openapi/config.json \
  -g php \
  --http-user-agent="openfga-sdk php/0.1" \

# Clear out the existing Models directory
rm -rf src/API/Models
mkdir src/API/Models

# Copy the contents of .openapi/build/lib/Model into src/API/Models
cp -r .openapi/build/lib/Models/* src/API/Models

# Run PHP CS Fixer on the generated code
export PHP_CS_FIXER_IGNORE_ENV=1
vendor/bin/php-cs-fixer fix src/API/Models --quiet
