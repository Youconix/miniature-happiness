<?php
namespace admin;

define('NIV','../');
require(NIV.'vendor/youconix/core/bootstrap.php');

class ModulesJS extends \admin\MenuModules {
  protected function parse(){
    $this->getFiles('js');
    
    $this->headers->contentType('text/javascript');
    $this->display();
  }
}

try {
  \Loader::Inject('\admin\ModulesJS');
}
catch(\Exception $e){
}

