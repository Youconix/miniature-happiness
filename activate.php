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
 * Account activation page
 * Does not work for openID accounts
 *
 * @author:		Rachelle Scheijen
 * @copyright	Youconix
 * @version		1.0
 * @since		1.0
 */
define('NIV', './');
include (NIV . 'core/BaseLogicClass.php');

class Activation extends \core\BaseLogicClass
{

    private $model_User;

    /**
     * PHP 5 constructor
     */
    public function __construct()
    {
        $this->init();
        
        $this->activate();
        
        $this->header();
        
        $this->footer();
    }

    /**
     * Inits the class Activation
     */
    protected function init()
    {
        $this->init_get = array(
            'key' => 'string-DB'
        );
        
        parent::init();
        
        $this->model_User = Memory::models('User');
    }

    /**
     * Activates the user account
     */
    private function activate()
    {
        if (! isset($this->get['key'])) {
            header('location:index.php');
            exit();
        }
        
        if ($this->model_User->activateUser($this->get['key'])) {
            $this->service_Template->set('content', '<h2 class="notice">' . $this->service_Language->get('language/activate/accountActivated') . '</h2>');
        } else {
            $this->service_Template->set('content', '<h2 class="errorNotice">' . $this->service_Language->get('language/activate/accountNotActivated') . '</h2>');
        }
    }
}

$obj_Activation = new Activation();
unset($obj_Activation);