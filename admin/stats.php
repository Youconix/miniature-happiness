<?php
/**
 * Admin statistics view class
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 2012,2013,2014  Rachelle Scheijen
 * @author    Rachelle Scheijen
 * @since     1.0
 * @changed    11/12/10
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

define('NIV','../');
include(NIV.'include/AdminLogicClass.php');

class Stats extends AdminLogicClass  {
	private $model_Stats;
	private $i_date;
	private $i_daysMonth;
	private $i_month;
	private $i_year;

	/**
	 * Starts the class Stats
	 */
	public function __construct(){
		$this->init();

		if( !Memory::isAjax() || !isset($this->get['command']) )
		exit();

		if( $this->get['command'] == 'index' ){
			$this->view();
		}
		else if( $this->get['command'] == 'OS' ){
			$this->OSLong();
		}
		else if( $this->get['command'] == 'sizes' ){
			$this->screenSizes(-1);
		}
		else if( $this->get['command'] == 'browsers' ){
			$this->browsersLong();
		}

		if( $this->get['command'] != 'index' ){
			$this->service_Template->set('back',$this->service_Language->get('language/buttons/back'));
		}
	}

	/**
	 * Inits the class Stats
	 */
	protected function init(){
		$this->init_get	= array('month'	=> 'int','year'	=> 'int');
		 
		parent::init();

		if( isset($this->get['month']) && $this->get['month'] >= 1 && $this->get['month'] <= 12 )
		$this->i_month	= $this->get['month'];
		else
		$this->i_month			= date('n');

		$this->i_year		= date('Y');
		if( isset($this->get['year']) && $this->get['year'] >= ($this->i_year-3) && $this->get['year'] <= $this->i_year )
		$this->i_year	= $this->get['year'];

		$this->model_Stats	= Memory::models('Stats');
		$this->i_date		= mktime(0,0,0,$this->i_month,1,$this->i_year);

		$this->i_daysMonth	= Memory::helpers('Date')->getDaysMonth($this->i_month,$this->i_year);
		$this->service_Template->set('amount',$this->service_Language->get('language/admin/stats/amount'));
		$this->service_Template->set('pageTitle',$this->service_Language->get('language/admin/stats/title'));

		$this->service_Template->set('month',$this->i_month);
		$this->service_Template->set('year',$this->i_year);
	}

	/**
	 * Displays the main view
	 */
	private function view(){
		$this->hits();
		$this->visitors();
		$this->os(10);
		$this->browsers();
		$this->screenColors(10);
		$this->screenSizes(10);
		$this->reference();
		$this->pages();

		$this->service_Template->set('fullList',$this->service_Language->get('language/admin/stats/fullList'));

		$i_yearLast	= $this->i_year;
		$i_yearNext	= $this->i_year;
		$i_monthLast	= $this->i_month-1;
		$i_monthNext	= $this->i_month+1;
		if( $i_monthLast == 0 ){
			$i_monthLast	= 12;
			$i_yearLast--;
		}
		else if( $i_monthNext == 13 ){
			$i_monthNext	= 1;
			$i_yearNext++;
		}

		if( $i_yearLast >= (date('Y')-3) ){
			$this->service_Template->set('lastMonth','<a href="javascript:adminStats.view2('.$i_monthLast.','.$i_yearLast.')">&lt;&lt</a>');
		}
		if( $i_yearNext <= date('Y') ){
			$this->service_Template->set('nextMonth','<a href="javascript:adminStats.view2('.$i_monthNext.','.$i_yearNext.')">&gt;&gt</a>');
		}
	}

	/**
	 * Displays the hits
	 */
	private function hits(){
		$a_hits		= $this->model_Stats->getHits($this->i_date);
		$a_hitHours	= array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$i_hitsTotal = 0;

		for($i=1; $i<=$this->i_daysMonth; $i++){
			$this->service_Template->setBlock('hitsDay',array('day'=>$i));

			$i_number	= 0;
			if( array_key_exists($i, $a_hits) ){
				for($j=0; $j<=23; $j++){
					if( array_key_exists($j, $a_hits[$i]) ){
						$i_amount	= $a_hits[$i][$j]['amount'];

						$a_hitHours[$j] += $i_amount;
						$i_number	+= $i_amount;
						$i_hitsTotal += $i_amount;
					}
				}

			}

			$this->service_Template->setBlock('hitsNumber',array('number'=>$this->format($i_number)) );
		}

		for($i=0; $i<=23; $i++){
			$i_perc  = 0;
			if( $i_hitsTotal > 0 )	$i_perc	 = round($a_hitHours[$i]/$i_hitsTotal*100);

			$this->service_Template->setBlock('visitorsHour',array('hour'=>$i,
					'number'=>$this->format($a_hitHours[$i]),'width'=>$this->format($i_perc*4),'percent'=>$this->format($i_perc)) );
		}

		$this->service_Template->set('HitsTitle',$this->service_Language->get('language/admin/stats/visitors').' '.strtolower($this->service_Language->get('language/months/month'.$this->i_month)).' '.$this->i_year,$this->i_date);
	}

	/**
	 * Displays the unique visitors
	 */
	private function visitors(){
		$a_visitors	= $this->model_Stats->getUnique($this->i_date);

		for($i=1; $i<=$this->i_daysMonth; $i++){
			$this->service_Template->setBlock('visitorsDay',array('day'=>$i));

			$i_number	= 0;
			if( array_key_exists($i, $a_visitors) )	$i_number	= $a_visitors[$i]['amount'];

			$this->service_Template->setBlock('visitorsNumber',array('number'=>$this->format($i_number)) );
		}

		$this->service_Template->set('HitsUniqueTitle',$this->service_Language->get('language/admin/stats/uniqueVisitors').' '.strtolower($this->service_Language->get('language/months/month'.$this->i_month)).' '.$this->i_year,$this->i_date);
	}

	/**
	 * Displays the operating systems
	 */
	private function OS(){
		$a_osses	= $this->model_Stats->getOS($this->i_date);

		foreach($a_osses AS $a_os){
			$this->service_Template->setBlock('OS',array('name'=>$a_os['name'],'number'=>$this->format($a_os['amount'])));
		}

		$this->service_Template->set('operatingTitle',$this->service_Language->get('language/admin/stats/OSses'));
		$this->service_Template->set('osTitle',$this->service_Language->get('language/admin/stats/OS'));
	}

	/**
	 * Displays the detailed operating systems
	 */
	private function OSLong(){
		$a_osses	= $this->model_Stats->getOSLong($this->i_date);

		foreach($a_osses AS $a_os){
			$this->service_Template->setBlock('OS',array('name'=>$a_os['name'],'number'=>$this->format($a_os['amount'])));
		}

		$this->service_Template->set('operatingTitle',$this->service_Language->get('language/admin/stats/OSses'));
		$this->service_Template->set('osTitle',$this->service_Language->get('language/admin/stats/OS'));
	}

	/**
	 * Displays the browsers
	 */
	private function browsers(){
		$a_browsers	= array_reverse($this->model_Stats->getBrowsers($this->i_date));
		krsort($a_browsers,SORT_STRING);

		foreach($a_browsers AS $a_browser){
			$this->service_Template->setBlock('browser',array('name'=>$a_browser['name'],'number'=>$this->format($a_browser['amount'])));
		}

		$this->service_Template->set('browsersTitle',$this->service_Language->get('language/admin/stats/browsers'));
		$this->service_Template->set('browserTitle',$this->service_Language->get('language/admin/stats/browser'));
	}

	/**
	 * Displays the detailed browsers
	 */
	private function browsersLong(){
		$a_browsers	= $this->model_Stats->getBrowsersLong($this->i_date);

		foreach($a_browsers AS $a_browser){
			$this->service_Template->setBlock('browser',array('name'=>$a_browser['name'],'version'=>$a_browser['version'], 'number'=>$this->format($a_browser['amount']),'version'=>$a_browser['version']));
		}

		$this->service_Template->set('browsersTitle',$this->service_Language->get('language/admin/stats/browsers'));
		$this->service_Template->set('browserTitle',$this->service_Language->get('language/admin/stats/browser'));
		$this->service_Template->set('browserVersion',$this->service_Language->get('language/admin/stats/version'));
	}

	/**
	 * Displays the screen colors
	 *
	 * @param int $i_limit	The number of records to show, -1 for no limit
	 */
	private function screenColors($i_limit){
		$a_colors	= $this->model_Stats->getScreenColors($this->i_date,$i_limit);
		krsort($a_colors,SORT_STRING);

		foreach($a_colors AS $a_color){
			$this->service_Template->setBlock('screenColor',array('name'=>$a_color['name'],'number'=>$this->format($a_color['amount'])));
		}

		$this->service_Template->set('screenColorsTitle',$this->service_Language->get('language/admin/stats/screenColors'));
		$this->service_Template->set('colorTitle',$this->service_Language->get('language/admin/stats/colors'));
	}

	/**
	 * Displays the screen sizes
	 *
	 * @param int $i_limit	The number of records to show, -1 for no limit
	 */
	private function screenSizes($i_limit){
		$a_sizes	= $this->model_Stats->getScreenSizes($this->i_date,$i_limit);

		foreach($a_sizes AS $a_size){
			$this->service_Template->setBlock('screenSize',array('name'=>$a_size['width'].'X'.$a_size['height'],'number'=>$this->format($a_size['amount'])));
		}

		$this->service_Template->set('screenSizesTitle',$this->service_Language->get('language/admin/stats/screenSizes'));
		$this->service_Template->set('screenSize',$this->service_Language->get('language/admin/stats/size'));
	}

	/**
	 * Displays the reference
	 */
	private function reference(){
		$a_references	= $this->model_Stats->getReferences($this->i_date);
		$i_total	= 0;

		foreach($a_references AS $a_reference){
			$i_total += $a_reference['amount'];
		}

		if( $i_total == 0 )	$i_total = 1;

		$s_bookmark	= $this->service_Language->get('language/admin/stats/bookmark');
		foreach($a_references AS $a_reference){
			if( empty($a_reference['name']) )	$a_reference['name']	= $s_bookmark;

			$this->service_Template->setBlock('reference',array('name'=>$a_reference['name'],'number'=>$this->format($a_reference['amount']),'percent'=>round($a_reference['amount']/$i_total*100)) );
		}
			
		$this->service_Template->set('referencesTitle',$this->service_Language->get('language/admin/stats/reference'));
	}

	/**
	 * Displays the pages hits
	 */
	private function pages(){
		$a_pages	= $this->model_Stats->getPages($this->i_date);
		$i_total	= 0;

		foreach($a_pages AS $a_page){
			$i_total += $a_page['amount'];
		}

		foreach($a_pages AS $a_page){
			$i_percent	= round($a_page['amount']/$i_total*100);
			$this->service_Template->setBlock('visitorsPage',array('page'=>$a_page['name'],'number'=>$this->format($a_page['amount']), 'width'=>round($i_percent*4),'percent'=>$i_percent) );
		}
			
		$this->service_Template->set('pagesTitle',$this->service_Language->get('language/admin/stats/pages'));
	}
}

$obj_Stats = new Stats();
unset($obj_Stats);
