<?php
$platform_variables = json_decode(base64_decode($_ENV['PLATFORM_VARIABLES']), TRUE);
$GITHUB_TOKEN = $platform_variables["GITHUB_TOKEN"];
$environment = $_ENV["PLATFORM_ENVIRONMENT"];
$pr = substr($environment, 3);
shell_exec("bin/behat features/installaion.feature --format progress --out test_report.txt;");
$myfile = fopen("test_report.txt", "r") or die("Unable to open file!");
$at1= fread($myfile,filesize("test_report.txt"));
//$at1=str_replace(".","",$at1);
$at1=preg_replace( "/\r|\n/", " ", $at1);
fclose($myfile);
$at=shell_exec("bin/behat");
$tx="curl -u 'aritnath1990:{$GITHUB_TOKEN}' -X POST --data '{";
$tx=$tx.'"body"';
$tx=$tx.':"';
$tx=$tx.$at1.'"';
$tx=$tx."}'";
$tx=$tx." https://api.github.com/repos/shrimala/ahsweb/issues/". $pr ."/comments";
echo shell_exec($tx);
?>
