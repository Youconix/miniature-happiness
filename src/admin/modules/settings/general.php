<?php

namespace admin\modules\settings;

/**
 * General settings configuration class
 * @author Rachelle Scheijen
 * @since 1.0
 */
class General extends \admin\modules\settings\Settings
{

  /**
   *
   * @var \youconix\core\services\FileHandler
   */
  private $fileHandler;

  /**
   *
   * @var \Config
   */
  private $config;
  private $invalidTemplates = ['admin', 'shared'];

  /**
   * Constructor
   *
   * @param \youconix\core\templating\AdminControllerWrapper $wrapper
   * @param \youconix\core\helpers\OnOff $onOff
   * @param \Settings $settings      
   * @param \youconix\core\services\FileHandler $fileHandler            
   * @param \Config $config
   */
  public function __construct(\youconix\core\templating\AdminControllerWrapper $wrapper,
			      \youconix\core\helpers\OnOff $onOff, \Settings $settings,
			      \youconix\core\services\FileHandler $fileHandler, \Config $config)
  {
    parent::__construct($wrapper, $onOff, $settings);

    $this->fileHandler = $fileHandler;
    $this->config = $config;
  }

  /**
   * Inits the class Settings
   */
  protected function init()
  {
    $this->init_post = array(
	'name_site' => 'string',
	'site_url' => 'string',
	'site_base' => 'string',
	'timezone' => 'string',
	'template' => 'string',
	'logger' => 'string',
	'log_location' => 'string',
	'log_size' => 'int'
    );

    parent::init();
  }

  /**
   * Displays the general settings
   * @Route("/controlpanel/settings/general", name="admin_settings_general_index")
   * @return \Output
   */
  public function general()
  {
    $template = $this->createView('admin/modules/settings/general/general');

    $template->set('generalTitle', t('system/settings/general/title'));

    $template->set('nameSiteText', t('system/settings/general/nameSite'));
    $template->set('nameSite', $this->getValue('main/nameSite'));
    $template->set('nameSiteError', t('system/settings/general/siteNameEmpty'));
    $template->set('siteUrlText', t('system/settings/general/siteUrl'));
    $template->set('siteUrl', $this->getValue('main/url'));
    $template->set('siteUrlError', t('system/settings/general/urlEmpty'));
    $template->set('siteBaseText', t('system/settings/general/basedir'));
    $template->set('siteBase', $this->getValue('main/base'));
    $template->set('timezoneText', t('system/settings/general/timezone'));
    $template->set('timezone', $this->getValue('main/timeZone'));
    $template->set('timezoneError', t('system/settings/general/timezoneInvalid'));

    /* Templates */
    $template->set('templatesHeader', t('system/settings/general/templates'));
    $template->set('templateText', 'Template set');    
    $template->set('template', $this->parseTemplateDirs());

    /* Logs */
    $template->set('loggerText',
		   t('system/settings/general/logger') . ' (\Psr\Log\LoggerInterface)');
    $template->set('logger', $this->getValue('main/logs', 'default'));
    $template->set('loggerError', t('system/settings/general/loggerInvalid'));

    $template->set('location_log_default', '');
    if ($this->getValue('main/logs', 'default') != 'default') {
      $template->set('location_log_default', 'style="display:none"');
    }

    $template->set('logLocationText', t('system/settings/general/logLocation'));
    $template->set('logLocation',
      $this->getValue('main/log_location', 
      $this->getDefaultLogDirectory()
    ));
    $template->set('logLocationError', t('system/settings/general/logLocationInvalid'));
    $template->set('logSizeText', t('system/settings/general/logSize'));
    $config = $this->config;
    $template->set('logSize',
		   $this->getValue('main/log_max_size', $config::LOG_MAX_SIZE));
    $template->set('logSizeError', t('system/settings/general/logSizeInvalid'));

    $template->set('saveButton', t('system/buttons/save'));
    $this->setDefaultValues($template);

    return $template;
  }
  
  /**
   * 
   * @return array
   */
  private function parseTemplateDirs()
  {
    $s_template = $this->getValue('templates/dir', 'default');
    $templates = $this->getTemplateDirs();
    $a_templates = [];
    foreach ($templates as $dir) {
      if (in_array($dir->getFilename(), $this->invalidTemplates)) {
	continue;
      }
      $selected = (($dir->getFilename() == $s_template) ? 'selected="selected"' : '');

      $a_templates[] = [
	  'value' => $dir->getFilename(),
	  'selected' => $selected,
	  'text' => $dir->getFilename()
      ];
    }
    return $a_templates;
  }

  /**
   * 
   * @return \youconix\core\classes\OnlyDirectoryFilterIteractor
   */
  private function getTemplateDirs()
  {
    $directory = $this->fileHandler->directoryFilterName(
	$this->fileHandler->readDirectory(NIV . 'styles'), [
	'!.',
	'!..'
    ]);
    $templates = new \youconix\core\classes\OnlyDirectoryFilterIteractor($directory);
    return $templates;
  }
  
  /**
   * 
   * @return string
   */
  private function getDefaultLogDirectory()
  {
    $dataDir = str_replace('../','',
	str_replace('./', '', DATA_DIR)
    );
    return $dataDir . 'logs' . DIRECTORY_SEPARATOR;
  }

  /**
   * Saves the general settings
   * @Route("/controlpanel/settings/general/save", name="admin_settings_general_save")
   */
  public function generalSave()
  {
    $post = $this->getRequest()->post();
    if (!$post->validate(array(
	    'name_site' => 'required',
	    'site_url' => 'required|pattern:url',
	    "timezone" => 'required|pattern:#^[a-zA-Z]+/[a-zA-Z]+$#',
	    "template" => 'required',
	    'logger' => 'required',
	    'log_location' => 'type:string',
	    'log_size' => 'required|type:int|min:1000'
	))) {
      return;
    }

    if ($post->get('logger') == 'default' && empty($post->get('log_location'))) {
      return;
    }

    $this->setValue('main/nameSite', $post->get('name_site'));
    $this->setValue('main/url', $post->get('site_url'));
    $this->setValue('main/timeZone', $post->get('timezone'));
    $this->setValue('templates/dir', $post->get('template'));
    $this->setValue('main/logs', $post->get('logger'));
    $this->setValue('main/log_location',
		    str_replace(DATA_DIR, '', $post->get('log_location')));
    $this->setValue('main/log_max_size', $post->get('log_size'));

    $this->settings->save();
  }
}
