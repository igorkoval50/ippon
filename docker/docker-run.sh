#!/bin/bash
source ./.env
source ./web_server/scripts/b-log.sh
LOG_LEVEL_ALL

if [ ! -f ./.env ]; then
	FATAL "File .env is not found. You must have a .env file in this directory"
	exit 1
fi

echo $PLATTFORM

DEBUG "Creating SSL certificate for localhost"
CMDS="mkcert"

for i in $CMDS
do
  # command -v will return >0 when the $i is not found
  if command -v $i >/dev/null; then
    continue
  else
    if [ $i == "mkcert" ]; then
      WARN "$i command not found. Trying to install..."
      sudo curl -L "https://github.com/FiloSottile/mkcert/releases/download/v1.4.1/mkcert-v1.4.1-linux-amd64" -o /usr/local/bin/mkcert && sudo chmod +x /usr/local/bin/mkcert

      if command -v $i >/dev/null; then
        continue
      else
        FATAL "Error installing mkcert! Read https://mkcert.dev"
        exit 1
      fi
    else
      ERROR "$i command not found."
    fi
  fi
done

mkcert  -cert-file ./data/cert/localhost.pem -key-file ./data/cert/localhost-key.pem localhost

if [[ $PLATTFORM =~ 'KENNER'  ]]; then
  if [[ ! -d ../$DOMAIN ]]; then
    mkdir -m777 -p ../$DOMAIN

    if [ $? -ne 0 ]; then
      FATAL "Check righs for create direcroty $(dirname "$(pwd)")/$DOMAIN"
      exit 1
    fi
  fi

  if [[ -w ../$DOMAIN ]]; then
    DEBUG "Directory $DOMAIN is writable"
  else
    FATAL "Directory $DOMAIN is not writable. Check rights for the directory $(dirname "$(pwd)")/$DOMAIN "
    exit 1
  fi
fi

if [ ! -f ./.containerid ]; then
	DEBUG "File .containerid is not found in this directory"
	DEBUG "Create .containerid file in this directory"
	touch .containerid
	printf "MYSQL_CONTAINER_NAME=\nAPACHE_CONTAINER_NAME=\nPROXY_CONTAINER_NAME=\n" >.containerid
fi

if grep "MYSQL_CONTAINER_NAME\|APACHE_CONTAINER_NAME\|PROXY_CONTAINER_NAME" ./.containerid; then
	project_name=$(git rev-parse --show-toplevel)
	project_name=${project_name##*/}

	if [ -z $project_name ]; then
		project_name="${PWD##*/}"
		commit='kennersoft'
	else
		commit=$(git rev-parse HEAD | awk '{print substr($0,1,7);exit}')
	fi

	hash=$project_name-$commit

	sed -i 's/^MYSQL_CONTAINER_NAME=.*/MYSQL_CONTAINER_NAME='$hash-DB'/' ./.containerid
	export MYSQL_CONTAINER_NAME=$hash-DB
	sed -i 's/^APACHE_CONTAINER_NAME=.*/APACHE_CONTAINER_NAME='$hash-WEB'/' ./.containerid
	export APACHE_CONTAINER_NAME=$hash-WEB
	sed -i 's/^PROXY_CONTAINER_NAME=.*/PROXY_CONTAINER_NAME='$hash-PROXY'/' ./.containerid
	export PROXY_CONTAINER_NAME=$hash-PROXY
else
	FATAL "The .containerid file has corrupted."
	rm -i ./.containerid
fi

export UID_U=$UID
export GID_U=$(id -g)

DEBUG "###### Down dockers container #####"
docker-compose down

DEBUG "###### Up dockers container #####"
docker-compose build
docker-compose up
