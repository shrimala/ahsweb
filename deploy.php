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
	
function runcmd ($cmd, $path){
  echo "<pre><strong>";
  echo $path . "<br> >  ". $cmd;
  echo "</strong><br>";
  echo shell_exec("cd $path; $cmd 2>&1");
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
$base_path = "~/web/sites/default/files";
$repo_path = $base_path . "/ahsweb";

runcmd("rm -rf ahsweb", $base_path);
runcmd("mkdir ahsweb", $base_path);
runcmd("chmod -R 777 ahsweb", $base_path);
runcmd("chmod -R 777 ahsweb", $base_path);
runcmd("git init", $repo_path);
runcmd("git pull https://{$GITHUB_TOKEN}@github.com/shrimala/ahsweb.git {$branch}", $repo_path);
runcmd("git name-rev --name-only HEAD", $repo_path);
runcmd("git checkout -b {$branch}", $repo_path);
runcmd("git name-rev --name-only HEAD", $repo_path);
runcmd("chmod -R 777 config/sync", $repo_path);
runcmd("rm config/sync/*", $repo_path);
runcmd("ls -l config", $repo_path);
runcmd("cat config/sync/system.site.yml", $repo_path);
runcmd("cat config/sync/", $repo_path);
runcmd("drush -y config-export;", $repo_path);
runcmd("ls -l config", $repo_path);
runcmd("cat config/sync/system.site.yml", $repo_path);
runcmd("git config core.filemode false", $repo_path);
runcmd("git add config/sync/", $repo_path);
runcmd("git status;", $repo_path);
runcmd("git config  user.email 'owner@ahs.org.uk'", $repo_path);
runcmd("git config  user.name 'AHSowner'", $repo_path);
runcmd("git commit -m '{$_POST['t1']}'", $repo_path);
runcmd("git push https://{$GITHUB_TOKEN}@github.com/shrimala/ahsweb.git {$branch}", $repo_path);

}
?>
</body>
</html>
