<?php
namespace admin;

/**
 * Admin homepage
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 2012,2013,2014 Rachelle Scheijen
 * @author Rachelle Scheijen
 * @since 1.0
 *        @changed 25/09/10
 *       
 *        Scripthulp framework is free software: you can redistribute it and/or modify
 *        it under the terms of the GNU Lesser General Public License as published by
 *        the Free Software Foundation, either version 3 of the License, or
 *        (at your option) any later version.
 *       
 *        Scripthulp framework is distributed in the hope that it will be useful,
 *        but WITHOUT ANY WARRANTY; without even the implied warranty of
 *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *        GNU General Public License for more details.
 *       
 *        You should have received a copy of the GNU Lesser General Public License
 *        along with Scripthulp framework. If not, see <http://www.gnu.org/licenses/>.
 */
include (NIV . 'core/BaseLogicClass.php');

class Index extends \core\BaseLogicClass
{

    private $service_Logs;

    protected function view()
    {}

    /**
     * Routes the controller
     *
     * @see Routable::route()
     */
    public function route($s_command)
    {
        $this->$s_command();
        
        \core\Memory::loadClass('MenuAdmin');
    }

    /**
     * Inits the class AdminLogicClass
     *
     * @see BaseClass::init()
     */
    protected function init()
    {
        define('LAYOUT', 'admin');
        
        $this->forceSSL();
        
        parent::init();
        
        $this->service_Session = \core\Memory::services('Session');
        $this->model_User = \core\Memory::models('User');
        
        $this->service_Template->setJavascriptLink('<script src="{NIV}js/admin/language.php?lang=' . $this->service_Language->getLanguage() . '"></script>');
    }
}
?>