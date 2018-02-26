[![Build Status](https://img.shields.io/travis/stevenpray/symfony-angular-starter/master.svg?style=flat-square)](https://travis-ci.org/stevenpray/symfony-angular-starter)
[![Maintainability](https://img.shields.io/codeclimate/maintainability/stevenpray/symfony-angular-starter.svg?style=flat-square)](https://codeclimate.com/github/stevenpray/symfony-angular-starter/maintainability)
[![Test Coverage](https://img.shields.io/codeclimate/c/stevenpray/symfony-angular-starter.svg?style=flat-square)](https://codeclimate.com/github/stevenpray/symfony-angular-starter/test_coverage)

# Symfony-Angular Starter

Starter project for Symfony RESTful API and Angular SPA.

## Quick Start

````
cd symfony-angular-starter
cp docker-compose.yml.dist docker-compose.yml  
docker-compose up
````

### Client

[http://localhost:8000](http://localhost:8000)  

| Username | Password | Roles              |
|----------|----------|--------------------|
| user     | user     | `ROLE_USER`        |
| admin    | admin    | `ROLE_ADMIN`       |
| super    | super    | `ROLE_SUPER_ADMIN` |


### Server

[http://localhost:8001](http://localhost:8001)

Obtain a token for API access:  
`curl -X POST http://localhost:8001/login -d username=admin -d password=admin`

Swagger API Docs  
[http://localhost:8001/api/docs.json](http://localhost:8001/api/docs.json)
