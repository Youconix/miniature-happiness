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
class Index extends \core\BaseLogicClass
{
    /**
     * Base graphic class constructor
     *
     * @param \Input $Input    The input parser       
     * @param \Config $config            
     * @param \Language $language            
     * @param \Output $template            
     * @param \core\classes\HeaderAdmin $header            
     * @param \core\classes\MenuAdmin $menu            
     * @param \\Footer $footer            
     * @param \core\helpers\ConfirmBox $confirmbox            
     */
    public function __construct(\Input $Input, \Config $config, \Language $language, \Output $template, \core\classes\HeaderAdmin $header, \core\classes\MenuAdmin $menu, \Footer $footer, \core\helpers\ConfirmBox $confirmbox)
    {
        $config->setLayout('admin');
        
        parent::__construct($Input, $config, $language, $template, $header, $menu, $footer);
        
       $this->prepareInput($Input);
        
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