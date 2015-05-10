<?php
namespace core;

/**
 * General GUI parent class
 * This class is abstract and should be inheritanced by every controller with a gui
 *
 * This file is part of Miniature-happiness
 *
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
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @version 1.0
 * @since 1.0
 * @see core/BaseClass.php
 */
if (! class_exists('\core\BaseClass')) {
    include (NIV . 'core/BaseClass.php');
}

abstract class BaseLogicClass extends \core\BaseClass implements \Routable
{
    /**
     * @var \core\models\Config 
     */
    protected $model_Config;
    
    /**
     * @var \core\services\Template
     */
    protected $service_Template;
    
    /**
     * @var \core\services\Language
     */
    protected $service_Language;
    
    /**
     * @var \core\classes\Header
     */
    protected $header;
    
    /**
     * @var \core\classes\Menu 
     */
     protected $menu;
     
     /**
      * @var \core\classes\Footer
      */
     protected $footer;

    /**
     * Base graphic class constructor
     * 
     * @param \core\services\Security $service_Security
     * @param \core\models\Config $model_Config
     * @param \core\services\Language $service_Language
     * @param \core\services\Template $service_Template
     * @param \core\classes\Header $header
     * @param \core\classes\Menu $menu
     * @param \core\classes\Footer $footer
     */
    public function __construct(\core\services\Security $service_Security,\core\models\Config $model_Config,
        \core\services\Language $service_Language,\core\services\Template $service_Template,
        \core\classes\Header $header,\core\classes\Menu $menu,\core\classes\Footer $footer)
    {
        $this->model_Config = $model_Config;
        $this->service_Language = $service_Language;
        $this->service_Template = $service_Template;
        $this->header = $header;
        $this->menu = $menu;
        $this->footer = $footer;
        
        parent::__construct($service_Security);
    }

    /**
     * Routes the controller
     *
     * @see Routable::route()
     */
    public function route($s_command)
    {
        if (! method_exists($this, $s_command)) {
            throw new \BadMethodCallException('Call to unkown method '.$s_command.' on class '.get_class($this).'.');
        }
        
        $this->$s_command();
        
        $this->showLayout();
    }

    /**
     * Shows the header, menu and footer
     */
    protected function showLayout()
    {
        /* Call header */
        $this->header->createHeader();
        
        /* Call Menu */
        $this->menu->generateMenu();
        
        /* Call footer */
        $this->footer->createFooter();
    }

    /**
     * Inits the class BaseLogicClass
     *
     * @see BaseClass::init()
     */
    protected function init()
    {
        parent::init();
        
        $s_language = $this->service_Language->getLanguage();
        $this->service_Template->setJavascriptLink('<script src="{NIV}js/language.php?lang='.$s_language.'" type="text/javascript"></script>');
        $this->service_Template->setJavascriptLink('<script src="{NIV}js/site.js" type="text/javascript"></script>');
        
        if (! $this->model_Config->isAjax()) {
            $this->loadView();
        }
        
        /* Call statistics */
        if (! $this->model_Config->isAjax() && stripos($_SERVER['PHP_SELF'], 'admin/') === false)
            require (NIV . 'stats/statsView.php');
    }

    /**
     * Loads the view
     */
    protected function loadView()
    {
        /* Set language and encoding */
        $this->service_Template->set('lang', $this->service_Language->getLanguage());
        $this->service_Template->set('encoding', $this->service_Language->getEncoding());
        if ($this->service_Language->exists('title')) {
            $this->service_Template->set('mainTitle', $this->service_Language->get('title') . ',  ');
        }
    }
}