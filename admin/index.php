<?php
namespace admin;

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
     *
     * @var \core\classes\HeaderAdmin
     */
    protected $headerAdmin;

    /**
     *
     * @var \core\classes\MenuAdmin
     */
    protected $menuAdmin;

    /**
     * Base graphic class constructor
     *
     * @param \core\services\Security $service_Security            
     * @param \core\models\Config $model_Config            
     * @param \core\services\Language $service_Language            
     * @param \core\services\Template $service_Template            
     * @param \core\classes\HeaderAdmin $header            
     * @param \core\classes\MenuAdmin $menu            
     * @param \core\classes\Footer $footer            
     * @param \core\helpers\ConfirmBox $confirmbox            
     */
    public function __construct(\core\services\Security $service_Security, \core\models\Config $model_Config, \core\services\Language $service_Language, \core\services\Template $service_Template, \core\classes\HeaderAdmin $header, \core\classes\MenuAdmin $menu, \core\classes\Footer $footer, \core\helpers\ConfirmBox $confirmbox)
    {
        $model_Config->setLayout('admin');
        
        $this->model_Config = $model_Config;
        $this->service_Language = $service_Language;
        $this->service_Template = $service_Template;
        $this->service_Security = $service_Security;
        
        $this->headerAdmin = $header;
        $this->footer = $footer;
        $this->menuAdmin = $menu;
        
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
        
        $this->service_Template->setJavascriptLink('<script src="{NIV}js/admin/language.php?lang=' . $this->service_Language->getLanguage() . '"></script>');
        $this->service_Template->set('noscript', '<noscript>' . $this->service_Language->get('language/noscript') . '</noscript>');
    }

    /**
     * Shows the header, menu and footer
     */
    protected function showLayout()
    {
        /* Call header */
        $this->headerAdmin->createHeader();
        
        /* Call Menu */
        $this->menuAdmin->generateMenu();
        
        /* Call footer */
        $this->footer->createFooter();
    }
}