<?php

namespace admin;

/**
 * Admin maintenance class
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 2012,2013,2014  Rachelle Scheijen
 * @author    Rachelle Scheijen
 * @since     1.0
 * @changed    25/09/10
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
 *
 */

define('NIV','../');

include(NIV.'include/AdminLogicClass.php');

class Maintenance extends \core\AdminLogicClass  {
	private $service_Maintenance;

	/**
	 * Starts the class Groups
	 */
	public function __construct(){
		$this->init();

                if( !Memory::isAjax() ){    exit(); }

		if( !isset($this->get['action']) && !isset($this->post['action']) ){
			$this->view();
		}
		if( isset($this->get['action']) ){
			if( $this->get['action'] == 'css' ){
				$this->performAction($this->get['action']);
			}
			else if( $this->get['action'] == 'js' ){
				$this->performAction($this->get['action']);
			}
			else if( $this->get['action'] == 'checkDatabase' ){
				$this->performAction($this->get['action']);
			}
			else if( $this->get['action'] == 'optimizeDatabase' ){
				$this->performAction($this->get['action']);
			}
		}
		else if( isset($this->post['action']) ){
			if( $this->post['action'] == 'cleanLogs' ){
				$this->performAction($this->post['action']);
			}
			else if( $this->post['action'] == 'cleanStatsYear' ){
				$this->performAction($this->post['action']);
			}
			else if( $this->post['action'] == 'cleanStatsMonth' ){
				$this->performAction($this->post['action']);
			}
		}
	}

	/**
	 * Inits the class Groups
	 */
	protected function init(){
		$this->init_get = array(
				'action'	=> 'string'
		);
		$this->init_post = array(
				'action'	=> 'string'
		);

		parent::init();

		$this->service_Maintenance	= Memory::services('Maintenance');
	}

	/**
	 * Generates the action menu
	 */
	private function view(){
		$this->service_Template->set('compressCSS',$this->service_Language->get('admin/maintenance/compressCSS'));
		$this->service_Template->set('compressJS',$this->service_Language->get('admin/maintenance/compressJS'));
		$this->service_Template->set('checkDatabase',$this->service_Language->get('admin/maintenance/checkDatabase'));
		$this->service_Template->set('optimizeDatabase',$this->service_Language->get('admin/maintenance/optimizeDatabase'));
		$this->service_Template->set('cleanLogs',$this->service_Language->get('admin/maintenance/cleanLogs'));
		$this->service_Template->set('systemUpdate',$this->service_Language->get('admin/maintenance/systemUpdate'));
		$this->service_Template->set('cleanStatsYear',$this->service_Language->get('admin/maintenance/cleanStatsYear'));
		$this->service_Template->set('cleanStatsMonth',$this->service_Language->get('admin/maintenance/cleanStatsMonth'));
		$this->service_Template->set('ready',$this->service_Language->get('admin/maintenance/ready'));
	}

	/**
	 * Performs the maintenance action
	 * 
	 * @param string $s_action	The action
	 */
	private function performAction($s_action){
		$bo_result	= false;
		 
		if( $s_action == 'css' ){
			$bo_result	= $this->service_Maintenance->compressCSS();
		}
		else if( $s_action == 'js' ){
			$bo_result	= $this->service_Maintenance->compressJS();
		}
		else if( $s_action == 'checkDatabase' ){
			$bo_result	= $this->service_Maintenance->checkDatabase();
		}
		else if( $s_action == 'optimizeDatabase' ){
			$bo_result	= $this->service_Maintenance->optimizeDatabase();
		}
		else if( $s_action == 'cleanLogs' ){
			$bo_result = $this->service_Maintenance->cleanLogs();
		}
		else if( $s_action == 'cleanStatsYear' ){
			$bo_result	= $this->service_Maintenance->cleanStatsYear();
		}
		else if( $s_action == 'cleanStatsMonth' ){
			$bo_result	= $this->service_Maintenance->cleanStatsMonth();
		}
		
		if( $bo_result){
			$this->service_Template->set('result',1);
		}
		else {
			$this->service_Template->set('result',0);
		}
	}
}

$obj_Maintenance = new Maintenance();
unset($obj_Maintenance);
