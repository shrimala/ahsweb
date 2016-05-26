<?php
echo "Export all the configuration file to GitHub";
define('PRIVATE_KEY', '6246935ccabd572af450595011a42ef1761e2bf4');
echo shell_exec("drush -y config-export");
echo shell_exec("git clone -b ConfigExport https://github.com/shrimala/ahsweb.git");
echo shell_exec("chmod -R 777 ahsweb");
echo shell_exec("cd /app/public/sites/default/files");
echo shell_exec("config/sync/. ahsweb/config/sync/");
echo shell_exec("git add --all");
echo shell_exec("git commit -am 'update message'");
echo shell_exec("git push origin ConfigExport");
/**echo shell_exec("cd ahsweb");
echo shell_exec("git config user.email 'arith.nath@dcplkolkata.com'");
echo shell_exec("git config user.name 'aritnath1990'");
echo shell_exec("git commit -am 'update message'");
echo shell_exec("git push origin ConfigExport");*/
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
