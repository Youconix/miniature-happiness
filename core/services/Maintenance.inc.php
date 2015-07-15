<?php

namespace core\services;

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
 * Maintenance service for maintaining the website
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @version 1.0
 * @since 1.0
 */
class Maintenance extends Service {
	private $service_File;
	/**
	 * 
	 * @var \Builder
	 */
	private $builder;
	/**
	 * 
	 * @var \Config
	 */
	private $config;
	private $model_Stats;
	/**
	 * 
	 * @var \Logger
	 */
	private $logger;
	private $s_styleDir;
	private $a_cssAdmin;
	private $a_jsAdmin;
	private $s_cssPrint;
	
	/**
	 * PHP 5 constructor
	 *
	 * @param core\services\File $service_File
	 *        	The file service
	 * @param Builder $builder
	 *        	query builder
	 * @param core\models\Stats $model_Stats
	 *        	The statistics model
	 * @param \Config $config
	 *        	The config
	 * @param \Logger $logger
	 *        	The logger
	 */
	public function __construct(\core\services\File $service_File, \Builder $builder, \core\models\Stats $model_Stats, \Config $config, \Logger $logger) {
		$this->service_File = $service_File;
		$this->builder = $builder;
		$this->model_Stats = $model_Stats;
		$this->s_styleDir = $config->getStylesDir ();
		$this->config = $config;
		$this->logger = $logger;
	}
	
	/**
	 * Compresses the CSS files into one file
	 */
	public function compressCSS() {
		$this->a_cssAdmin = array ();
		$this->s_cssPrint = '';
		
		try {
			/* Get css files */
			$a_css = $this->service_File->readDirectory ( $this->s_styleDir . 'css', true );
			
			/* Join files */
			$s_file = trim ( $this->joinCssDir ( $a_css ) );
			if (! empty ( $s_file )) {
				/* Save general CSS file */
				$this->service_File->writeFile ( $this->s_styleDir . 'css/cssPage.css', $s_file );
			}
			
			/* General CSS print file */
			if (! empty ( $this->s_cssPrint )) {
				$this->service_File->writeFile ( $this->s_styleDir . 'css/cssPage_print.css', $this->s_cssPrint );
			}
			
			$s_file = trim ( $this->joinCssDir ( $this->a_cssAdmin, false ) );
			if (! empty ( $s_file )) {
				/* Save Admin CSS file */
				$this->service_File->writeFile ( $this->s_styleDir . 'css/admin/cssAdmin.css', $s_file );
			}
			
			return 1;
		} catch ( \Exception $e ) {
			\core\Memory::services ( 'ErrorHandler' )->error ( $e );
			return 0;
		}
	}
	
	/**
	 * Joins the given CSS directory
	 *
	 * @param array $a_css        	
	 * @param boolean $bo_ignoreAdmin
	 *        	false to include de admin dir
	 */
	private function joinCssDir($a_css, $bo_ignoreAdmin = true) {
		$s_file = '';
		foreach ( $a_css as $s_css ) {
			if (is_array ( $s_css )) {
				$s_file .= $this->joinCssDir ( $s_css );
				continue;
			}
			
			if ($bo_ignoreAdmin && stripos ( $s_css, 'css/admin' ) !== false) {
				$this->a_cssAdmin [] = $s_css;
				continue;
			}
			
			$s_title = str_replace ( '../', '', $s_css );
			
			if ($s_title == 'css/cssPage.css' || $s_title == 'css/cssPage_print.css' || $s_title == 'css/admin/cssAdmin.css' || $s_title == 'css/stijl.css') {
				continue;
			}
			
			$s_content = str_replace ( '<!--', '', $this->service_File->readFile ( $s_css ) );
			$s_content = trim ( str_replace ( '-->', '', $s_content ) );
			
			if (stripos ( $s_title, '_print.css' ) !== false) {
				$this->s_cssPrint .= '/* ' . $s_title . ' */
						' . $s_content . '

								';
			} else {
				$s_file .= '/* ' . $s_title . ' */
						' . $s_content . '

								';
			}
		}
		
		return $s_file;
	}
	
	/**
	 * Compresses the javascript files into one file
	 */
	public function compressJS() {
		$this->a_jsAdmin = array ();
		
		try {
			/* Get js files */
			$a_js = $this->service_File->readDirectory ( NIV . 'js', true );
			
			/* Join files */
			$s_file = trim ( $this->joinJsDir ( $a_js ) );
			if (! empty ( $s_file )) {
				/* Save general jS file */
				$this->service_File->writeFile ( NIV . 'js/jsPage.js', $s_file );
			}
			
			$s_file = trim ( $this->joinJsDir ( $this->a_jsAdmin, false ) );
			if (! empty ( $s_file )) {
				/* Save Admin JS file */
				$this->service_File->writeFile ( NIV . 'js/admin/jsAdmin.js', $s_file );
			}
			
			return 1;
		} catch ( \Exception $e ) {
			\core\Memory::services ( 'ErrorHandler' )->error ( $e );
			return 0;
		}
	}
	
	/**
	 * Joins the given javascript directory
	 *
	 * @param array $a_js        	
	 * @param boolean $bo_ignoreAdmin
	 *        	false to include de admin dir
	 */
	private function joinJsDir($a_js, $bo_ignoreAdmin = true) {
		$s_file = '';
		foreach ( $a_js as $s_js ) {
			if (is_array ( $s_js )) {
				$s_file .= $this->joinJsDir ( $s_js );
				continue;
			}
			if ($bo_ignoreAdmin && stripos ( $s_js, 'js/admin' ) !== false) {
				$this->a_jsAdmin [] = $s_js;
				continue;
			}
			
			$s_title = str_replace ( '../', '', $s_js );
			
			if ($s_title == 'js/general.css' || $s_title == 'js/jsPage.js' || $s_title == 'js/admin/jsAdmin.js' || strpos ( $s_title, '.php' ) !== false) {
				continue;
			}
			
			$s_content = str_replace ( '<!--', '', $this->service_File->readFile ( $s_js ) );
			$s_content = trim ( str_replace ( '-->', '', $s_content ) );
			
			$s_file .= '/* ' . $s_title . ' */
					' . $s_content . '

							';
		}
		
		return $s_file;
	}
	
	/**
	 * Optimizes the database tables
	 */
	public function optimizeDatabase() {
		$a_tables = $this->getTables ();
		$i_registrated = time () - 172800; // 2 days ago
		$i_pm = time () - 2592000; // 30 days ago
		
		try {
			$this->builder->delete ( 'users' )->getWhere ()->addAnd ( array (
					'registrated',
					'active' 
			), array (
					'i',
					's' 
			), array (
					$i_registrated,
					'0' 
			), array (
					'<',
					'=' 
			) );
			$this->builder->getResult ();
			
			$this->builder->delete ( 'pm' )->getWhere ()->addAnd ( 'send', 'i', $i_pm, '<' );
			$this->builder->getResult ();
			
			$service_Database = $this->builder->getDatabase ();
			
			foreach ( $a_tables as $a_table ) {
				$bo_status = $service_Database->optimize ( $a_table [0] );
				
				if (! $bo_status) {
					/* Try repair table */
					$service_Database->repair ( $a_table [0] );
					$service_Database->optimize ( $a_table [0] );
				}
			}
			
			return 1;
		} catch ( \DBException $e ) {
			reportException( $e );
			
			return 0;
		}
	}
	
	/**
	 * Checks the database tables and auto repairs
	 */
	public function checkDatabase() {
		$a_tables = $this->getTables ();
		
		$service_Database = $this->builder->getDatabase ();
		
		try {
			foreach ( $a_tables as $a_table ) {
				$bo_status = $service_Database->analyse ( $a_table [0] );
				
				if (! $bo_status) {
					/* Try repair table */
					$service_Database->repair ( $a_table [0] );
				}
			}
			
			return 1;
		} catch ( \DBException $e ) {
			reportException( $e );
			return 0;
		}
	}
	
	/**
	 * Returns the table names in the current database
	 *
	 * @return array table names
	 */
	private function getTables() {
		$this->builder->showTables ();
		
		$a_tables = $this->builder->getResult ()->fetch_row ();
		return $a_tables;
	}
	
	/**
	 * Cleans the stats from a year old
	 */
	public function cleanStatsYear() {
		$this->model_Stats->cleanStatsYear ();
	}
	
	/**
	 * Cleans the stats from a month old
	 */
	public function cleanStatsMonth() {
		$this->model_Stats->cleanStatsMonth ();
	}
	public function createBackupFull() {
		if (! $this->isZipSupported ()) {
			$this->logger->critical ( 'Can not create backup. Zip support is missing' );
			return null;
		}
		
		$s_temp = DATA_DIR . DIRECTORY_SEPARATOR . 'backups' . DIRECTORY_SEPARATOR;
		$s_filename = $s_temp . 'backup ' . date ( 'd-m-Y H:i' ) . 'zip';
		$obj_zip = new \ZipArchive ();
		
		if ($obj_zip->open ( $s_filename . \ZipArchive::CREATE ) !== true) {
			$this->logger->critical ( 'Can not create zip archive in directory ' . $s_temp . '.' );
			return;
		}
		
		$obj_zip->setArchiveComment ( 'Backup created by Miniature-happiness on ' . date ( 'd-m-Y H:i:s' ) . '.' );
		
		/* Add database */
		$obj_zip->addFromString ( 'database.sql', $this->backupDatabase () );
		
		$this->addDirectory ( $obj_zip, NIV, '' );
		
		$obj_zip->close ();
		
		return $s_temp . $s_filename;
	}
	protected function addDirectory($obj_zip, $s_directory, $s_pre) {
		if ($s_pre != '') {
			$s_pre .= DIRECTORY_SEPARATOR;
		}
		
		$a_files = $this->service_File->readDirectory ();
		foreach ( $a_files as $s_file ) {
			if (substr ( $s_file, 0, 1 ) == '.' && $s_file != '.htaccess') {
				continue;
			}
			
			if (is_dir ( $s_file )) {
				$obj_zip->addEmptyDir ( $s_pre . $s_file );
				$obj_zip = $this->addDirectory ( $obj_zip, $s_directory . DIRECTORY_SEPARATOR . $s_file, $s_pre . $s_file );
			} else {
				$obj_zip->addFile ( $s_directory . DIRECTORY_SEPARATOR, $s_pre . $s_file );
			}
		}
		
		return $obj_zip;
	}
	public function createBackup() {
		if (! $this->isZipSupported ()) {
			$this->logger->critical ( 'Can not create backup. Zip support is missing' );
			return null;
		}
		
		$s_temp = DATA_DIR . DIRECTORY_SEPARATOR . 'backups' . DIRECTORY_SEPARATOR;
		$s_filename = $s_temp . 'backup ' . date ( 'd-m-Y H:i' ) . 'zip';
		$obj_zip = new \ZipArchive ();
		
		if ($obj_zip->open ( $s_filename . \ZipArchive::CREATE ) !== true) {
			$this->logger->critical ( 'Can not create zip archive in directory ' . $s_temp . '.' );
			return;
		}
		
		$obj_zip->setArchiveComment ( 'Backup created by Miniature-happiness on ' . date ( 'd-m-Y H:i:s' ) . '.' );
		
		/* Add database */
		$obj_zip->addFromString ( 'database.sql', $this->backupDatabase () );
		
		/* Add files */
		$obj_zip->addEmptyDir ( 'settings' );
		$a_files = $this->service_File ( DATA_DIR . 'settings' );
		foreach ( $a_files as $s_file ) {
			if ($s_file == '.' || $s_file == '.') {
				continue;
			}
			
			$obj_zip->addFile ( DATA_DIR . 'settings' . DIRECTORY_SEPARATOR . $s_file, 'settings' . DIRECTORY_SEPARATOR . $s_file );
		}
		
		$obj_zip->addEmptyDir ( 'logs' );
		$a_files = $this->service_File ( DATA_DIR . 'logs' );
		foreach ( $a_files as $s_file ) {
			if ($s_file == '.' || $s_file == '.') {
				continue;
			}
			
			$obj_zip->addFile ( DATA_DIR . 'logs' . DIRECTORY_SEPARATOR . $s_file, 'logs' . DIRECTORY_SEPARATOR . $s_file );
		}
		
		$obj_zip->close ();
		
		return $s_temp . $s_filename;
	}
	private function backupDatabase() {
		$s_sql = '-- Database dump created by Miniature-happiness on ' . date ( 'd-m-Y H:i:s' ) . ".\n\n";
		
		$s_sql .= $this->builder->dumpDatabase ();
		
		return $s_sql;
	}
	public function restoreBackup($s_backup) {
		if (! $this->isZipSupported ()) {
			$this->logger->critical ( 'Can not restore backup. Zip support is missing' );
			return false;
		}
	}
	protected function isZipSupported() {
		return class_exists ( 'ZipArchive' );
	}
}