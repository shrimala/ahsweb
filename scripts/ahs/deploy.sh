#!/bin/bash
cd web
drush --yes cache-rebuild
drush --yes updatedb
drush config-set "system.site" uuid "2748a6a9-0c67-43c6-aa47-71df4737a69f" --yes
for module in `cat ../modules.common`; do drush --yes pm-enable $module; done
for module in `cat ../modules.dev`; do drush --yes pm-enable $module; done
drush cc drush
drush --yes config-split-import
# the duplicate is necessary to fix some weirdness
drush --yes config-split-import
drush --yes entup
