<?php
echo "partha";
define('PRIVATE_KEY', '6246935ccabd572af450595011a42ef1761e2bf4');
echo shell_exec("git clone -b ConfigExport https://github.com/shrimala/ahsweb.git");
echo shell_exec("chmod -R 777 ahsweb");
echo shell_exec("cp drupal_config/dev/. ahsweb/config/sync/");
echo shell_exec("git add --all");
echo shell_exec("git commit -am 'update message'");
echo shell_exec("git push origin ConfigExport");
echo "done";
/**echo shell_exec("cd ahsweb");
echo shell_exec("git config user.email 'arith.nath@dcplkolkata.com'");
echo shell_exec("git config user.name 'aritnath1990'");
echo shell_exec("git commit -am 'update message'");
echo shell_exec("git push origin ConfigExport");*/
