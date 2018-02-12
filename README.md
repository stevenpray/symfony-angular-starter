[![Build Status](https://travis-ci.org/stevenpray/symfony-angular-starter.svg?branch=master)](https://travis-ci.org/stevenpray/symfony-angular-starter)

# Symfony-Angular Starter

Starter project for Symfony RESTful API and Angular SPA.

## Quick Start

````
cd symfony-angular-starter
cp docker-compose.yml.dist docker-compose.yml  
docker-compose up
````

[http://localhost:8000](http://localhost:8000)  
[http://localhost:8001](http://localhost:8001)

| Username | Password | Roles              |
|----------|----------|--------------------|
| user     | user     | `ROLE_USER`        |
| admin    | admin    | `ROLE_ADMIN`       |
| super    | super    | `ROLE_SUPER_ADMIN` |

Obtain a token for API access:  
`curl -X POST http://localhost:8001/login_check -d username=admin -d password=admin`
