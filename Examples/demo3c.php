<?php
// demo3.php
include_once('../loco.php');
class Test {
  var $value;
  
  public function ShowValue() {
    echo $this->value;
  }
  
}

$gl = new xLOCO('Test');
$gl->value = 42;
$actions = $gl->__lococall('ShowValue');
xLOCO::Execute($actions);
echo "\n";
?>