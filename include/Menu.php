<?php

namespace core;

/**
 * Site menu
 *                                                                              
 * This file is part of Scripthulp framework  
 *
 * @copyright 2012,2013,2014  Rachelle Scheijen
 * @author    Rachelle Scheijen
 * @since     1.0
 * @changed   12/07/12
 * 
 *                                                                             
 * Scripthulp framework is free software: you can redistribute it and/or modify 
 * it under the terms of the GNU Lesser General Public License as published by  
 * the Free Software Foundation, either version 3 of the License, or            
 * (at your option) any later version.                                          
 *                                                                              
 * Scripthulp framework is distributed in the hope that it will be useful,      
 * but WITHOUT ANY WARRANTY; without even the implied warranty of               
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                
 * GNU General Public License for more details.                                 
 *                                                                              
 * You should have received a copy of the GNU Lesser General Public License     
 * along with Scripthulp framework.  If not, see <http://www.gnu.org/licenses/>.
 */
class Menu {

    private $service_Template;
    private $service_Language;
    private $obj_User;
    private $model_Groups;

    /**
     * Starts the class menu
     */
    public function __construct(\core\services\Template $service_Template,  \core\services\Language $service_Language,
        \core\models\User $model_User,  \core\models\Groups $model_Groups){
        $this->service_Template = $service_Template;
        $this->service_Language = $service_Language;
        $this->obj_User = $model_User->get();
        $this->model_Groups = $model_Groups;

        $this->generateMenu();
    }

    /**
     * Generates the menu 
     */
    protected function generateMenu() {
        if (defined('USERID')) {
            $this->loggedIn();

            if ($this->obj_User->isAdmin(GROUP_ADMIN)) {
                $this->service_Template->loadTemplate('menu', 'menu_admin.tpl');

                $this->service_Template->set('adminPanel', '<a href="{NIV}admin/"  class="subadmin">' . $this->service_Language->get('menu/adminPanel') . '</a>');
            } else {
                $this->service_Template->loadTemplate('menu', 'menu.tpl');
            }
        } else {
            $this->service_Template->loadTemplate('menu', 'menu.tpl');

            $this->loggedout();
        }
    }

    /**
     * Displays the logged out items
     */
    protected function loggedout() {
        $this->service_Template->set('home', $this->service_Language->get('system/menu/home'));
        $this->service_Template->set('login', '<a href="{LEVEL}login">' . $this->service_Language->get('system/menu/login') . '</a>');
        $this->service_Template->set('registration', $this->service_Language->get('system/login/registration'));
    }

    /**
     * Displays the logged in items
     */
    protected function loggedIn() {
        $this->service_Template->set('home', $this->service_Language->get('menu/home'));
        $this->service_Template->set('logout', '<a href="{NIV}logout.php">' . $this->service_Language->get('menu/logout') . '</a>');
    }

}

?>
