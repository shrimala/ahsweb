<?php


$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
	'League\\Flysystem\\Replicate\\' => array($vendorDir . '/league/flysystem-replicate-adapter/src'),
    'League\\Flysystem\\Memory\\' => array($vendorDir . '/league/flysystem-memory/src'),
    'League\\Flysystem\\Dropbox\\' => array($vendorDir . '/league/flysystem-dropbox/src'),
    'League\\Flysystem\\Cached\\' => array($vendorDir . '/league/flysystem-cached-adapter/src'),
    'League\\Flysystem\\AwsS3v3\\' => array($vendorDir . '/league/flysystem-aws-s3-v3/src'),
    'League\\Flysystem\\' => array($vendorDir . '/league/flysystem/src'),
);


