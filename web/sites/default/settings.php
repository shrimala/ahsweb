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

// The hash_salt should be a unique random value for each application.
// If left unset, the settings.platformsh.php file will attempt to provide one.
// You can also provide a specific value here if you prefer and it will be used
// instead. In most cases it's best to leave this blank on Platform.sh. You
// can configure a separate hash_salt in your settings.local.php file for
// local development.
// $settings['hash_salt'] = 'change_me';

// Set up a config sync directory.
//
// This is defined inside the read-only "config" directory. This works well,
// however it requires a patch from issue https://www.drupal.org/node/2607352
// to fix the requirements check and the installer.
$config_directories[CONFIG_SYNC_DIRECTORY] = '../config/sync';

// Automatic Platform.sh settings.
if (file_exists(__DIR__ . '/settings.platformsh.php')) {
  include __DIR__ . '/settings.platformsh.php';
}

if (isset($_ENV["PLATFORM_ROUTES"])) {
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
}

// Settings for different Platform.sh environments
if (isset($_ENV["PLATFORM_ENVIRONMENT"])) {
  // We're on platform.sh
  // Fetch the Platform.sh environment variables.
  $platformVariables = json_decode(base64_decode($_ENV['PLATFORM_VARIABLES']), TRUE);
  if ($_ENV['PLATFORM_ENVIRONMENT']==='master') {
    //We're on platform.sh master
    //$settings['config_readonly'] = TRUE;
    $dropboxPath = "Live";
  } else {
    // We're on a platform.sh dev environment
    $dropboxPath = "Dev";
    if (file_exists(__DIR__ . '/settings.platformdev.php')) {
      include __DIR__ . '/settings.platformdev.php';
    }
  }
  // Build the Flysystem scheme using credentials configure on Platform.sh.
  $schemes = [
    'dropboxwebarchive' => [
      'driver' => 'dropbox',
      'config' => [
        'token' => $platformVariables['DROPBOX_TOKEN'],
        'client_id' => $platformVariables['DROPBOX_CLIENT'],
        'prefix' => $dropboxPath,
      ],
    ]
  ];
  
} else {
	
  // We're not on Platform.sh; maybe Travis, maybe local.
  if (isset($_ENV['TRAVIS'])) {
    //We're on Travis
    $webRoot = $_ENV['TRAVIS_BUILD_DIR'];
  } else {
    //We're on local
    $webRoot= $_SERVER['DOCUMENT_ROOT'];
  }
  
  // Build the Flysystem scheme, using local storage instead of dropbox.
  $schemes = [
    'dropboxwebarchive' => [
      'driver' => 'local',
      'config' => [
        'root' => $webRoot . '/web/sites/default/files/testfiles',
      ],
	]
  ];

}
// Passing the Flysystem schemes to Drupal
$settings['flysystem'] = $schemes;

// Local settings. These come last so that they can override anything.
if (file_exists(__DIR__ . '/settings.local.php')) {
  include __DIR__ . '/settings.local.php';
}
