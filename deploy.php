<html>
  <head>
    <title>
      Configuration Export
    </title>
  </head>
  <body style="background-color: #000000; color: #FFFFFF; font-weight: bold; padding: 0 10px;">
    <div style="width:100%">
      <div style="float:left;width:100%;">
        <p style="color:white;">Git Configuration Export Script</p>
        <form action="" method="post">
          Enter Commit: <input type="text" name="t1" placeholder="Please enter commit">
		      <input type="submit" value="Submit" value="Commit">
		    </form>
      </div>
    </div>
<?php
if (isset($_POST["t1"]) && !empty($_POST["t1"])) {
function runcmd ($cmd){
  echo "<pre><strong>";
  echo ">  ". $cmd;
  echo "</strong><br>";
  echo shell_exec($cmd . " 2>&1");
  echo "</pre>";
}
echo "<br><br><br><br><br><br>";
echo "Export all the configuration file to GitHub<br>";
$platform_variables = json_decode(base64_decode($_ENV['PLATFORM_VARIABLES']), TRUE);
$GITHUB_TOKEN = $platform_variables["GITHUB_TOKEN"];
$environment = $_ENV["PLATFORM_ENVIRONMENT"];
echo "Environment name: " . $environment . "<br>";
$pr = substr($environment, 3);
echo "PR number: ". $pr . "<br>";
$ip=shell_exec("curl -s https://api.github.com/repos/shrimala/ahsweb/pulls/" . $pr);
$json = json_decode($ip, true); 
echo $json['head']['ref'];
$branch = $json['head']['ref'];
echo "<br>Branch name: " . $branch;
$t=date("d-m-Y");
$t=$t."_".date("H:i:s");
runcmd("cd /app/public/sites/default/files/Arit;
cd vendor/behat/behat;
bin/behat features/installaion.feature;
cd /app/public/sites/default/files;
rm -rf ahsweb;
mkdir ahsweb;
chmod -R 777 ahsweb;
cd ahsweb;
git init;
git pull https://{$GITHUB_TOKEN}@github.com/shrimala/ahsweb.git {$branch};
git name-rev --name-only HEAD;
git checkout -b {$branch};
git name-rev --name-only HEAD;
chmod -R 777 config/sync;
ls -l config/;
drush -y config-export;
cd ahsweb;
git config core.filemode false;
git add config/sync/;
git add BehatReport/;
git config  user.email 'owner@ahs.org.uk';
git config  user.name 'AHSowner';
git commit -m '{$_POST['t1']}';
git push https://{$GITHUB_TOKEN}@github.com/shrimala/ahsweb.git {$branch}");
}
?>
</body>
</html>
