<?php

namespace admin\modules\settings;

use \youconix\core\templating\AdminController as AdminController;

/**
 * Admin settings configuration class
 *
 * @author Rachelle Scheijen
 * @since 1.0
 */
abstract class Settings extends AdminController
{
  /**
   *
   * @var \youconix\core\helpers\OnOff
   */
  protected $onOff;
  
  /**
   *
   * @var \Settings
   */
  protected $settings;

  /**
   * Constructor
   * 
   * @param \youconix\core\templating\AdminControllerWrapper $wrapper
   * @param \youconix\core\helpers\OnOff $onOff
   * @param \Settings $settings
   */
  public function __construct(\youconix\core\templating\AdminControllerWrapper $wrapper,
			      \youconix\core\helpers\OnOff $onOff,
			      \Settings $settings)
  {
    parent::__construct($wrapper);

    $this->settings = $settings;
    $this->onOff = $onOff;
  }

  /**
   * Returns the value
   *
   * @param string $s_key
   *            The key
   * @param string $default
   *            The default value if the key does not exist
   * @return string The value
   */
  protected function getValue($s_key, $default = '')
  {
    if (!$this->settings->exists($s_key)) {
      return $default;
    }

    $s_value = $this->settings->get($s_key);
    if (empty($s_value) && !empty($default)) {
      return $default;
    }

    return $s_value;
  }

  /**
   * Sets the value
   *
   * @param string $s_key
   *            The key
   * @param string $s_value
   *            The value
   */
  protected function setValue($s_key, $s_value)
  {
    if (!$this->settings->exists($s_key)) {
      $this->settings->add($s_key, $s_value);
    } else {
      $this->settings->set($s_key, $s_value);
    }
  }
  
  /**
     * Loads the given view into the parser
     *
     * @param string $s_view
     *            The view relative to the template-directory
     * @param array $a_data
     * 		  Data as key-value pair
     * @param string $s_templateDir
     * 		  Override the default template directory
     * @return \Output
     * @throws \TemplateException if the view does not exist
     * @throws \IOException if the view is not readable
     */
    protected function createView($s_view, $a_data = [], $s_templateDir = 'admin')
    {
        $output = $this->wrapper->getOutput();

        $output->load($s_view, $s_templateDir);
        $output->setArray($a_data);
        $this->wrapper->getLayout()->parse($output);

        return $output;
    }
}
