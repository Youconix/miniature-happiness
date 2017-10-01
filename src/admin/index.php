<?php

namespace admin;

use \youconix\core\templating\BaseController as BaseController;
use \youconix\core\templating\gui\AdminLogicClass AS Layout;

/**
 * Miniature-happiness is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Miniature-happiness is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Miniature-happiness. If not, see <http://www.gnu.org/licenses/>.
 *
 * Admin homepage
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 1.0
 */
class Index extends BaseController
{
  /**
   *
   * @var \Language
   */
  private $language;

  /**
   *
   * @var \youconix\core\helpers\ConfirmBox 
   */
  private $confirmBox;

  /**
   * Constructor
   *
   * @param \Request $request
   * @param \Config $config            
   * @param \Language $language            
   * @param \Output $template
   * @param \youconix\core\templating\gui\AdminLogicClass $layout
   * @param \youconix\core\helpers\ConfirmBox $confirmbox
   */
  public function __construct(\Request $request, \Language $language,
                              \Output $template, Layout $layout,
                              \youconix\core\helpers\ConfirmBox $confirmbox)
  {
    parent::__construct($request, $layout, $template);

    $this->language = $language;
    $this->confirmBox = $confirmbox;
  }

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
    $template->append('head',
        '<script src="/js/admin/language.php?lang='.$this->language->getLanguage().'"></script>');
    $template->set('noscript',
        '<noscript>'.$this->language->get('language/noscript').'</noscript>');
  }
}