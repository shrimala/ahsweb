<?php

function runcmd ($cmd){
  echo "<pre><strong>";
  echo ">  ". $cmd;
  echo "</strong><br>";
  echo shell_exec($cmd . " 2>&1");
  echo "</pre>";
}

echo "Export all the configuration file to GitHub<br>";
$platform_variables = json_decode(base64_decode($_ENV['PLATFORM_VARIABLES']), TRUE);
$GITHUB_TOKEN = $platform_variables["GITHUB_TOKEN"];
echo "Github token is " . $GITHUB_TOKEN;
runcmd("cd /app/public/sites/default/files");
//runcmd("git clone -b ConfigExport2  https://{$GITHUB_TOKEN}@github.com/shrimala/ahsweb.git");
runcmd("git init");
runcmd("git pull -b ConfigExport2 https://{$GITHUB_TOKEN}@github.com/shrimala/ahsweb.git");
runcmd("chmod -R 777 ahsweb");
runcmd("cd /app/public/sites/default/files/ahsweb");
runcmd("drush -y config-export");
runcmd("chmod -R 777 /app/public/sites/default/files/ahsweb/config/sync");
runcmd("cd /app/public/sites/default/files/ahsweb");
runcmd("git add --all");
runcmd("git config  user.email 'arith.nath@dcplkolkata.com'");
runcmd("git config  user.name 'aritnath1990'");
runcmd("git commit -am 'update message'");
runcmd("git push origin ConfigExport2 https://{$GITHUB_TOKEN}@github.com/shrimala/ahsweb.git");
?>
<html>
  <head>
    <title>
      Configuration Export
    </title>
  </head>
  <body style="background-color: #000000; color: #FFFFFF; font-weight: bold; padding: 0 10px;">
    <div style="width:700px">
      <div style="float:left;width:350px;">
        <p style="color:white;">Git Configuration Export Script</p>
      </div>
    </div>
  </body>
</html>
