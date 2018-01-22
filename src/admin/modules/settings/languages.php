<?php

namespace admin\modules\settings;

/**
 * Language configuration class
 * @author Rachelle Scheijen
 * @since 1.0
 */
class Languages extends \admin\modules\settings\Settings
{

  /**
   *
   * @var \core\services\FileHandler
   */
  private $fileHandler;

  /**
   *
   * @var \Config
   */
  private $config;

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
    $this->init_post = [
	'default_language' => 'string',
	'backup_language' => 'string',
    ];

    parent::init();
  }

  /**
   * Displays the languages
   * @Route("/controlpanel/settings/language", name="admin_settings_language_index")
   * @return \Output
   */
  public function language()
  {
    $template = $this->createView('admin/modules/settings/languages/view');

    $s_defaultLanguage = $this->getValue('defaultLanguage', 'en-UK');
    $s_backupLanguage = $this->getValue('fallbackLanguage', 'en-UK');

    $installedLanguages = $this->getLanguages();
    $languages = [];
    $backupLanguages = [];
    foreach ($installedLanguages as $language) {
      $selected = (($language == $s_defaultLanguage) ? 'selected="selected"' : '');

      $languages[] = [
	  'value' => $language,
	  'text' => $language,
	  'selected' => $selected
      ];
    }
    foreach ($installedLanguages as $language) {
      $selected = (($language == $s_backupLanguage) ? 'selected="selected"' : '');

      $backupLanguages[] = [
	  'value' => $language,
	  'text' => $language,
	  'selected' => $selected
      ];
    }

    $template->set('languageTitle', $this->getText('languages', 'title'));
    $template->set('defaultLanguageText', $this->getText('languages', 'defaultLanguage'));
    $template->set('backupLanguageText', $this->getText('languages', 'fallbackLanguage'));
    $template->set('languages', $languages);
    $template->set('backupLanguages', $backupLanguages);
    $template->set('saveButton', t('system/buttons/save'));
    $this->setDefaultValues($template);

    return $template;
  }

  /**
   * Saves the languages
   * @Route("/controlpanel/settings/language/save", name="admin_settings_language_save")
   */
  public function languageSave()
  {
    $post = $this->getRequest()->post();
    if (!$post->validate([
	    'default_language' => 'required|type:string|set:' . implode(', ',
								 $this->getLanguages()),
	    'backup_language' => 'required|type:string|set:' . implode(', ',
								$this->getLanguages())
	])) {
      $this->badRequest();
      return;
    }

    $this->setValue('defaultLanguage', $post->get('default_language'));
    $this->setValue('fallbackLanguage', $post->get('backup_language'));
    $this->settings->save();
  }

  /**
   * 
   * @return array
   */
  private function getLanguages()
  {
    $installedLanguages = $this->getInstalledLanguages();
    $languages = [];
    foreach ($installedLanguages as $language) {
      $s_filename = str_replace('.lang', '', $language->getFilename());
            
      $languages[] = $s_filename;
    }

    return $languages;
  }

  /**
   * Returns the current installed languages
   * 
   * @return \DirectoryIterator
   */
  private function getInstalledLanguages()
  {
    $languages = $this->fileHandler->readDirectory(NIV . 'language');
    $languages = $this->fileHandler->directoryFilterName($languages,
							 [
	'*-*|*.lang'
    ]);

    return $languages;
  }
}
