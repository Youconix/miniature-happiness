<?php
namespace core\blocks;

/** 
 * General block interface                                                     
 *                                                                              
 * This file is part of Scripthulp framework                                    
 *                                                                              
 * @copyright 2012,2013,2014  Rachelle Scheijen                                
 * @author    Rachelle Scheijen                                                
 * @since     2.0                                                              
 * @changed   07/07/2014
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
abstract class Block {
    protected $service_Language;
    protected $service_Template;
    
    public function __construct(\core\services\Language $service_Language,\core\services\Template $service_Template){
      $this->service_Language = $service_Language;
      $this->service_Template = $service_Template;
    }
  
    /**
     * Clones the block
     * 
     * @return The cloned block
     */
    public function cloneBlock() {
        return clone $this;
    }
}
?>
