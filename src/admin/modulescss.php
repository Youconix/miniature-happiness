<?php
namespace admin;

define('NIV','../');
require(NIV.'vendor/youconix/core/bootstrap.php');

class ModulesCSS extends \admin\MenuModules {
  protected function parse(){
    $this->getFiles('css');
    
    $this->headers->contentType('text/css');
    $this->display();
  }
}

try {
  \Loader::Inject('\admin\ModulesCSS');
}
catch(\Exception $e){
}