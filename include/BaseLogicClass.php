<?php
/**
 * General GUI parent class
 * This class is abstract and should be inheritanced by every controller with a gui
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 		2012,2013,2014  Rachelle Scheijen
 * @author    		Rachelle Scheijen
 * @version		1.0
 * @since		    1.0
 * @date			12/01/2006
 * @changed   		03/03/2010
 * @see				include/BaseClass.php
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
include(NIV.'include/BaseClass.php');

abstract class BaseLogicClass extends BaseClass {
	protected $service_Session;
	
	/**
	 * Inits the class BaseLogicClass
	 * 
	 * @see BaseClass::init()
	 */
	protected function init(){
		parent::init();
		
		$this->service_Session = \core\Memory::services('Session');
	}
	
	/**
	 * Displays the site header
	 */
    protected function header(){
    	/* Call header */
	    \core\Memory::loadClass(NIV.'include/Header.php','Header');
    }

    /**
     * Displays the site menu
     */
    protected function menu(){
        /* Call Menu */
        \core\Memory::loadClass(NIV.'include/Menu.php','Menu');
    }

    /**
     * Displays the site footer
     */
    protected function footer(){
        /* Call footer */
        \core\Memory::loadClass(NIV.'include/Footer.php','Footer');
    }
}

?>
