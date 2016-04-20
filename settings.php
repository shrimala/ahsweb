<?php
/**
 * @file
 * Drupal 8 settings.php file.
 */

//#############################
//STANDARD PLATFORM.SH SETTINGS

// Default Drupal 8 settings.
$databases = [];
$config_directories = [];
$settings['update_free_access'] = FALSE;
$settings['container_yamls'][] = __DIR__ . '/services.yml';
$settings['install_profile'] = 'standard';

// Set up a config sync directory.
//
// This is defined inside the read-only "config" directory. This works well,
// however it requires a patch from issue https://www.drupal.org/node/2607352
// to fix the requirements check and the installer.
$config_directories[CONFIG_SYNC_DIRECTORY] = __DIR__ . '/config/sync';

// Define a config sync directory outside the document root.
/*if (isset($_ENV['PLATFORM_APP_DIR'])) {
  $config_directories[CONFIG_SYNC_DIRECTORY] = $_ENV['PLATFORM_APP_DIR'] . '/config/sync';
}
*/ 

// Override paths for config files in Platform.sh.
//if (isset($_ENV['PLATFORM_APP_DIR'])) {
//  $config_directories = array(
//    CONFIG_ACTIVE_DIRECTORY => $_ENV['PLATFORM_APP_DIR'] . '/config/active',
//    CONFIG_SYNC_DIRECTORY => $_ENV['PLATFORM_APP_DIR'] . '/config/staging',
//  );
//}

// Load automatic Platform.sh settings.
if (file_exists(__DIR__ . '/settings.platformsh.php')) {
  include __DIR__ . '/settings.platformsh.php';
}




/*****************************************************/
// AHS CUSTOMISATIONS
//Please state the purpose of each customisation, which feature requires it


// Set base url, needed by simplenews module
$main_route_url = 'http://{default}/';
$routes = json_decode(base64_decode($_ENV['PLATFORM_ROUTES']),true);
foreach ($routes as $route_url => $route_info) {
  if ($route_info["original_url"] == $main_route_url) {
    $base_url = $route_url;
    break;
  }
}
$base_url = rtrim($base_url,'/');

// Local settings. Used to override settings in local development environment.
if (file_exists(__DIR__ . '/settings.dev.php')) {
  include __DIR__ . '/settings.dev.php';
}
