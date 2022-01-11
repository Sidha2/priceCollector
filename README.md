# Price collector

## Usage

Run docker containers
```
docker-compose up -d
```

Web service is available on http://localhost

MariaDB is available from web service container on host `db:3306`, otherwise on `127.0.0.1:3306`

phpMyAdmin is available on http://localhost:8080

___
*If you want to persist mysql data, to stop containers, use*
```
docker-compose stop
```
