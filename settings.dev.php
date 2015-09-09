<?php

var_dump(json_decode(base64_decode($_ENV['PLATFORM_RELATIONSHIPS']), TRUE));


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
