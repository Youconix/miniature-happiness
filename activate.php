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
    private $headers;

    /**
     * 
     * @var \core\models\User
     */
    private $user;

    /**
     * Class constructor
     * 
     * @param \core\Input $input    The input parser
     * @param \core\models\Config $config
     * @param \core\services\Language $language
     * @param \core\services\Template $template
     * @param \core\classes\Header $header
     * @param \core\classes\Menu $menu
     * @param \core\classes\Footer $footer
     * @param \core\models\User $user
     * @param \core\services\Headers $headers
     */
    public function __construct(\core\Input $input,\core\models\Config $config,\core\services\Language $language,
        \core\services\Template $template,\core\classes\Header $header,\core\classes\Menu $menu,\core\classes\Footer $footer,\core\models\User $user,\core\services\Headers $headers)
    {
        parent::__construct($input, $config, $language, $template, $header, $menu, $footer);
        
        $this->user = $user;
        $this->headers = $headers;
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
            $this->$headers->redirect('index/view');
        }
        
        if ($this->user->activate($this->get['key'])) {
            $s_redirect = $this->config->getActivationRedirect();
            
            $this->service_Template->set('content', '<h2 class="notice">' . t('activate/accountActivated') . '</h2>
            <meta http-equiv="refresh" content="1;URL=\''.$s_redirect.'\'" /> ');
        } else {
            $this->service_Template->set('content', '<h2 class="errorNotice">' . t('activate/accountNotActivated') . '</h2>');
        }
    }
}