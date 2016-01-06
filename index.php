<?php

##
##      $Id: index.php 61155 2013-03-19 22:36:07Z proche $
##


require_once("common.php");
require_once("tools.php");
print_header("License Server Status");

?>

    <link rel="top" href="index.php"/>
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
            <li class="active"><a href="#">Home</a></li>
            <!--li><a href="#about">About</a></li-->
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Admin <span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="license_alert.php?nomail=1">License Alert</a></li>
                <li class="disabled"><a href="config/index.php">Configuration</a></li>
                <li <?php if (!is_array($monitor_license)){ echo 'class="disabled"'; } ?>><a href="utilization.php">License utilization</a></li>
                <li <?php if (!isset($monitor_license) || !(sizeof($monitor_license) > 0)){ echo 'class="disabled"'; } ?>><a href="monitor.php">License utilization trends</a></li>
                <li role="separator" class="divider"></li>
                <li <?php if (!isset($log_file) || !(sizeof($log_file) > 0)){ echo 'class="disabled"'; } ?>><a href="denials.php">FlexLM denials</a></li>
                <li <?php if (!isset($log_file) || !(sizeof($log_file) > 0)){ echo 'class="disabled"'; } ?>><a href="checkouts.php">FlexLM checkouts</a></li>
              </ul>
            </li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>
    <div class="container" role="main">
      <h1>License server status overview</h1>
      To get current usage for an individual server please click on the "Details" next to the server.
      <div id="msg" style="visibility:hidden;"></div>
<?php

##########################################################################
# We are using PHP Pear library to create tables :-)
##########################################################################
require_once ("HTML/Table.php");

# empty statusmsg
$statusMsg="";

$tableStyle = array('cellpadding' => '1',
                    'cellspacing' => '2',
                    'class' => 'table table-striped');

# Create a new table object
$table = new HTML_Table();
$table->setAttributes($tableStyle);
$table->setColAttributes(1,"align=\"center\"");

# Define a table header
$headerStyle = "";
$colHeaders = array("License Server", "Description", "Status", "Current Usage", "Available features/license", "Master", "Version");
$table->addRow($colHeaders, $headerStyle, "TH");
# set width on description col
$table->setColAttributes(1,"width=\"180\"");

# grab all the different server types
foreach ($server as $host) {
    $type[]=$host['type'];
}
# return only unique types
$types = array_unique($type);

# loop thru each unique type and make up status table
foreach ($types as $type) {
    $servers = findServers($type,"type");

    if (sizeof($servers)>0) {
        $table->addRow(array(strtoupper($type) . " Servers"),$headerStyle,"TH");
        $table->setCellAttributes(($table->getRowCount() -1),0,"colspan='" .$table->getColCount() ."'");

        for ( $i = 0 ; $i < sizeof($servers) ; $i++ ) {
            $cur = current($servers);
            $status_array = getDetails($cur);
            # does this host contain a webui?
            # currently only RLM offers webui
            if (isset($cur["webui"])) {
                $host = "<a href=\"".$cur["webui"]."\">".$cur["hostname"]."</a>";
            } else {
                $host = $cur["hostname"];
            }
            $table->AddRow(array($host,
                                $cur["desc"],
                                strtoupper($status_array["status"]["service"]),
                                $status_array["status"]["clients"],
                                $status_array["status"]["listing"],
                                $status_array["status"]["master"],
                                $status_array["status"]["version"]));

            # Set the background color of status cell
            $table->updateCellAttributes( ($table->getRowCount() - 1) , 2, "class='" . $status_array["status"]["service"] . "'");
            $table->updateCellAttributes( 1 , 0, "");
            # fetch status
            $statusMsg=AppendStatusMsg($statusMsg,$status_array["status"]["msg"]);
            # next!
            next($servers);
        }
    }
}

# Display the table
$table->display();

#footer
include_once('version.php');

print ("\n");
print ('<script language="javascript" type="text/javascript">');
print ("document.getElementById('msg').innerHTML = \"".$statusMsg."\";");
# if we have a msg, make the box visiable
if (strlen($statusMsg)>1) {
    print ("document.getElementById('msg').style.visibility = 'visible';");
}
print ('</script>');
?>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    </div>
  </body>
</html>