#!/bin/sh

# Prepare the settings file for installation
if [ ! -f /app/public/web/sites/default/settings.php ]
  then
    cp /app/public/web/sites/default/default.settings.php /app/public/web/sites/default/settings.php
    chmod 777 /app/public/web/sites/default/settings.php
fi

# Prepare the services file for installation
if [ ! -f /app/public/web/sites/default/services.yml ]
  then
    cp /app/public/web/sites/default/default.services.yml /app/public/web/sites/default/services.yml
    chmod 777 /app/public/web/sites/default/services.yml
fi

# Prepare the files directory for installation
if [ ! -d /app/public/web/sites/default/files ]
  then
    mkdir -m777 /app/public/web/sites/default/files
fi
