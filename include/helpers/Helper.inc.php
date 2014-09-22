<?php
namespace core\helpers;

/** 
 * General helper interface                                                     
 *                                                                              
 * This file is part of Scripthulp framework                                    
 *                                                                              
 * @copyright 2012,2013,2014  Rachelle Scheijen                                
 * @author    Rachelle Scheijen                                                
 * @since     1.0                                                              
 * @changed    24/05/12                                                         
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
abstract class Helper {   
    /**
     * Clones the helper
     * 
     * @return The cloned helper
     */
    public function cloneHelper() {
        return clone $this;
    }
}

interface Display{
	/**
	 * Generates the HTML code
	 * 
	 * @return string	The HTML code
	 */
	public function generate();
}
?>
