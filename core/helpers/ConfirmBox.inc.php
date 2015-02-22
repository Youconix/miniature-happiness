<?php
namespace core\helpers;

class ConfirmBox extends \core\helpers\Helper {
 public function __construct(\core\services\Template $service_Template){
  $service_Template->setCssLink('<link rel="stylesheet" href="{NIV}{style_dir}css/widgets/confirmbox.css" media="screen">');
  $service_Template->setJavascriptLink('<script src="{NIV}js/widgets/confirmbox.js"></script>');
 }
}