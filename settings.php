<?php
/**
 * @file
 * Platform.sh example settings.php file for Drupal 8.
 */

// Default Drupal 8 settings.
//
// These are already explained with detailed comments in Drupal's
// default.settings.php file.
//
// See https://api.drupal.org/api/drupal/sites!default!default.settings.php/8
$databases = [];
$config_directories = [];
$settings['update_free_access'] = FALSE;
$settings['container_yamls'][] = __DIR__ . '/services.yml';

// Install with the 'standard' profile for this example.
//
// As the settings.php file is not writable during install on Platform.sh (for
// good reasons), Drupal will refuse to install a profile that is not defined
// here.
$settings['install_profile'] = 'standard';

// You should modify the hash_salt so that it is specific to your application.
//
// You can do this with a Platform.sh environment variable (drupal:hash_salt or
// d8settings:hash_salt).
$settings['hash_salt'] = '4946c1912834b8477cc70af309a2c30dcec24c2103c724ff30bf13b4c10efd82';

// Set up a config sync directory.
//
// This is defined inside the read-only "config" directory. This works well,
// however it requires a patch from issue https://www.drupal.org/node/2607352
// to fix the requirements check and the installer.
$config_directories[CONFIG_SYNC_DIRECTORY] = __DIR__ . '/files/ahsweb/config/sync';

// Automatic Platform.sh settings.
if (file_exists(__DIR__ . '/settings.platformsh.php')) {
  include __DIR__ . '/settings.platformsh.php';
}

// Local settings. These come last so that they can override anything.
if (file_exists(__DIR__ . '/settings.local.php')) {
  include __DIR__ . '/settings.local.php';
}
