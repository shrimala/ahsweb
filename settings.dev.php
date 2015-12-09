<?php

$schemes = [
  'dropboxexample' => [
    'driver' => 'dropbox',
    'config' => [
      'token' => 'fQJL4O-6G8AAAAAAAAAADlao81xX6fgVF7jqKIyxcH2ecliBeP6MLRScDlobQTgx',
      'client_id' => 'arit.nath@dcplkolkata.com',
    ],
  ],
 /* 's3example' => [
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
  ], */
  'localexample' => [
    'driver' => 'local',
    'config' => [
      'root' => '/var/www/html/drupal8arit/sites/default/files',
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
