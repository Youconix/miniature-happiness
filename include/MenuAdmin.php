<?php

namespace core;

/**
 * Displays the admin menu
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 2012,2013,2014  Rachelle Scheijen
 * @author    Rachelle Scheijen
 * @since     1.0
 * @changed   09/01/2013
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
class MenuAdmin {
    private $service_Language;
    private $service_Template;

    /**
     * Starts the class menuAdmin
     */
    public function __construct(\core\services\Language $service_Language, \core\services\Template $service_Template){
        $this->service_Language = $service_Language;
        $this->service_Template = $service_Template;

        $this->createMenu();
        
        $this->text();
    }

    /**
     * Generates the menu in XHTML-code
     */
    private function createMenu(){
        $this->service_Template->loadTemplate('menuAdmin','menu_admin_active.tpl');
    }

    /**
     * Displays the text
     */
    private function text(){
    	$this->service_Template->set('groups',$this->service_Language->get('admin/menu/groups'));
    	$this->service_Template->set('users',$this->service_Language->get('admin/menu/users'));
    	$this->service_Template->set('logs',$this->service_Language->get('admin/menu/logs'));
    	$this->service_Template->set('settings',$this->service_Language->get('admin/menu/settings'));
    	$this->service_Template->set('stats',$this->service_Language->get('admin/menu/stats'));
    	$this->service_Template->set('maintenance',$this->service_Language->get('admin/menu/maintenance'));
    	$this->service_Template->set('logoutAdmin',$this->service_Language->get('menu/logout'));
    }
}
?>
