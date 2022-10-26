# Clockify export

## Nastavení .envů

- z ./.docker/.env.example vytvořit ./.docker/.env a nastavit v něm vlastní hodnoty
- z ./.env.local.example vytvořit ./.env.local a doplnit hodnoty podle předchozího envu z ./.docker/

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