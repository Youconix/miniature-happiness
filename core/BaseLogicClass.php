<?php
namespace core;

/**
 * General GUI parent class
 * This class is abstract and should be inheritanced by every controller with a gui
 *
 * This file is part of Scripthulp framework
 *
 * Scripthulp framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Scripthulp framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Scripthulp framework. If not, see <http://www.gnu.org/licenses/>.
 *
 * @changed 03/01/2015
 *
 * @copyright 2012,2013,2014 Rachelle Scheijen
 * @author Rachelle Scheijen
 * @version 1.0
 * @since 1.0
 * @see include/BaseClass.php
 */
if (! class_exists('\core\BaseClass')) {
    include (NIV . 'core/BaseClass.php');
}

abstract class BaseLogicClass extends BaseClass implements \Routable
{

    protected $service_Session;

    public function __construct()
    {
        $this->init();
    }

    /**
     * Routes the controller
     *
     * @see Routable::route()
     */
    public function route($s_command)
    {
        $this->$s_command();
        
        $this->showLayout();
    }

    protected function showLayout()
    {
        /* Call header */
        $obj_header = Memory::loadClass('Header');
        $obj_header->createHeader();
        
        /* Call Menu */
        $obj_menu = Memory::loadClass('Menu');
        $obj_menu->generateMenu();
        
        /* Call footer */
        $obj_footer = Memory::loadClass('Footer');
        $obj_footer->createFooter();
    }

    /**
     * Inits the class BaseLogicClass
     *
     * @see BaseClass::init()
     */
    protected function init()
    {
        parent::init();
        
        $this->service_Session = Memory::services('Session');
    }
}

?>
