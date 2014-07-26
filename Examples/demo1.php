<?php
include_once('../loco.php');
class Test {
  var $value;
}

$gl = new GoLOCO('Test');
$gl->value = 42;
$actions = $gl->__locoget();

$gl2 = new GoLOCO();
$gl2->__locoset($actions);
$gl2->__locogo();
$test = $gl2->__locoobject();

echo $test->value;
echo "\n";
?>