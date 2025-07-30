<?php
$output = array();
exec('ping -c 1 1.1.1.1', $output);
$output = implode("\n", $output);
preg_match('/time=([\d]+)[.\s\d]+ms/', $output, $matches);
echo $matches[1];
?>