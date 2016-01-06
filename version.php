<?php

$version_id = "1.9.2";

echo("<hr/><a href='http://freshmeat.net/projects/phplicensewatcher/'>PhpLicensewatcher</a> $version_id\n");
echo("<p>Page last refreshed at: " . DATE("Y-m-d H:i:s") . "\n");

$End = getTime();
echo "<br>Execution time: ".number_format(($End - $Start),2)."s";

?>
