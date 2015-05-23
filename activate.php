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
class Activate extends \includes\BaseLogicClass
{
    /**
     * 
     * @var \core\services\Headers
     */
    private $service_Headers;

    /**
     * 
     * @var \core\models\Registration
     */
    private $model_Registration;

    /**
     * Class constructor
     * 
     * @param \core\services\Security $service_Security
     * @param \core\models\Config $model_Config
     * @param \core\services\Language $service_Language
     * @param \core\services\Template $service_Template
     * @param \core\classes\Header $header
     * @param \core\classes\Menu $menu
     * @param \core\classes\Footer $footer
     * @param \core\models\Registration $model_Registration
     * @param \core\services\Headers $service_Headers
     */
    public function __construct(\core\services\Security $service_Security,\core\models\Config $model_Config,
        \core\services\Language $service_Language,\core\services\Template $service_Template,
        \core\classes\Header $header,\core\classes\Menu $menu,\core\classes\Footer $footer,\core\models\Registration $model_Registration,\core\services\Headers $service_Headers)
    {
        parent::__construct($service_Security, $model_Config, $service_Language, $service_Template, $header, $menu, $footer);
        
        $this->model_Registration = $model_Registration;
        $this->service_Headers = $service_Headers;
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
    }

    /**
     * Activates the user account
     */
    protected function code()
    {
        if (! isset($this->get['key'])) {
            $this->service_Headers->redirect('index/view');
        }
        
        if ($this->model_Registration->activateUser($this->get['key'])) {
            $s_redirect = $this->model_Config->getActivationRedirect();
            
            $this->service_Template->set('content', '<h2 class="notice">' . $this->service_Language->get('language/activate/accountActivated') . '</h2>
            <meta http-equiv="refresh" content="1;URL=\''.$s_redirect.'\'" /> ');
        } else {
            $this->service_Template->set('content', '<h2 class="errorNotice">' . $this->service_Language->get('language/activate/accountNotActivated') . '</h2>');
        }
    }
}