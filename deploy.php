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
$ip=shell_exec("curl -s https://api.github.com/repos/shrimala/ahsweb/pulls/37");
$json = json_decode($ip, true); 
echo $json['title'];
$dynBatch = $json['title'];
runcmd("cd /app/public/sites/default/files;
mkdir ahsweb;
cd ahsweb;
git init;
git pull https://{$GITHUB_TOKEN}@github.com/shrimala/ahsweb.git DynamicPR;
git checkout -b DynamicPR;
drush -y config-export;
git add config/sync/;
git config  user.email 'owner@ahs.org.uk';
git config  user.name 'AHSowner';
git commit -m '{$_POST['t1']}';
git push https://{$GITHUB_TOKEN}@github.com/shrimala/ahsweb.git DynamicPR");
}
?>
</body>
</html>
