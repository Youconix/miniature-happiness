<?php
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
 * Log out page. Logs the user out of the system                                                   
 *                                                                              
 * This file is part of Miniature-happiness                                    
 *                                                                              
 * @copyright Youconix
 * @author    Rachelle Scheijen
 * @since     1.0
 */
define('NIV', './');
define('SH', '1');
define('PROCESS', '1');

include (NIV . 'core/BaseClass.php');

class Logout extends \core\BaseClass
{

    /**
     * Starts the class Logout
     */
    public function __construct()
    {
        $this->init();
        
        $this->logout();
    }

    /**
     * Logs the user out
     */
    private function logout()
    {
        \Loader::inject('\core\models\Login')->logout();
        
	$s_url = \core\Memory::parseUrl('index.php');
        \Loader::inject('\core\services\Headers')->redirect($s_url);
    }
}

$obj_Logout = new Logout();
unset($obj_Logout);
