<?php

namespace admin;

abstract class MenuModules
{
  /**
   * 
   * @var \youconix\core\models\ControlPanelModules
   */
  protected $controlPanelModules;

  /**
   *
   * @var \youconix\core\services\Xml
   */
  protected $xml;

  /**
   *
   * @var \youconix\core\services\FileHandler
   */
  protected $fileHandler;

  /**
   *
   * @var \youconix\core\services\Headers
   */
  protected $headers;
  protected $a_files = [];
  protected $s_file;

  /**
   * Starts the class menuAdmin
   */
  public function __construct(\youconix\core\services\Xml $xml,
                              \youconix\core\models\ControlPanelModules $controlPanelModules,
                              \youconix\core\services\FileHandler $fileHandler,
                              \youconix\core\services\Headers $headers)
  {
    $this->xml = $xml;
    $this->controlPanelModules = $controlPanelModules;
    $this->fileHandler = $fileHandler;
    $this->headers = $headers;

    $this->parse();
  }

  abstract protected function parse();

  protected function getFiles($s_extension)
  {
    $s_cacheFile = $this->controlPanelModules->getCacheFile($s_extension);

    if ($this->fileHandler->exists($s_cacheFile)) {
      $this->s_file = $this->fileHandler->readFile($s_cacheFile);
      return;
    }

    $s_dir = $this->controlPanelModules->getDirectory();
    $a_modules = $this->controlPanelModules->getInstalledModulesList();

    foreach ($a_modules as $s_module) {
      $obj_settings = $this->xml->cloneService();
      $obj_settings->load($s_dir.DS.$s_module.'/settings.xml');

      if ($s_extension == 'js') {
        $s_jsLink = $obj_settings->get('module/js');
        $this->readFiles($s_module, $s_jsLink);
      } else {
        $s_css = $obj_settings->get('module/css');
        $this->readFiles($s_module, $s_css);
      }
    }

    $this->s_file = implode("\n", $this->a_files);
    if( !defined('DEBUG') ){
      $this->fileHandler->writeFile($s_cacheFile, $this->s_file);
    }
  }

  protected function readFiles($s_module, $s_link)
  {
    if (empty($s_link)) {
      return;
    }

    $a_files = explode(',', $s_link);
    $s_dir = NIV.'/admin/modules/'.$s_module.'/';
    foreach ($a_files as $s_itemLink) {
      if( $this->fileHandler->exists($s_dir.$s_itemLink) ){
        $s_file = $this->fileHandler->readFile($s_dir.$s_itemLink);
        $this->a_files[] = $s_file;
      }
    }
  }

  protected function display()
  {
    $this->headers->printHeaders();
    echo($this->s_file);
    die();
  }
}