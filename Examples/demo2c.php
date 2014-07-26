<?php
// demo2c.php
include_once('../loco.php');
$gl = new GoLOCO();
$gl->__locoload('./demo2.loco');
$gl->__locogo();
$test = $gl->__locoobject();
echo $test->value;
echo "\n";
?>