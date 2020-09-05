#!/bin/sh

container=$(grep -Po 'APACHE_CONTAINER_NAME=\K(.*)' ./.containerid)

docker exec -it -e XDEBUG_CONFIG="remote_host=$(ip -4 addr show docker0 | grep -Po 'inet \K[\d.]+')" -e PHP_IDE_CONFIG="serverName=localhost" -e SHOPWARE_ENV=dev ${container} bash
