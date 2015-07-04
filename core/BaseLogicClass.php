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
abstract class BaseLogicClass extends \core\BaseClass implements \Routable
{
    /**
     * @var \Config 
     */
    protected $config;
    
    /**
     * @var \Output
     */
    protected $template;
    
    /**
     * @var \Language
     */
    protected $language;
    
    /**
     * @var \Header
     */
    protected $header;
    
    /**
     * @var \Menu 
     */
     protected $menu;
     
     /**
      * @var \Footer
      */
     protected $footer;

    /**
     * Base graphic class constructor
     * 
     * @param \Input $input    The input parser
     * @param \Config $config
     * @param \Language $language
     * @param \Output $template
     * @param \Header $header
     * @param \Menu $menu
     * @param \Footer $footer
     */
    public function __construct(\Input $input,\Config $config,\Language $language,\Output $template,
        \Header $header, \Menu $menu, \Footer $footer)
    {
        $this->config = $config;
        $this->language = $language;
        $this->template = $template;
        $this->header = $header;
        $this->menu = $menu;
        $this->footer = $footer;
        
        parent::__construct($input);
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
        
        $s_language = $this->language->getLanguage();
        $this->template->setJavascriptLink('<script src="{NIV}js/language.php?lang='.$s_language.'" type="text/javascript"></script>');
        $this->template->setJavascriptLink('<script src="{NIV}js/site.js" type="text/javascript"></script>');
        
        if (! $this->config->isAjax()) {
            $this->loadView();
        }
        
        /* Call statistics */
        if (! $this->config->isAjax() && stripos($_SERVER['PHP_SELF'], 'admin/') === false)
            require (NIV . 'stats/statsView.php');
    }

    /**
     * Loads the view
     */
    protected function loadView()
    {
        /* Set language and encoding */
        $this->template->set('lang', $this->language->getLanguage());
        $this->template->set('encoding', $this->language->getEncoding());
        if ($this->language->exists('title')) {
            $this->template->set('mainTitle', $this->language->get('title') . ',  ');
        }
    }
}