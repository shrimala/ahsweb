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

// Override paths for config files in Platform.sh.
if (isset($_ENV['PLATFORM_APP_DIR'])) {
  $config_directories = array(
    CONFIG_SYNC_DIRECTORY => $_ENV['PLATFORM_APP_DIR'] . '/config/sync',
  );
}

// Configure relationships.
if (isset($_ENV['PLATFORM_RELATIONSHIPS'])) {
  $relationships = json_decode(base64_decode($_ENV['PLATFORM_RELATIONSHIPS']), TRUE);

  if (empty($databases['default']['default']) && !empty($relationships['database'])) {
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
}

// Import variables prefixed with 'drupal:' into $settings.
if (isset($_ENV['PLATFORM_VARIABLES'])) {
  $variables = json_decode(base64_decode($_ENV['PLATFORM_VARIABLES']), TRUE);

  $prefix_len = strlen('drupal:');
  foreach ($variables as $name => $value) {
    if (substr($name, 0, $prefix_len) == 'drupal:') {
      $settings[substr($name, $prefix_len)] = $value;
    }
  }	
}

// Default PHP settings.
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);
ini_set('session.gc_maxlifetime', 200000);
ini_set('session.cookie_lifetime', 2000000);
ini_set('pcre.backtrack_limit', 200000);
ini_set('pcre.recursion_limit', 200000);

// Local settings.
if (file_exists(__DIR__ . '/settings.local.php')) {
  include __DIR__ . '/settings.local.php';
}
