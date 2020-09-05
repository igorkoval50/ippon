#!/bin/bash

source /scripts/b-log.sh
LOG_LEVEL_ALL
source /scripts/utils.sh
NOTICE "We are using database ${MYSQL_DATABASE} for platform ${PLATTFORM}"

if ! CheckBb; then
  if [ ${PLATTFORM} == "OXID6" ]; then
    /var/www/html/vendor/bin/oe-eshop-db_views_regenerate
  fi

  if [ ${PLATTFORM} == "MAGENTO2" ]; then
    mysql -u $MYSQL_USER -p$MYSQL_PASSWORD -h database_server -e "USE ${MYSQL_DATABASE};
        SELECT TABLE_NAME INTO @table_name FROM information_schema.tables as tbb WHERE tbb.TABLE_NAME LIKE \"%core_config_data\" LIMIT 1;
        SET @sql := CONCAT(\"UPDATE \", @table_name ,\" SET value = 'http://localhost:8081/' WHERE path IN ('web/unsecure/base_url', 'web/secure/base_url');\");
        PREPARE update_query FROM @sql;
        EXECUTE update_query;
        DEALLOCATE PREPARE update_query;
        SET @cookie := CONCAT(\"UPDATE \", @table_name ,\" SET value = '' WHERE path = 'web/cookie/cookie_domain';\");
        PREPARE cookie_query FROM @cookie;
        EXECUTE cookie_query;
        DEALLOCATE PREPARE cookie_query;" 2>/var/log/docker/mysql.err
  fi

  if [ ${PLATTFORM} == "SHOPWARE" ]; then
    #uncomment below for set cron

    #crontab -l -u www-data | {
    #	cat
    #	printf "\n* * * * * php /var/www/html/bin/console sw:cron:run\n"
    #} | crontab -u www-data -

    DEBUG "Current crontab is $(crontab -l -u www-data)"

    mysql -u $MYSQL_USER -p$MYSQL_PASSWORD -h database_server -e "USE ${MYSQL_DATABASE};
        SET @sql := CONCAT(\"UPDATE s_core_shops SET host='localhost:8081', secure=0 LIMIT 1;\");
        PREPARE update_query FROM @sql;
        EXECUTE update_query;
        DEALLOCATE PREPARE update_query;" 2>/var/log/docker/mysql.err
  fi

  if [ ${PLATTFORM} == "SHOPWARE6" ]; then
    #uncomment for cron

    #crontab -l -u www-data | {
    #	cat
    #	printf "\n* * * * * php /var/www/html/bin/console scheduled-task:run --time-limit=180\n"
    #} | crontab -u www-data -

    DEBUG "Current crontab is $(crontab -l -u www-data)"

    mysql -u $MYSQL_USER -p$MYSQL_PASSWORD -h database_server -e "USE ${MYSQL_DATABASE};
        SET @sql := CONCAT(\"UPDATE sales_channel_domain SET url='http://localhost:8081' LIMIT 1;\");
        PREPARE update_query FROM @sql;
        EXECUTE update_query;
        DEALLOCATE PREPARE update_query;" 2>/var/log/docker/mysql.err

    DEBUG "Clearing shopware 6 cache..."
    cd /var/www/html && sudo -u www-data php bin/console cache:clear

    if [ ! -f "/var/www/html/config/jwt/private.pem" ]; then
      DEBUG "Generating JWT certs..."
      sudo -u www-data php /var/www/html/bin/console system:generate-jwt-secret
      chown -R www-data:www-data /var/www/html/config/jwt
      chmod -R 644 /var/www/html/config/jwt/*

      DEBUG "Compiling theme and installing assets... It takes a few minutes, please wait!!!"
      cd /var/www/html && sudo -u www-data php bin/console theme:compile && sudo -u www-data php bin/console assets:install && sudo -u www-data php bin/console dal:refresh:index
    fi
  fi

  if [[ ${PLATTFORM} == "KENNERPIM" || ${PLATTFORM} == "KENNERCORE" ]]; then
    crontab -l -u www-data | {
      cat
      printf "\n* * * * * php /var/www/html/${DOMAIN}/index.php cron\n"
    } | crontab -u www-data -

    DEBUG "Current crontab is $(crontab -l -u www-data)"
  fi

else
  ERROR "Database for ${PLATTFORM} is not loaded!"
fi
