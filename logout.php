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
if( strpos($_SERVER['REQUEST_URI'],'logout.php') !== false ){
    $s_url = str_replace('logout.php', 'logout/performLogout', $_SERVER['REQUEST_URI']);
    header('location: '.$s_url);
    die();
}

class Logout extends \core\BaseClass implements \Routable
{
    /**
     * 
     * @var \core\models\Login
     */
    private $login;
    
    
    /**
     * Starts the class Logout
     *
     * @param \core\Input $input    The input parser           
     */
    public function __construct(\core\Input $input,\core\models\Login $login)
    {
        parent::__construct($input);
        
        $this->login = $login;
    }
    
    /**
     * Routes the controller
     *
     * @see Routable::route()
     */
    public function route($s_command)
    {
        $this->performLogout();
    }

    /**
     * Logs the user out
     */
    protected function performLogout()
    {
        $this->login->logout();
    }
}