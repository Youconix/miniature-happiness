<?php
/**
 * Error 500 class
 *
 * @name error.php
 * @package error_docs
 *          @lisence:		http://scripthulp.com/licence.php
 * @author :		REJ Scheijen Scripthulp
 * @version 1.0
 * @since 1.0
 *        @date made:		01-05-2010
 *        @date last changed:	02-05-2010
 */
if( !class_exists('\includes\BaseLogicClass') ){
 require (NIV . 'includes/BaseLogicClass.php');
}
class Error500 extends \includes\BaseLogicClass {
 /**
  * Starts the class Error504
  */
 public function __construct(){
  $this->init();
  
  $this->displayError();
  
  $this->showLayout();
 }
 private function displayError(){
  header("HTTP/1.1 500 Internal Server Error");
  
  $service_Language = \core\Memory::services('Language');
  
  $this->service_Template->set('title', $service_Language->get('language/errors/error500/serverError'));
  
  $this->service_Template->set('notice', $service_Language->get('language/errors/error500/systemError'));
  
  if( isset($_SESSION['error']) ){
   if( isset($_SESSION['errorObject']) ){
    Memory::services('ErrorHandler')->error($_SESSION['errorObject']);
   }
   else{
    Memory::services('ErrorHandler')->errorAsString($_SESSION['error']);
   }
   
   if( defined('DEBUG') ){
    $this->service_Template->set('debug_notice', $_SESSION['error']);
   }
  }
 }
}

$obj_Error = new Error500();
unset($obj_Error);
