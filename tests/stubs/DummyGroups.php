<?php

if( !class_exists('\core\models\Model') ){
  require(NIV.'include/models/Model.inc.php');
}
if( !class_exists('\core\models\Groups') ){
  require(NIV.'include/models/Groups.inc.php');
}

class DummyGroups extends \core\models\Groups {
  public function __construct(){}
}
?>