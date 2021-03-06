# LOCO
Base class for LOCO functionality.
The class LOCO has not much separate logic and is just a collector for
all calls and variable set calls.


## __constructor
The first argument in the constructor is the class that should be used
when executing the actions.

## __lococall
The first argument in this call is the function that should return a
value when executing the actions later.
The __lococall returns an array containing all actions stored in LOCO.

## __locoinclude
Tells LOCO to include a specific file at runtime.


# xLOCO
Extends the basic LOCO class by adding two static functions.

## Execute
Executes an array of actions (that you get from __lococall)

## ExecuteFile
Loads the specified file and then calls Execute


# GoLOCO
A more stand-alone extension of the LOCO class adding the special
functions __locogo, __locoobject, __locoget, __locoset, __locosave
and __locoload.

## __locogo
Executes the stored actions. The return value of the last call is used
as a return value for this function.

## __locoobject
Returns the object used by LOCO internally.

## __locoget
Get the list of actions.

## __locoset
Set a new list of actions.

## __locosave
Save the list of actions to a file.

## __locoload
Load a new list of actions from a file.



# When to use LOCO?
LOCO can be used to decrease the execution time for a request by
extracting tasks that are heavy time consumers to an external task,
for instance a cronjob, without the need for the new context to know
any context specific information about the old context.
An example is to send mail from the web server to the webmaster without
putting any delay to the request.

In file where you wish to send a mail but doesn't bother if it
filed or not. (Note that you does not have to include PHPMailer in
this file. This is because it is never actually called.)

```php
include_once(INCLUDE_PATH_TO_LOCO.'/loco.php');
$mail = new GoLOCO('PHPMailer');
$mail->__locoinclude(INCLUDE_PATH_TO_PHPMAILER.'/class.phpmailer.php');
$mail->IsHTML();
$mail->AddAddress('tb@bytecode.se', 'Thomas');
$mail->Subject = 'GoLOCO test';
$mail->Body = 'This mail is sent by using <strong>GoLOCO</strong>';
$mail->__locosave(SAVEPATH.'/filename.loco');
```

Then there might be a cronjob running in the background calling
another file containing this code:

```php
include_once(INCLUDE_PATH_TO_LOCO.'/loco.php');
$send = new GoLOCO();
$send->__locoload(SAVEPATH.'/filename.loco');
$send->__locogo('Send');
```


# Examples

## Demo 1
A rather complicated way to create an object from the class Test,
setting the $value to a value and then print the $value.
```php
<?php
// demo1.php
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
```
Should print out 42


## Demo 2
Simple way to include at time of execution.
The basic function is Demo 1 but split into three files
```php
<?php
// demo2a.php
class Test {
  var $value;
}
?>
```

```php
<?php
// demo2b.php
include_once('../loco.php');
$gl = new GoLOCO('Test');
$gl->__locoinclude('./demo2a.php');
$gl->value = 42;
$gl->__locosave('./demo2.loco');
?>
```

```php
<?php
// demo2c.php
include_once('../loco.php');
$gl = new GoLOCO();
$gl->__locoload('./demo2.loco');
$gl->__locogo();
$test = $gl->__locoobject();
echo $test->value;
?>
```
Running demo2b.php followed by demo2c.php should print out 42

The script demo2b.php generates a file called demo2.loco that contains
the actions performed in demo2c.php.


## Demo 3
```php
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
?>
```