<?php
$s = file_get_contents('adminregister.php');
echo "{=".substr_count($s, '{')." }=".substr_count($s, '}')."\n";
?>