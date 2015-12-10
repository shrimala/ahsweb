<?php

// Fetching the Platform Variables's data
$variables = json_decode(base64_decode($_ENV['PLATFORM_VARIABLES']), TRUE);

// Fetching the dropbox details
$dropbox_token = $variables['dropbox_token'];
$dropbox_client_id = $variables['dropbox_client_id'];

// Structuring the schemes
$schemes = [
  'dropboxexample' => [
    'driver' => 'dropbox',
    'config' => [
      'token' => $dropbox_token,
      'client_id' => $dropbox_client_id,
    ],
  ],
  'localexample' => [
    'driver' => 'local',
    'config' => [
      'root' => '/app/public/sites/default/files',
    ],
    'cache' => FALSE, 
    'replicate' => 'dropboxexample',

    'serve_js' => TRUE,
    'serve_css' => TRUE,
  ]
];

// Assigning the Schemes to Flysystem
$settings['flysystem'] = $schemes;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
