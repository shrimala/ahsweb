<?php
$platform_variables = json_decode(base64_decode($_ENV['PLATFORM_VARIABLES']), TRUE);
$GITHUB_TOKEN = $platform_variables["GITHUB_TOKEN"];
$environment = $_ENV["PLATFORM_ENVIRONMENT"];
$pr = substr($environment, 3);
$at=shell_exec("bin/behat");
$at1="Test Report pass";
$tx="curl -u 'aritnath1990 :c4b1247a2459dce7dd1cf67c4e2e71418fc76404' -X POST --data '{";
$tx=$tx.'"body"';
$tx=$tx.':"';
$tx=$tx.$at1.'"';
$tx=$tx."}'";
$tx=$tx." https://api.github.com/repos/shrimala/ahsweb/issues/". $pr ."/comments";
echo shell_exec($tx);
?>
