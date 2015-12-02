#!/bin/sh

# Prepare the settings file for installation
if [ ! -f public/web/sites/default/settings.php ]
  then
    cp public/web/sites/default/default.settings.php public/web/sites/default/settings.php
    chmod 777 public/web/sites/default/settings.php
fi

# Prepare the services file for installation
if [ ! -f public/web/sites/default/services.yml ]
  then
    cp public/web/sites/default/default.services.yml public/web/sites/default/services.yml
    chmod 777 web/sites/default/services.yml
fi

# Prepare the files directory for installation
if [ ! -d public/web/sites/default/files ]
  then
    mkdir -m777 public/web/sites/default/files
fi
