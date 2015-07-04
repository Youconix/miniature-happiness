<?php
namespace admin;

use core\BaseLogicClass;
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
include (NIV . 'core/BaseLogicClass.php');

class Index extends \core\BaseLogicClass
{
    /**
     * Base graphic class constructor
     *
     * @param \Input $Input    The input parser       
     * @param \Config $model_Config            
     * @param \Language $service_Language            
     * @param \Output $service_Template            
     * @param \core\classes\HeaderAdmin $header            
     * @param \core\classes\MenuAdmin $menu            
     * @param \\Footer $footer            
     * @param \core\helpers\ConfirmBox $confirmbox            
     */
    public function __construct(\Input $Input, \Config $model_Config, \Language $service_Language, \Output $service_Template, \core\classes\HeaderAdmin $header, \core\classes\MenuAdmin $menu, \Footer $footer, \core\helpers\ConfirmBox $confirmbox)
    {
        $model_Config->setLayout('admin');
        
        parent::__construct($input, $config, $language, $template, $header, $menu, $footer);
        
       $this->prepareInput($Input);
        
        $this->init();
        
        $confirmbox->create();
    }

    protected function view()
    {}

    /**
     * Inits the class AdminLogicClass
     *
     * @see BaseClass::init()
     */
    protected function init()
    {        
        parent::init();
        
        $this->template->setJavascriptLink('<script src="{NIV}js/admin/language.php?lang=' . $this->language->getLanguage() . '"></script>');
        $this->template->set('noscript', '<noscript>' . $this->language->get('language/noscript') . '</noscript>');
    }
}