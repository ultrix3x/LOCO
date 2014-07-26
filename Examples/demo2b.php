<?php
// demo2b.php
include_once('../loco.php');
$gl = new GoLOCO('Test');
$gl->__locoinclude('./demo2a.php');
$gl->value = 42;
$gl->__locosave('./demo2.loco');
?>