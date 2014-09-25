<?php 

namespace core\services;

/**
 * Maintenance service for maintaining the website
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 		2014,2015,2016  Rachelle Scheijen
 * @author    		Rachelle Scheijen
 * @version       1.0
 * @since         1.0
 * @date          12/01/2006
 * @changed   		30/03/2014
 *
 * Scripthulp framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Scripthulp framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Scripthulp framework.  If not, see <http://www.gnu.org/licenses/>.
 */
class Maintenance extends Service {
	private $service_File;
	private $service_QueryBuilder;
  private $model_Stats;
	private $s_styleDir;
	private $a_cssAdmin;
	private $a_jsAdmin;
	private $s_cssPrint;

	/**
	 * PHP 5 constructor
   * 
   * @param core\services\File          $service_File         The file service
   * @param core\services\QueryBuilder  $service_QueryBuilder The query builder
   * @param core\services\Template      $service_Template     The template service
   * @param core\models\Stats           $model_Stats          The statistics model
	 */
	public function __construct(\core\services\File $service_File, \core\services\QueryBuilder $service_QueryBuilder, 
    \core\services\Template $service_Template,\core\models\Stats $model_Stats){
		$this->service_File	= $service_File;
		$this->service_QueryBuilder	= $service_QueryBuilder->createBuilder();
    $this->model_Stats  = $model_Stats;
		$this->s_styleDir	= $service_Template->getStylesDir();
	}

	/**
	 * Compresses the CSS files into one file
	 */
	public function compressCSS(){		
		$this->a_cssAdmin	= array();
		$this->s_cssPrint	= '';

		try {
			/* Get css files */
			$a_css  = $this->service_File->readDirectory($this->s_styleDir.'css',true);

			/* Join files */
			$s_file = trim($this->joinCssDir($a_css));
			if( !empty($s_file) ){
				/* Save general CSS file */
				$this->service_File->writeFile($this->s_styleDir.'css/cssPage.css',$s_file);
			}
				
			/* General CSS print file */
			if( !empty($this->s_cssPrint) ){
				$this->service_File->writeFile($this->s_styleDir.'css/cssPage_print.css',$this->s_cssPrint);
			}
				
			$s_file	= trim($this->joinCssDir($this->a_cssAdmin,false));
			if( !empty($s_file) ){
				/* Save Admin CSS file */
				$this->service_File->writeFile($this->s_styleDir.'css/admin/cssAdmin.css',$s_file);
			}
				
			return 1;
		}
		catch(\Exception $e){
			\core\Memory::services('ErrorHandler')->error($e);
			return 0;
		}
	}

	/**
	 * Joins the given CSS directory
	 * 
	 * @param array		$a_css				The files
	 * @param boolean	$bo_ignoreAdmin		Set to false to include de admin dir
	 */
	private function joinCssDir($a_css,$bo_ignoreAdmin = true){
		$s_file = '';
		foreach($a_css AS $s_css ){
			if( is_array($s_css) ){
				$s_file .= $this->joinCssDir($s_css);
				continue;
			}

			if( $bo_ignoreAdmin && stripos($s_css,'css/admin') !== false ){
				$this->a_cssAdmin[] = $s_css;
				continue;
			}

			$s_title    = str_replace('../', '',$s_css);

			if( $s_title == 'css/cssPage.css' || $s_title == 'css/cssPage_print.css' || $s_title == 'css/admin/cssAdmin.css' || $s_title == 'css/stijl.css' ){
				continue;
			}

			$s_content  = str_replace('<!--','',$this->service_File->readFile($s_css));
			$s_content  = trim(str_replace('-->','',$s_content));

			if( stripos($s_title,'_print.css') !== false ){
				$this->s_cssPrint .= '/* '.$s_title.' */
						'.                    $s_content.'

								';
			}
			else {
				$s_file .= '/* '.$s_title.' */
						'.                    $s_content.'

								';
			}
		}

		return $s_file;
	}

	/**
	 * Compresses the javascript files into one file
	 */
	public function compressJS(){		
		$this->a_jsAdmin	= array();

		try {
			/* Get js files */
			$a_js  = $this->service_File->readDirectory(NIV.'js',true);

			/* Join files */
			$s_file = trim($this->joinJsDir($a_js));
			if( !empty($s_file) ){
				/* Save general jS file */
				$this->service_File->writeFile(NIV.'js/jsPage.js',$s_file);
			}

			$s_file	= trim($this->joinJsDir($this->a_jsAdmin,false));
			if( !empty($s_file) ){
				/* Save Admin JS file */
				$this->service_File->writeFile(NIV.'js/admin/jsAdmin.js',$s_file);
			}

			return 1;
		}
		catch(\Exception $e){
			\core\Memory::services('ErrorHandler')->error($e);
			return 0;
		}
	}

	/**
	 * Joins the given javascript directory
	 * 
	 * @param array		$a_js				The files
	 * @param boolean	$bo_ignoreAdmin		Set to false to include de admin dir
	 */
	private function joinJsDir($a_js,$bo_ignoreAdmin = true){
		$s_file = '';
		foreach($a_js AS $s_js ){
			if( is_array($s_js) ){
				$s_file .= $this->joinJsDir($s_js);
				continue;
			}
			if( $bo_ignoreAdmin && stripos($s_js,'js/admin') !== false ){
				$this->a_jsAdmin[] = $s_js;
				continue;
			}

			$s_title    = str_replace('../', '',$s_js);

			if( $s_title == 'js/general.css' || $s_title == 'js/jsPage.js' || $s_title == 'js/admin/jsAdmin.js' || strpos($s_title,'.php') !== false ){
				continue;
			}

			$s_content  = str_replace('<!--','',$this->service_File->readFile($s_js));
			$s_content  = trim(str_replace('-->','',$s_content));

			$s_file .= '/* '.$s_title.' */
					'.                    $s_content.'

							';
		}

		return $s_file;
	}

	/**
	 * Optimizes the database tables
	 */
	public function optimizeDatabase(){
		$a_tables	= $this->getTables();
		$i_registrated	= time() - 172800; //2 days ago
		$i_pm			= time() - 2592000; //30 days ago

		try {
			$this->service_QueryBuilder->delete('users')->getWhere()->addAnd(array('registrated','active'),array('i','s'),array($i_registrated,'0'),array('<','='));
			$this->service_QueryBuilder->getResult();
			
			$this->service_QueryBuilder->delete('pm')->getWhere()->addAnd('send','i',$i_pm,'<');
			$this->service_QueryBuilder->getResult();
			
			$service_Database	= $this->service_QueryBuilder->getDatabase();
				
			foreach($a_tables AS $a_table){
				$bo_status = $service_Database->optimize($a_table[0]);

				if( !$bo_status ){
					/* Try repair table */
					$service_Database->repair($a_table[0]);
					$service_Database->optimize($a_table[0]);
				}
			}
			
			return 1;
		}
		catch(\DBException $e){
			\core\Memory::services('ErrorHandler')->error($e);
			return 0;
		}
	}

	/**
	 * Checks the database tables and auto repairs
	 */
	public function checkDatabase(){
		$a_tables	= $this->getTables();

		$service_Database	= $this->service_QueryBuilder->getDatabase();
		
		try {
			foreach($a_tables AS $a_table){
				$bo_status = $service_Database->analyse($a_table[0]);

				if( !$bo_status ){
					/* Try repair table */
					$service_Database->repair($a_table[0]);
				}
			}
			
			return 1;
		}
		catch(\DBException $e){
			\core\Memory::services('ErrorHandler')->error($e);
			return 0;
		}
	}

	/**
	 * Returns the table names in the current database
	 *
	 * @return array	The table names
	 */
	private function getTables(){
		$this->service_QueryBuilder->showTables();
		
		$a_tables	= $this->service_QueryBuilder->getResult()->fetch_row();
		return $a_tables;
	}

	/**
	 * Cleans the stats from a year old
	 */
	public function cleanStatsYear(){
		$this->model_Stats->cleanStatsYear();
	}

	/**
	 * Cleans the stats from a month old
	 */
	public function cleanStatsMonth(){
		$this->model_Stats->cleanStatsMonth();
	}

	/**
	 * Cleans the old logs
	 */
	public function cleanLogs(){
		$this->model_Stats->clean();
	}
}
?>