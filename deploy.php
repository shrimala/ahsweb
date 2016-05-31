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
runcmd("rm -rf /app/public/sites/default/files/ahsweb;
cd /app/public/sites/default/files;
git clone -b ConfigExport2 https://{$GITHUB_TOKEN}@github.com/shrimala/ahsweb.git;
chmod -R 777 ahsweb;
cd /app/public/sites/default/files/ahsweb;
ls config/sync;
drush -y config-export;
ls /app/public/sites/default/files/ahsweb/config/sync;
chmod -R 777 /app/public/sites/default/files/ahsweb/config/sync;
git add --all;
git config  user.email 'arith.nath@dcplkolkata.com';
git config  user.name 'aritnath1990';
git commit -m 'update message';
git push origin ConfigExport2");

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
