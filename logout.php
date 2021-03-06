<?php
/**
 * Log out page. Logs the user out of the system                                                   
 *                                                                              
 * This file is part of Scripthulp framework                                    
 *                                                                              
 * @copyright 2012,2013,2014  Rachelle Scheijen                                
 * @author    Rachelle Scheijen                                                
 * @since     1.0                                                              
 * @changed    20/11/12                                                   
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
define('NIV','./');
define('SH','1');
define('PROCESS','1');

include(NIV.'include/BaseClass.php');

class Logout extends BaseClass  {

    /**
     * Starts the class Logout
     */
    public function __construct(){
        $this->init();

		$this->logout();

        $this->service_Memory->endProgram();
    }


   /**
    * Logs the user out 
    */
   private function logout(){
       Memory::services('Authorization')->logout();

        header('location: '.NIV.'index.php');
        exit();
  } 
}

$obj_Logout = new Logout();
unset($obj_Logout);
