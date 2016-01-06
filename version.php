<?php

$version_id = "1.9.2";

echo("<hr/><span style=\"text-align:left;\"><a style=\"font-style: italic;\" href='http://freshmeat.net/projects/phplicensewatcher/'>PhpLicensewatcher</a> v. $version_id</span>\n");
echo("<p><span style=\"text-align:right;\">Page last refreshed at: " . DATE("Y-m-d H:i:s") . "</span>\n");

$End = getTime();
echo "<br>Execution time: ".number_format(($End - $Start),2)."s";

?>
