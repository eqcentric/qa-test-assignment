# QA Engineer Test Assignment

## Goal

Provide test scenarios for web services.

## Description

You are given the following environment:

1. PHP API application
2. Adapter Microservice
3. MongoDB database

Application behavior:

- PHP API handles MongoDB connection and data operations
- Microservice provides configuration for MongoDB pipelines and data source
- `GET {{ADAPTER_URL}}/pipeline` endpoint returns MongoDB pipeline in JSON
- `POST {{ADAPTER_URL}}/sync/:id` endpoint sends data to API to store in database
- `POST {{API_URL}}/data/:id` endpoint receives data from adapter
- `POST {{API_URL}}/setup/:id` endpoint fetches pipeline config from adapter and applies it to database
- `GET {{API_URL}}/data/:id` endpoint returns output data

You need to setup test scenarios for the following workflows:

### PHP unit tests

Provide unit tests for PHP API. Tests should implement mocks for adapter API and MongoDB. No real connections are allowed.

- `POST {{API_URL}}/setup/:id` should call to adapter API with `id` parameter
- `POST {{API_URL}}/setup/:id` should create provided pipeline in MongoDB
- `GET {{API_URL}}/data/:id` should query data from database

You are free to modify PHP code in any way you need, while maintaining correct app behavior.

Refer to documentation on [Slim Framework](https://www.slimframework.com/docs/v4/).
Closures in the code can be replaced with invokable classes, database and API clients can be provided via DI container or any other way you find suitable.

### Integration tests

Provide a blackbox test suite that executes requests to an adapter and asserts the following:

- Adapter returns correct MongoDB pipeline syntax
- MongoDB pipeline can be successfully created in real database
- Results of MongoDB pipeline contain following fields (all fields are required)
    - `key`: `string`
    - `parent_key`: `string|null`
    - `model_key`: `string|null`
    - `model`: `string|null`
    - `critical`: `boolean`

You are free to use any available testing frameworks and programming languages.

You should use an actual MongoDB instance for these tests. Sample data is available in `assets.json` file.

## Setup

The repository contains source code for API and adapter, docker-compose file for test environment and Postman collection with all endpoints setup.

## Submitting results

DO NOT create pull requests to assignment repository. You can submit results in a new repository, or as a zip file.

Zip files should be sent to `roman@makini.io`. Repositories should be accessible to Github/Gitlab account `rkgrep`
