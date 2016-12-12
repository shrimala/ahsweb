#!/bin/bash
mkdir tmp
mkdir private
cd web
drush --yes cache-rebuild
drush --yes updatedb
for module in `cat ../modules.common`; do drush --yes pm-enable $module; done
for module in `cat ../modules.dev`; do drush --yes pm-enable $module; done

#don't uninstall dev modules when importing config
drush config-set "system.site" uuid "2748a6a9-0c67-43c6-aa47-71df4737a69f" --yes
drush --yes config-import --skip-modules="$(echo $(cat ../modules.dev) | tr " " "," )"

drush --yes sql-sanitize --sanitize-email="no"
for module in `cat ../modules.master`; do drush --yes pm-uninstall $module; done
drush cc drush