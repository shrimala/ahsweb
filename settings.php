<?php
/**
 * @file
 * Platform.sh example settings.php file for Drupal 8.
 */

// Install with the 'standard' profile for this example.
$settings['install_profile'] = 'standard';

// You should modify the hash_salt so that it is specific to your application.
$settings['hash_salt'] = '4946c1912834b8477cc70af309a2c30dcec24c2103c724ff30bf13b4c10efd82';

/**
 * Default Drupal 8 settings.
 *
 * These are already explained with detailed comments in Drupal's
 * default.settings.php file.
 *
 * See https://api.drupal.org/api/drupal/sites!default!default.settings.php/8
 */
$databases = array();
$config_directories = array();
$settings['update_free_access'] = FALSE;
$settings['container_yamls'][] = __DIR__ . '/services.yml';

// Define a config sync directory outside the document root.
/*if (isset($_ENV['PLATFORM_APP_DIR'])) {
  $config_directories[CONFIG_SYNC_DIRECTORY] = $_ENV['PLATFORM_APP_DIR'] . '/config/sync';
}
*/ 

// Override paths for config files in Platform.sh.
if (isset($_ENV['PLATFORM_APP_DIR'])) {
  $config_directories = array(
    CONFIG_ACTIVE_DIRECTORY => $_ENV['PLATFORM_APP_DIR'] . '/config/active',
    CONFIG_SYNC_DIRECTORY => $_ENV['PLATFORM_APP_DIR'] . '/config/staging',
  );
}

// Set trusted hosts based on real Platform.sh routes.
if (isset($_ENV['PLATFORM_ROUTES'])) {
  $routes = json_decode(base64_decode($_ENV['PLATFORM_ROUTES']), TRUE);
  $settings['trusted_host_patterns'] = array();
  foreach ($routes as $url => $route) {
    $host = parse_url($url, PHP_URL_HOST);
    if ($host !== FALSE && $route['type'] == 'upstream' && $route['upstream'] == $_ENV['PLATFORM_APPLICATION_NAME']) {
      $settings['trusted_host_patterns'][] = '^' . preg_quote($host) . '$';
    }
  }
  $settings['trusted_host_patterns'] = array_unique($settings['trusted_host_patterns']);
}


/*********** Our customisations ************/

// Configure relationships. -- Needed it for database configuration 
$relationships = json_decode(base64_decode($_ENV['PLATFORM_RELATIONSHIPS']), TRUE);

if (empty($databases['default']['default'])) {
  foreach ($relationships['database'] as $endpoint) {
    $database = array(
      'driver' => $endpoint['scheme'],
      'database' => $endpoint['path'],
      'username' => $endpoint['username'],
      'password' => $endpoint['password'],
      'host' => $endpoint['host'],
      'port' => $endpoint['port'],
    );

    if (!empty($endpoint['query']['compression'])) {
      $database['pdo'][PDO::MYSQL_ATTR_COMPRESS] = TRUE;
    }

    if (!empty($endpoint['query']['is_master'])) {
      $databases['default']['default'] = $database;
    }
    else {
      $databases['default']['slave'][] = $database;
    }
  }
}

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

// Setting private url
$settings['file_private_path']='sites/default/files/private';

// Local settings. These are required for Platform.sh.
#if (file_exists(__DIR__ . '/settings.local.php')) {
#  include __DIR__ . '/settings.local.php';
#}

if (file_exists(__DIR__ . '/settings.dev.php')) {
  include __DIR__ . '/settings.dev.php';
}
