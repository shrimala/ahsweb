<?php

$variables = json_decode(base64_decode($_ENV['PLATFORM_VARIABLES']), TRUE);



$dropbox_token = $variables['dropbox_token'];
$dropbox_client_id = $variables['dropbox_client_id'];

$s3_key = $variables['s3_key'];
$s3_secret = $variables['s3_secret'];
$s3_region = $variables['s3_region'];
$s3_bucket = $variables['s3_bucket'];
$s3_prefix = $variables['s3_prefix'];
$s3_cname = $variables['s3_cname'];

var_dump($drop_token,$dropbox_client_id,$s3_key,$s3_secret);

$schemes = [
  'dropboxexample' => [
    'driver' => 'dropbox',
    'config' => [
      'token' => 'fQJL4O-6G8AAAAAAAAAADlao81xX6fgVF7jqKIyxcH2ecliBeP6MLRScDlobQTgx',
      'client_id' => 'arit.nath@dcplkolkata.com',
    ],
  ],
  's3example' => [
    'type' => 's3',
    'driver' => 's3',
    'config' => [
      'key'    => 'AKIAJ6TQOIFM4NO7J2WQ',
      'secret' => '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',
      'region' => 'eu-west-1',
      'bucket' => 'drupal8test',
      'prefix'=> 'drupal8',
      'cname' => 'drupal8test.s3-website-eu-west-1.amazonaws.com',
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

$settings['flysystem'] = $schemes;
