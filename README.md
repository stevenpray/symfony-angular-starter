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

Usename: password  
Password: password  
  
`curl -X POST http://localhost:8001/login_check -d username=password -d password=password`
