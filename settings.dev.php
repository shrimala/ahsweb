<?php

$variables = json_decode(base64_decode($_ENV['PLATFORM_VARIABLES']), TRUE);



$dropbox_token = $variables['dropbox_token'];
$dropbox_client_id = $variables['dropbox_client_id'];
/*
$s3_key = $variables['s3_key'];
$s3_secret = $variables['s3_secret'];
$s3_region = $variables['s3_region'];
$s3_bucket = $variables['s3_bucket'];
$s3_prefix = $variables['s3_prefix'];
$s3_cname = $variables['s3_cname'];
*/

$schemes = [
  'dropboxexample' => [
    'driver' => 'dropbox',
    'config' => [
      'token' => $dropbox_token,
      'client_id' => $dropbox_client_id,
    ],
  ],
 /* 's3example' => [
    'type' => 's3',
    'driver' => 's3',
    'config' => [
      'key'    => $s3_key,
      'secret' => $s3_secret,
      'region' => $s3_region,
      'bucket' => $s3_bucket,
      'prefix'=> $s3_prefix,
      'cname' => $s3_cname,
    ],
  ],*/
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

$settings['flysystem'] = $schemes;


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
