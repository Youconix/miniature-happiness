<?php

namespace admin;

use \youconix\core\templating\BaseController as BaseController;

/**
 * Admin homepage
 *
 * @since 1.0
 */
class Index extends BaseController
{

  /**
   *
   * @var \youconix\core\helpers\ConfirmBox 
   */
  private $confirmBox;

  /**
   *
   * @var \youconix\core\helpers\OnOff
   */
  protected $slider;

  /**
   * Constructor
   *
   * @param \youconix\core\templating\AdminControllerWrapper $wrapper
   * @param \youconix\core\helpers\ConfirmBox $confirmbox
   */
  public function __construct(\youconix\core\templating\AdminControllerWrapper $wrapper,
      \youconix\core\helpers\ConfirmBox $confirmbox,
      \youconix\core\helpers\OnOff $slider
  )
  {
    parent::__construct($wrapper);

    $this->confirmBox = $confirmbox;
    $this->slider = $slider;
  }

  /**
   * 
   * @return \Output
   * @Route("/controlpanel", name="admin_index_view")
   */
  public function view()
  {
    $template = $this->createView('admin/index/view', [], 'admin');
    $this->setJavascript($template);

    return $template;
  }

  /**
   * Inits the class AdminLogicClass
   *
   * @see BaseClass::init()
   */
  protected function setJavascript($template)
  {
    $this->confirmBox->create($template);
    $this->slider->addHead($template);
    $template->set('currentLanguage', $this->getLanguage()->getLanguage());
    $template->set('noscript',
		   '<noscript>' . $this->getLanguage()->get('language/noscript') . '</noscript>');
  }
}
