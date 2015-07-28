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
class Languages extends \admin\modules\settings\Settings {
	
	/**
	 *
	 * @var \core\services\FileHandler
	 */
	private $fileHandler;
	
	/**
	 *
	 * @var \Headers
	 */
	private $headers;
	
	/**
	 *
	 * @var \core\services\Xml
	 */
	private $xml;
	
	/**
	 *
	 * @var \core\helpers\languageTree
	 */
	private $languageTree;
	
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
	 * @param \Headers $headers        	
	 * @param \core\services\Xml $xml        	
	 * @param \core\helpers\languageTree $languageTree        	
	 */
	public function __construct(\Input $Input, \Config $config, \Language $language, \Output $template, \Logger $logs, \Settings $settings, \core\services\FileHandler $fileHandler, \Headers $headers, \core\services\Xml $xml, \core\helpers\languageTree $languageTree) {
		parent::__construct ( $Input, $config, $language, $template, $logs, $settings );
		
		$this->fileHandler = $fileHandler;
		$this->headers = $headers;
		$this->xml = $xml;
		$this->languageTree = $languageTree;
	}
	
	/**
	 * Routes the controller
	 *
	 * @see Routable::route()
	 */
	public function route($s_command) {
		if ($_SERVER ['REQUEST_METHOD'] != 'POST') {
			switch ($s_command) {
				case 'language' :
					$this->language ();
					break;
				
				case 'install_language' :
					$this->installLanguageList ();
					break;
				
				case 'edit_language' :
					$this->editLanguage ();
					break;
				
				case 'edit_language_form' :
					$this->editLanguageForm ();
					break;
			}
		} else {
			switch ($s_command) {
				case 'language' :
					$this->languageSave ();
					break;
				
				case 'edit_language_save' :
					$this->edit_language_save();
					break;
			}
		}
	}
	
	/**
	 * Inits the class Settings
	 */
	protected function init() {
		$this->init_get = array (
				'file' => 'string',
				'path' => 'string' 
		);
		
		$this->init_post = array (
				'default_language' => 'string',
				'language' => 'string',
				'file' => 'string',
				'path' => 'string' ,
				'data' => 'string-array'
		);
		
		parent::init ();
	}
	
	/**
	 * Displays the languages
	 */
	private function language() {
		$this->template->set ( 'languageTitle', t ( 'system/settings/languages/title' ) );
		$this->template->set ( 'defaultLanguageText', 'Standaard taal' );
		
		$s_defaultLanguage = $this->getValue ( 'defaultLanguage', 'nl_NL' );
		
		$languages = $this->getInstalledLanguages ();
		foreach ( $languages as $language ) {
			$s_filename = $language->getFilename ();
			($s_filename == $s_defaultLanguage) ? $selected = 'selected="selected"' : $selected = '';
			
			$this->template->setBlock ( 'defaultLanguage', array (
					'value' => $s_filename,
					'text' => $s_filename,
					'selected' => $selected 
			) );
			
			$this->template->setBlock ( 'language', array (
					'text' => $s_filename 
			) );
		}
		
		$this->template->set ( 'saveButton', t ( 'system/buttons/save' ) );
	}
	
	/**
	 * Saves the languages
	 */
	private function languageSave() {
		if (! $this->service_Validation->validate ( array (
				'default_language' => array (
						'required' => 1,
						'type' => 'string' 
				) 
		), $this->post )) {
			return;
		}
		
		$this->setValue ( 'defaultLanguage', $this->post ['default_language'] );
		$this->settings->save ();
	}
	
	/**
	 * Displays the available languages list
	 */
	private function installLanguageList() {
		$this->template->set ( 'languageTitle', t ( 'system/settings/languages/installLanguages' ) );
		
		$xml_file = @file_get_contents ( \core\services\Settings::REMOTE . 'languages.xml' );
		if (! $xml_file) {
			$this->downloadError ();
			return;
		}
		
		$service_Xml = $this->xml;
		try {
			$service_Xml->loadXML ( $xml_file );
			
			if ($service_Xml->get ( 'languages/version' ) != \core\services\Settings::MAJOR) {
				$this->downloadError ();
				return;
			}
			
			$this->template->displayPart ( 'file_available' );
			
			$installedLanguagesRaw = $this->getInstalledLanguages ();
			$a_installedLanguages = array ();
			foreach ( $installedLanguages as $language ) {
				$a_installedLanguages [] = $language->getFilename ();
			}
			
			$available_languages = $service_Xml->getBlock ( 'languages/language' );
			foreach ( $available_languages as $available_language ) {
				$a_item = array (
						'disabled' => '' 
				);
				foreach ( $available_language->childNodes as $item ) {
					$a_item [$item->tagName] = $item->nodeValue;
				}
				
				if (in_array ( $a_item ['name'], $a_installedLanguages )) {
					$a_item ['disabled'] = 'disabled="disabled"';
				}
				
				$a_item ['name'] = str_replace ( '-', '_', $a_item ['name'] );
				
				$this->template->setBlock ( 'language', $a_item );
			}
			
			$this->template->set ( 'installButton', 'Installeren' );
		} catch ( \IOException $e ) {
		}
	}
	private function downloadError() {
	}
	/**
	 * Returns the current installed languages
	 * 
	 * @return \DirectoryIterator
	 */
	private function getInstalledLanguages() {
		$languages = $this->fileHandler->readDirectory ( NIV . 'language' );
		$languages = $this->fileHandler->directoryFilterName ( $languages, array (
				'*-*|*.lang' 
		) );
		
		return $languages;
	}
	/**
	 * Shows the language content tree for editing
	 */
	private function editLanguage() {
		$s_currentLanguage = $this->config->getLanguage ();
		$s_currentFile = 'site.lang';
		if( $this->get->has('file') ){
			$s_currentFile = $this->get->get('file');
		}
		
		/* Display language files */
		$languages = $this->fileHandler->readDirectory ( NIV . 'language' . DS . $s_currentLanguage . DS . 'LC_MESSAGES' );
		$languages = $this->fileHandler->directoryFilterName ( $languages, array (
				'*.lang' 
		) );
		
		foreach ( $languages as $language ) {
			($language->getFileName () == $s_currentFile) ? $s_selected = 'selected="selected"' : $s_selected = '';
			
			$this->template->setBlock ( 'available_languagesfiles', array (
					'value' => $language->getFilename (),
					'selected' => $s_selected,
					'text' => str_replace ( '.lang', '', $language->getFilename () ) 
			) );
		}
		
		$this->languageTree->init ( $s_currentLanguage, $s_currentFile );
		$this->languageTree->parse ();
		$s_result = $this->languageTree->build ();
		
		$this->template->set ( 'tree', $s_result );
		$this->template->set('fileText',t('system/settings/languages/file'));
	}
	/**
	 * Shows the language text editors
	 */
	private function editLanguageForm() {
		if (! $this->get->validate ( array (
				'file' => 'required',
				'path' => 'required' 
		) )) {
			$this->headers->http400 ();
			$this->headers->printHeaders ();
			die ();
		}
		
		$s_currentLanguage = $this->config->getLanguage ();
		$a_languages = $this->getInstalledLanguages ();
		
		$a_items = array ();
		
		try {
			foreach ( $a_languages as $language ) {
				$service_Xml = $this->xml;
				$service_Xml->load ( NIV . 'language' . DS . $language->getFileName () . DS . 'LC_MESSAGES' . DS . $this->get->get('file') );
				
				$a_items [$language->getFileName ()] = $service_Xml->get ( $this->get ['path'] );
			}
		} catch ( \XMLException $e ) {
			$this->headers->http400 ();
			$this->headers->printHeaders ();
			die ();
		}
		
		$this->template->set ( 'languageTitle', t ( 'system/settings/languages/editLanguages' ) );
		$this->template->set ( 'file', $this->get->get('file') );
		$this->template->set ( 'path', $this->get->get('path') );
		
		$i = 1;
		foreach ( $a_items as $language => $text ) {
			$this->template->setBlock ( 'languageItemHeader', array (
					'nr' => $i,
					'languageName' => $this->language->getLanguageText ( $language ) 
			) );
			$this->template->setBlock ( 'languageItem', array (
					'nr' => $i,
					'text' => $text ,
					'languageName' => $language
			) );
			$i ++;
		}
		
		$this->template->set('buttonBack',t('system/buttons/back'));
		$this->template->set('save',t('system/buttons/save'));
	}
	
	/**
	 * Saves the changed language text
	 */
	private function edit_language_save(){
		$a_languages = $this->getInstalledLanguages ();
		$a_data = $this->post->get('data');
		$s_path = $this->post->get('path');
		
		try {
			foreach ( $a_languages as $language ) {
				if( !array_key_exists($language->getFileName (), $a_data) ){
					continue;
				}
				
				$s_filename = NIV . 'language' . DS . $language->getFileName () . DS . 'LC_MESSAGES' . DS . $this->post->get('file');
				
				$service_Xml = $this->xml;
				$service_Xml->load ( $s_filename );
				
				$service_Xml->set($s_path, $a_data[$language->getFileName ()]);
				$service_Xml->save($s_filename);
			}
		} catch ( \XMLException $e ) {
			$this->headers->http400 ();
			$this->headers->printHeaders ();
			die ();
		}
	}
}