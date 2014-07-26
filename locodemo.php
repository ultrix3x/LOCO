<?php
include_once('./loco.php');

// A simple class used in these examples
class DoThisLater {
  public $info;
  
  public function CallMe($str) {
    echo "This is called with \$str set to \"$str\"\n";
  }
  
  public function ShowInfo() {
    echo $this->info;
  }

  public function GetInfo() {
    return $this->info;
  }
    
}


/***********************************************************************
 * Simple demo of saving a series of calls to disk to make the actual
 * call later.
 **********************************************************************/
// Create LOCO object with the classname as the first argument
$gl = new GoLOCO('DoThisLater');
// Call whatever functions on the object as you wish to call
$gl->CallMe('This has been stored on disk');
// Save the list of actions to disk
$gl->__locosave('./dothislater.loco');


echo "File has been stored on disk\n";

// Create anothe LOCO object
$glRestore = new GoLOCO();
// Load the list of actions from the file you saved them to
$glRestore->__locoload('./dothislater.loco');
// Run the list of actions
$glRestore->__locogo();


/***********************************************************************
 * Another simple demo of creating an list of actions to make the
 * actual call later.
 **********************************************************************/
$xl = new xLOCO('DoThisLater');
$actions = $xl->__lococall('CallMe', "This is called with xLOCO");
xLOCO::Execute($actions);

/***********************************************************************
 * Another simple demo of creating an list of actions to make the
 * actual call later. The function defined in __lococall returns a
 * value when xLOCO::Execute is called.
 **********************************************************************/
$xl = new xLOCO('DoThisLater');
$xl->info = 'This is added with xLOCO';
$actions = $xl->__lococall('GetInfo');
$info = xLOCO::Execute($actions);
echo "Got \"{$info}\" from xLOCO::Execute\n";



/***********************************************************************
 * This class could be used for sending mails using PHPMailer
 * 
 * In file where you wish to send a mail but doesn't bother if it
 * filed or not. (Note that you does not have to include PHPMailer in
 * this file. This is because it is never actually called.)
 * include_once(INCLUDE_PATH_TO_LOCO.'/loco.php');
 * $mail = new GoLOCO('PHPMailer');
 * $mail->IsHTML();
 * $mail->AddAddress('tb@bytecode.se', 'Thomas');
 * $mail->Subject = 'GoLOCO test';
 * $mail->Body = 'This mail is sent by using <strong>GoLOCO</strong>';
 * $mail->__locosave(SAVEPATH.'/filename.loco');
 * 
 * 
 * Then there might be a cronjob running in the background calling
 * another file containing this code:
 * 
 * include_once(INCLUDE_PATH_TO_PHPMAILER.'/class.phpmailer.php');
 * include_once(INCLUDE_PATH_TO_LOCO.'/loco.php');
 * $send = new GoLOCO();
 * $send->__locoload(SAVEPATH.'/filename.loco');
 * $send->__locogo('Send');
 * 
 * One of the major advantages using this method is that you can create
 * a mail by using information specific to a specific context in
 * another context without having to bother to recreate the original
 * context.
 * 
 * Another example of using context information is when you wish to
 * perform timeconsuming tasks without delaying the current request.
 * Just create a LOCO-object and call the appropriate functions and set
 * the appropriate variables and then save it to disk. Then use a
 * cronjob to perform the heavy work in the background.
 **********************************************************************/

?>