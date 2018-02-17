[![Build Status](https://travis-ci.org/stevenpray/symfony-angular-starter.svg?branch=master)](https://travis-ci.org/stevenpray/symfony-angular-starter)
[![Maintainability](https://api.codeclimate.com/v1/badges/1c9ef90324f12c0604ab/maintainability)](https://codeclimate.com/github/stevenpray/symfony-angular-starter/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/1c9ef90324f12c0604ab/test_coverage)](https://codeclimate.com/github/stevenpray/symfony-angular-starter/test_coverage)

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
