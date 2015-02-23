<?php
namespace core\classes;

/**
 * Site menu
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 1.0
 *       
 *       
 *        Miniature-happiness is free software: you can redistribute it and/or modify
 *        it under the terms of the GNU Lesser General Public License as published by
 *        the Free Software Foundation, either version 3 of the License, or
 *        (at your option) any later version.
 *       
 *        Miniature-happiness is distributed in the hope that it will be useful,
 *        but WITHOUT ANY WARRANTY; without even the implied warranty of
 *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *        GNU General Public License for more details.
 *       
 *        You should have received a copy of the GNU Lesser General Public License
 *        along with Miniature-happiness. If not, see <http://www.gnu.org/licenses/>.
 */
class Menu
{

    protected $service_Template;

    protected $service_Language;

    protected $obj_User;

    protected $model_Groups;

    /**
     * Starts the class menu
     */
    public function __construct(\core\services\Template $service_Template, \core\services\Language $service_Language, \core\models\User $model_User, \core\models\Groups $model_Groups)
    {
        $this->service_Template = $service_Template;
        $this->service_Language = $service_Language;
        $this->obj_User = $model_User->get();
        $this->model_Groups = $model_Groups;
    }

    /**
     * Generates the menu
     */
    public function generateMenu()
    {
        $this->service_Template->set('home', $this->service_Language->get('menu/home'));
        
        if (defined('USERID')) {
            $this->service_Template->displayPart('menuLoggedIn');
            
            $this->loggedIn();
        } else {
            $this->loggedout();
        }
    }

    /**
     * Displays the logged out items
     */
    protected function loggedout()
    {
        $this->service_Template->set('login', $this->service_Language->get('menu/login'));
        $this->service_Template->set('registration', $this->service_Language->get('menu/registration'));
    }

    /**
     * Displays the logged in items
     */
    protected function loggedIn()
    {
        $this->service_Template->set('logout', $this->service_Language->get('menu/logout'));
        
        if ($this->obj_User->isAdmin(GROUP_ADMIN)) {
            $this->service_Template->displayPart('menuAdmin');
            
            $this->service_Template->set('adminPanel', $this->service_Language->get('system/menu/adminPanel'));
        }
    }
}