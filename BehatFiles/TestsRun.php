<?php
function runcmd ($cmd){
  echo "<pre><strong>";
  echo ">  ". $cmd;
  echo "</strong><br>";
  echo shell_exec($cmd . " 2>&1");
  echo "</pre>";
}
$platform_variables = json_decode(base64_decode($_ENV['PLATFORM_VARIABLES']), TRUE);
$GITHUB_TOKEN = $platform_variables["GITHUB_TOKEN"];
$environment = $_ENV["PLATFORM_ENVIRONMENT"];
$pr = substr($environment, 3);
$ip=shell_exec("curl -s https://api.github.com/repos/shrimala/ahsweb/pulls/" . $pr);
$json = json_decode($ip, true); 
$branch = $json['head']['ref'];
$t=date("d-m-Y");
$t=$t."_".date("H:i:s");
shell_exec("mkdir ahsweb; chmod -R 777 ahsweb;");
shell_exec("bin/behat features/installaion.feature --format progress --out ahsweb/test_report". $t .".txt;");
$at=shell_exec("bin/behat");
runcmd("cd ahsweb;
git init;
git pull https://{$GITHUB_TOKEN}@github.com/shrimala/ahsweb.git {$branch};
git name-rev --name-only HEAD;
git checkout -b {$branch};
git name-rev --name-only HEAD;
git config core.filemode false;
git add test_report". $t .".txt;
git config  user.email 'owner@ahs.org.uk';
git config  user.name 'AHSowner';
git commit -m '".$at."';
git push https://{$GITHUB_TOKEN}@github.com/shrimala/ahsweb.git {$branch}");
?>
