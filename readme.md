# Clockify export

## Spuštění dockeru

```shell
~/.docker$ docker-compose -f docker-compose.yml up --build
```


## Spuštění migrace

```shell
docker exec -it docker_php_1 php bin/console doctrine:migrations:migrate
```

nebo 

```shell
docker exec -it docker_php_1 bash 
php bin/console doctrine:migrations:migrate
```

## Spuštění commandu

```shell
docker exec -it docker_php_1 php bin/console app:clockify
```

nebo 

```shell
docker exec -it docker_php_1 bash 
php bin/console app:clockify
```

## Vygenerovaný soubor

`var/results.txt`