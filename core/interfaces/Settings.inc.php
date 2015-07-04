<?php
interface  Settings {
	/**
	 * Returns if the object schould be treated as singleton
	 *
	 * @return boolean True if the object is a singleton
	 */
	public static function isSingleton();
	
	/**
	 * Gives the asked part of the loaded file
	 *
	 * @param String $s_path
	 *            The path to the language-part
	 * @return String The content of the requested part
	 * @throws XMLException when the path does not exist
	 */
	public function get($s_path);
	
	/**
	 * Saves the settings file
	 */
	public function save($s_file = '');
}