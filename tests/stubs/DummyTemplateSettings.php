<?php

if( !class_exists('\core\services\TemplateSettings') ){
  require(NIV.'include/services/TemplateSettings.inc.php');
}

class DummyTemplateSettings extends \core\services\TemplateSettings {
  public function __construct(){
    $this->s_templateDir = 'default';
  }
}
?>
