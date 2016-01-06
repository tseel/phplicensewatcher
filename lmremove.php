<?php

require_once("common.php");
require_once("tools.php");
include_once('auth.php');

print_header("Remove user license");

# grab server info
if (isset($_GET['server'])) {
  $host = $server[$_GET['server']];
  $hostno = $_GET['server'];
} else {
  die('no server defined');
}	

?>
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body role="document">
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">phpLicenseWatcher</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li><a href="index.php">Home</a></li>
            <li><a href="details.php?listing=0&server=<?php echo($hostno);?>">Server Details</a></li>
            <li class="active"><a href="#">Remove license</a></li>
            <!--li><a href="#about">About</a></li-->
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>
    <div class="container" role="main">
      <h1>Remove user license</h1>
      <div id="msg" style="visibility:hidden;"></div>

<?php

if ( isset($disable_license_removal) && $disable_license_removal == 1 ) {
    die("Sorry this feature is not enabled. If you need it talk to the maintainer of this
        page on how to enable it.");
}

$args=preg_split("/ /i", $_GET['arg']);

#echo("<p>Host: " . $host['hostname'] . "</p>");
#echo("<p>Args: " . $args[0] . " " . $args[1] . " " . $args[2] . "</p>");

######################################################################
# Due to laziness feature contains colon (:) at the end. We need
# to strip it 
######################################################################
$featurename = $_GET['feature'];

if ( preg_match('/^[a-zA-Z0-9\-_]+$/i', $featurename) ) { 
	echo("<p>Looking up feature " . $featurename . " on " . $host['hostname'] . "</p>");
} else {
	die("Security Alert: Feature name you supplied has illegal characters that could possibly be used for security compromise.");
}

######################################################################
# For security purposes and other reasons before we remove a license
# we'll try to make sure whether it is actually used
######################################################################
$fp = popen($lmutil_loc . " lmstat -f " . $featurename . " -c " . $host['hostname'] , "r");

$featureFound = false;
while ( !feof ($fp) ) {
    
    $line = fgets ($fp, 1024);
    
    if ( preg_match ("/$args[0] $args[1] $args[2] /i", $line, $matchedline ) ) {
      $featureFound = true;
      break;
    }
}

if ( $featureFound ) {
  echo("<p>Feature " . $featurename . " found, trying to remove license</p>");
  # checked out feature found
  $commandline = ($lmutil_loc . " lmremove -c " . $host['hostname'] . " " . $featurename . " " . $matchedline[0] );
 
  echo("<p>Output of your removal command<p><PRE>");

  $fp2 = popen($commandline , "r");

  while ( !feof ($fp2) ) {
    $line = fgets ($fp2, 1024);
    echo($line);
  } 
  echo("</p>");
  break;
} else {
  echo("<p>No checkouts for feature " . $featurename . " found  " . $host['hostname'] . " for specified user</p>");
}

# Close pipes
fclose($fp);
fclose($fp2);

?>
