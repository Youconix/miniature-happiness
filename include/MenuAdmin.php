<?php
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
    public function __construct(){
        $this->init();

        $this->createMenu();
        
        $this->text();
    }

    /**
     * Stops the class menuAdmin
     */
    public function __destruct(){
        $this->service_Language = null;
        $this->service_Template = null;
    }

    /**
     * Inits the class menuAdmin
     */
    private function init(){
        $this->service_Language = Memory::services('Language');
        $this->service_Template = Memory::services('Template');
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
    	$this->service_Template->set('groups',$this->service_Language->get('language/admin/menu/groups'));
    	$this->service_Template->set('users',$this->service_Language->get('language/admin/menu/users'));
    	$this->service_Template->set('logs',$this->service_Language->get('language/admin/menu/logs'));
    	$this->service_Template->set('settings',$this->service_Language->get('language/admin/menu/settings'));
    	$this->service_Template->set('stats',$this->service_Language->get('language/admin/menu/stats'));
    	$this->service_Template->set('maintenance',$this->service_Language->get('language/admin/menu/maintenance'));
    	$this->service_Template->set('logoutAdmin',$this->service_Language->get('language/menu/logout'));
    }
}
?>
