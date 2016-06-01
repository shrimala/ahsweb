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
$branch = $json['head']['ref'] . "<br>";
echo "Branch name: " . $branch;
runcmd("
cd /app/public/sites/default/files;
chmod -R 777 ahsweb/config/sync;
rm -rf ahsweb;
mkdir ahsweb
cd ahsweb;
echo "GIT INIT:";
git init;
echo "GIT PULL:";
git pull https://{$GITHUB_TOKEN}@github.com/shrimala/ahsweb.git {$branch}:local;
echo "GIT CHECKOUT:";
git checkout local;
echo "DRUSH CONFIG-EXPORT:";
drush -y config-export;
echo "GIT ADD:";
git add config/sync/;
git config  user.email 'owner@ahs.org.uk';
git config  user.name 'AHSowner';
echo "GIT COMMIT:";
git commit -m '{$_POST['t1']}';
echo "GIT PUSH:";
git push https://{$GITHUB_TOKEN}@github.com/shrimala/ahsweb.git local:{$branch}");
}
?>
</body>
</html>
