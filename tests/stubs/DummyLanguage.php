<?php
if( !class_exists('\core\services\Language') ){
  if( !class_exists('\core\services\Service') ){
    require(NIV.'include/services/Service.inc.php');
  }
  
  require(NIV.'include/services/Language.inc.php');
}

class DummyLanguage extends \core\services\Language {
  public function __construct(){
    
  }
}