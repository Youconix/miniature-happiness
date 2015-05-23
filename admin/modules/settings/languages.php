<?php
namespace admin;

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
if (! defined('NIV')) {
    define('NIV', '../../../');
}

include (NIV . 'admin/modules/settings/settings.php');

class Languages extends \admin\Settings
{

    /**
     * Calls the functions
     */
    protected function menu()
    {
        if (isset($this->get['command'])) {
            switch ($this->get['command']) {
                case 'language':
                    $this->language();
                    break;
                
                case 'install_language':
                    $this->installLanguageList();
                    break;
                
                case 'edit_language':
                    $this->editLanguage();
                    break;
                    
                case 'edit_language_form' :
                    $this->editLanguageForm();
                    break;
            }
        } else 
            if (isset($this->post['command'])) {
                switch ($this->post['command']) {
                    case 'language':
                        $this->languageSave();
                        break;
                }
            }
    }

    /**
     * Inits the class Settings
     */
    protected function init()
    {
        $this->init_get = array(
            'file' => 'string',
            'path' => 'string'
        );
        
        $this->init_post = array(
            'default_language' => 'string',
            'language' => 'string',
            'file' => 'string',
            'path' => 'string'
        );
        
        parent::init();
    }

    /**
     * Displays the languages
     */
    private function language()
    {
        $this->service_Template->set('languageTitle', t('system/admin/settings/languages/title'));
        $this->service_Template->set('defaultLanguageText', 'Standaard taal');
        
        $s_defaultLanguage = $this->getValue('defaultLanguage', 'nl_NL');
        
        $languages = $this->getInstalledLanguages();
        foreach ($languages as $language) {
            $s_filename = $language->getFilename();
            ($s_filename == $s_defaultLanguage) ? $selected = 'selected="selected"' : $selected = '';
            
            $this->service_Template->setBlock('defaultLanguage', array(
                'value' => $s_filename,
                'text' => $s_filename,
                'selected' => $selected
            ));
            
            $this->service_Template->setBlock('language', array(
                'text' => $s_filename
            ));
        }
        
        $this->service_Template->set('saveButton', t('system/buttons/save'));
    }

    /**
     * Saves the languages
     */
    private function languageSave()
    {
        if (! $this->service_Validation->validate(array(
            'default_language' => array(
                'required' => 1,
                'type' => 'string'
            )
        ), $this->post)) {
            return;
        }
        
        $this->setValue('defaultLanguage', $this->post['default_language']);
        $this->service_Settings->save();
    }

    /**
     * Displays the available languages list
     */
    private function installLanguageList()
    {
        $this->service_Template->set('languageTitle', t('system/admin/settings/languages/installLanguages'));
        
        $xml_file = @file_get_contents(\core\services\Settings::REMOTE . 'languages.xml');
        if (! $xml_file) {
            $this->downloadError();
            return;
        }
        
        $service_Xml = \Loader::Inject('\core\services\Xml');
        try {
            $service_Xml->loadXML($xml_file);
            
            if ($service_Xml->get('languages/version') != \core\services\Settings::MAJOR) {
                $this->downloadError();
                return;
            }
            
            $this->service_Template->displayPart('file_available');
            
            $installedLanguagesRaw = $this->getInstalledLanguages();
            $a_installedLanguages = array();
            foreach ($installedLanguages as $language) {
                $a_installedLanguages[] = $language->getFilename();
            }
            
            $available_languages = $service_Xml->getBlock('languages/language');
            foreach ($available_languages as $available_language) {
                $a_item = array(
                    'disabled' => ''
                );
                foreach ($available_language->childNodes as $item) {
                    $a_item[$item->tagName] = $item->nodeValue;
                }
                
                if (in_array($a_item['name'], $a_installedLanguages)) {
                    $a_item['disabled'] = 'disabled="disabled"';
                }
                
                $a_item['name'] = str_replace('-', '_', $a_item['name']);
                
                $this->service_Template->setBlock('language', $a_item);
            }
            
            $this->service_Template->set('installButton', 'Installeren');
        } catch (\IOException $e) {}
    }

    private function downloadError()
    {}

    private function getInstalledLanguages()
    {
        $languages = $this->service_FileHandler->readDirectory(NIV . 'language');
        $languages = $this->service_FileHandler->directoryFilterName($languages, array(
            '*-*|*.lang'
        ));
        
        return $languages;
    }

    private function editLanguage()
    {
        $s_currentLanguage = $this->model_Config->getLanguage();        
        $s_currentFile = 'site.lang';
        
        /* Display language files */
        
        $languages = $this->service_FileHandler->readDirectory(NIV . 'language'.DIRECTORY_SEPARATOR.$s_currentLanguage.DIRECTORY_SEPARATOR.'LC_MESSAGES');
        $languages = $this->service_FileHandler->directoryFilterName($languages, array(
            '*.lang'
        ));
        
        foreach ($languages as $language) {
            ($language->getFileName() == $s_currentFile) ? $s_selected = 'selected="selected"' : $s_selected = '';
            
            $this->service_Template->setBlock('available_languagesfiles', array(
                'value' => $language->getFilename(),
                'selected' => $s_selected,
                'text' => str_replace('.lang', '', $language->getFilename())
            ));
        }
        
        $helper_languageTree = \Loader::inject('\core\helpers\languageTree');
        $helper_languageTree->init($s_currentLanguage,$s_currentFile);
        $helper_languageTree->parse();
        $s_result = $helper_languageTree->build();        
        
        $this->service_Template->set('tree',$s_result);
    }
    
    private function editLanguageForm(){
        if( empty($this->get['file']) && empty($this->get['path']) ){
            $this->service_Headers->http400();
            $this->service_Headers->printHeaders();
            die();
        }
        
        $s_currentLanguage = $this->model_Config->getLanguage();
        $a_languages = $this->getInstalledLanguages();
        
        $a_items = array();
        
        try {
            foreach( $a_languages AS $language ){
                $service_Xml = \Loader::Inject('\core\services\Xml');
                $service_Xml->load(NIV . 'language'.DIRECTORY_SEPARATOR.$language->getFileName().DIRECTORY_SEPARATOR.'LC_MESSAGES'.DIRECTORY_SEPARATOR.$this->get['file']);
                
                $a_items[ $language->getFileName() ] = $service_Xml->get($this->get['path']);
            }
        }
        catch(\XMLException $e){
            $this->service_Headers->http400();
            $this->service_Headers->printHeaders();
            die();
        }
        
        $this->service_Template->set('languageTitle',t('system/admin/settings/languages/editLanguages'));
        $this->service_Template->set('file',$this->get['file']);
        $this->service_Template->set('path',$this->get['path']);
                
        $i=1;
        foreach($a_items AS $language => $text ){
            $this->service_Template->setBlock('languageItem',array('nr'=>$i,'text'=>$text,'languageName'=>$this->service_Language->getLanguageText($language)));
            $i++;
        }
    }
}

$obj_Languages = new Languages();
unset($obj_Languages);