<?php
namespace admin\modules\settings;

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
 * Admin settings configuration class
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 1.0
 */
class General extends \admin\modules\settings\Settings
{

    /**
     *
     * @var \core\services\FileHandler
     */
    private $fileHandler;

    /**
     * Constructor
     *
     * @param \Input $Input            
     * @param \Config $config            
     * @param \Language $language            
     * @param \Output $template            
     * @param \Logger $logs            
     * @param \Settings $settings            
     * @param \core\services\FileHandler $fileHandler            
     */
    public function __construct(\Input $Input, \Config $config, \Language $language, \Output $template, \Logger $logs, \Settings $settings, \core\services\FileHandler $fileHandler)
    {
        parent::__construct($Input, $config, $language, $template, $logs, $settings);
        
        $this->fileHandler = $fileHandler;
    }

    /**
     * Routes the controller
     *
     * @see Routable::route()
     */
    public function route($s_command)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            switch ($s_command) {
                case 'general':
                    $this->general();
                    break;
                
                case 'ssl':
                    $this->ssl();
                    break;
            }
        } else {
            switch ($s_command) {
                case 'general':
                    $this->generalSave();
                    break;
                
                case 'ssl':
                    $this->sslSave();
                    break;
            }
        }
    }

    /**
     * Inits the class Settings
     */
    protected function init()
    {
        $this->init_post = array(
            'name_site' => 'string',
            'site_url' => 'string',
            'site_base' => 'string',
            'timezone' => 'string',
            'template' => 'string',
            'logger' => 'string',
            'log_location' => 'string',
            'log_size' => 'int',
            'ssl' => 'int'
        );
        
        parent::init();
    }

    /**
     * Displays the general settings
     */
    private function general()
    {
        $this->template->set('generalTitle', t('system/settings/general/title'));
        
        $this->template->set('nameSiteText', t('system/settings/general/nameSite'));
        $this->template->set('nameSite', $this->getValue('main/nameSite'));
        $this->template->set('nameSiteError', t('system/settings/general/siteNameEmpty'));
        $this->template->set('siteUrlText', t('system/settings/general/siteUrl'));
        $this->template->set('siteUrl', $this->getValue('main/url'));
        $this->template->set('siteUrlError', t('system/settings/general/urlEmpty'));
        $this->template->set('siteBaseText', t('system/settings/general/basedir'));
        $this->template->set('siteBase', $this->getValue('main/base'));
        $this->template->set('timezoneText', t('system/settings/general/timezone'));
        $this->template->set('timezone', $this->getValue('main/timeZone'));
        $this->template->set('timezoneError', t('system/settings/general/timezoneInvalid'));
        
        /* Templates */
        $this->template->set('templatesHeader', t('system/settings/general/templates'));
        $this->template->set('templateText', 'Template set');
        $s_template = $this->getValue('templates/dir', 'default');
        
        $directory = $this->fileHandler->readDirectory(NIV . 'styles');
        $directory = $this->fileHandler->directoryFilterName($directory, array(
            '!.',
            '!..'
        ));
        $templates = new \core\classes\OnlyDirectoryFilterIteractor($directory);
        
        foreach ($templates as $dir) {
            ($dir->getFilename() == $s_template) ? $selected = 'selected="selected"' : $selected = '';
            
            $this->template->setBlock('template', array(
                'value' => $dir->getFilename(),
                'selected' => $selected,
                'text' => $dir->getFilename()
            ));
        }
        
        /* Logs */
        $this->template->set('loggerText', t('system/settings/general/logger') . ' (\Psr\Log\LoggerInterface)');
        $this->template->set('logger', $this->getValue('main/logs', 'default'));
        $this->template->set('loggerError', t('system/settings/general/loggerInvalid'));
        if ($this->getValue('main/logs', 'default') != 'default') {
            $this->template->set('location_log_default', 'style="display:none"');
        }
        
        $this->template->set('logLocationText', t('system/settings/general/logLocation'));
        $this->template->set('logLocation', $this->getValue('main/log_location', str_replace(array(
            '../',
            './'
        ), array(
            '',
            ''
        ), DATA_DIR) . 'logs' . DIRECTORY_SEPARATOR));
        $this->template->set('logLocationError', t('system/settings/general/logLocationInvalid'));
        $this->template->set('logSizeText', t('system/settings/general/logSize'));
        $config = $this->config;
        $this->template->set('logSize', $this->getValue('main/log_max_size', $config::LOG_MAX_SIZE));
        $this->template->set('logSizeError', t('system/settings/general/logSizeInvalid'));
        
        $this->template->set('saveButton', t('system/buttons/save'));
    }

    /**
     * Saves the general settings
     */
    private function generalSave()
    {
        if (! $this->post->validate(array(
            'name_site' => 'required',
            'site_url' => 'required|pattern:url',
            "timezone" => 'required|pattern:#^[a-zA-Z]+/[a-zA-Z]+$#',
            "template" => 'required',
            'logger' => 'required',
            'log_location' => 'type:string',
            'log_size' => 'required|type:int|min:1000'
        ))) {
            return;
        }
        
        if ($this->post['logger'] == 'default' && empty($this->post['log_location'])) {
            return;
        }
        
        $this->setValue('main/nameSite', $this->post['name_site']);
        $this->setValue('main/url', $this->post['site_url']);
        $this->setValue('main/base', $this->post['site_base']);
        $this->setValue('main/timeZone', $this->post['timezone']);
        $this->setValue('templates/dir', $this->post['template']);
        $this->setValue('main/logs', $this->post['logger']);
        $this->setValue('main/log_location', str_replace(DATA_DIR, '', $this->post['log_location']));
        $this->setValue('main/log_max_size', $this->post['log_size']);
        
        $this->settings->save();
    }

    /**
     * Displays the SSL setting
     */
    private function ssl()
    {
        /* Check if the host is ssl capable */
        if (file_get_contents('https://' . $_SERVER['HTTP_HOST'])) {
            $bo_ssl = true;
        } else {
            $bo_ssl = false;
        }
        
        $i_currentSSL = $this->getValue('main/ssl', \Settings::SSL_DISABLED);
        
        $s_loginSslValue = '';
        $s_alwaysSslValue = '';
        if (! $bo_ssl) {
            $s_loginSslValue = 'disabled="disabled" ';
            $s_alwaysSslValue = 'disabled="disabled" ';
        }
        
        if ($i_currentSSL == \Settings::SSL_DISABLED) {
            $this->template->set('no_ssl_value', 'checked="checked"');
        } else 
            if ($i_currentSSL == \Settings::SSL_LOGIN) {
                $s_loginSslValue .= 'checked="checked"';
            } else {
                $s_alwaysSslValue .= 'checked="checked"';
            }
        
        $this->template->set('login_ssl_value', $s_loginSslValue);
        $this->template->set('always_ssl_value', $s_alwaysSslValue);
        $this->template->set('current_ssl', $i_currentSSL);
        
        $this->template->set('no_ssl', \Settings::SSL_DISABLED);
        $this->template->set('login_ssl', \Settings::SSL_LOGIN);
        $this->template->set('always_ssl', \Settings::SSL_ALL);
        
        $this->template->set('sslTitle', t('system/settings/ssl/title'));
        $this->template->set('noSslText', t('system/settings/ssl/no_ssl'));
        $this->template->set('loginSslText', t('system/settings/ssl/login_ssl'));
        $this->template->set('alwaysSslText', t('system/settings/ssl/always_ssl'));
        $this->template->set('saveButton', t('system/buttons/save'));
    }

    /**
     * Saves the SSL setting
     */
    private function sslSave()
    {
        if (! isset($this->post['ssl']) || ! in_array($this->post['ssl'], array(
            \Settings::SSL_DISABLED,
            \Settings::SSL_LOGIN,
            \Settings::SSL_ALL
        ))) {
            return;
        }
        
        /* Check if the host is ssl capable */
        if (file_get_contents('https://' . $_SERVER['HTTP_HOST'])) {
            $bo_ssl = true;
        } else {
            $bo_ssl = false;
        }
        
        if (! $bo_ssl && $this->post['ssl'] != \Settings::SSL_DISABLED) {
            return;
        }
        
        $this->setValue('main/ssl', $this->post['ssl']);
        $this->settings->save();
    }
}