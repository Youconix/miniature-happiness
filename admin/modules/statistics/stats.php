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
 * Admin statistics view class
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 1.0
 */
define('NIV', '../../../');
include (NIV . 'core/AdminLogicClass.php');

class Stats extends \core\AdminLogicClass
{

    /**
     *
     * @var \core\models\Stats
     */
    private $model_Stats;

    private $i_startDate;

    private $i_endDate;

    private $a_colors;

    /**
     * Starts the class Stats
     */
    public function __construct()
    {
        $this->init();
        
        if (! $this->model_Config->isAjax() || ! isset($this->get['command'])) {
            exit();
        }
        
        switch ($this->get['command']) {
            case 'hits':
                $this->hits();
                break;
                
            case 'hits_hours' :
                $this->hitsHours();
                break;
                
            case 'os':
                $this->OS();
                break;
                
            case 'browser' :
                $this->browser();
                break;
                
            case 'screencolors' :
                $this->screenColors();
                break;
                
            case 'screensizes' :
                $this->screenSizes();
                break;
                
            case 'references' :
                $this->references();
                break;
                
            case 'pages' :
                $this->pages();
                break;
        }
        
        $this->service_Template->set('title_startdate',date('d-m-Y',$this->i_startDate));
        $this->service_Template->set('title_enddate',date('d-m-Y',$this->i_endDate));
    }

    /**
     * Inits the class Stats
     */
    protected function init()
    {
        parent::init();
        
        $this->service_Template->set('amount', t('system/admin/stats/amount'));
        $this->service_Template->set('pageTitle', t('system/admin/stats/title'));
        
        $i_daysMonth = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
        $this->i_startDate = mktime(0, 0, 0, date("n"), 1, date("Y") - 1);
        $this->i_endDate = mktime(23, 59, 59, date("n"), $i_daysMonth, date("Y"));
        
        $this->model_Stats = \Loader::Inject('\core\models\Stats');
        
        $this->a_colors = array(
            '1252f3', // blue
            'b331ae', // purple
            '63413e', // brows
            '3a8555', // green,
            'f32212', // red
            '1ee1f8', // light blue
            'ebf81e', // yellow
            '989898', // grey
            '72ff00', // light green
            '111', // black
        );
    }

    /**
     * Displays the hits and unique visitors
     */
    private function hits()
    {
        $a_lines = array(
            array(
                'color' => $this->a_colors[0],
                'text' => t('system/admin/statistics/hits')
            ),
            array(
                'color' => $this->a_colors[1],
                'text' => t('system/admin/statistics/visitors')
            ),
            array(
                'color' => $this->a_colors[2],
                'text' => 'Terugkerende bezoekers'
            )
        );
        $a_labels = array();
        $a_hits = array(
            0 => array(),
            1 => array(),
            2 => array()
        );
        
        $i_month = date('n', $this->i_startDate);
        for ($i = 1; $i <= 13; $i ++) {
            $a_labels[] = t('system/monthsShort/month' . $i_month);
            
            $i_month ++;
            if ($i_month == 13) {
                $i_month = 1;
            }
        }
        
        $hits = $this->model_Stats->getHits($this->i_startDate, $this->i_endDate);
        foreach ($hits as $hit) {
            $a_hits[0][] = $hit->getAmount();
        }
        $unique = $this->model_Stats->getUnique($this->i_startDate, $this->i_endDate);
        foreach ($unique as $hit) {
            $a_hits[1][] = $hit->getAmount();
        }
        foreach($a_hits[1] AS $key => $value){
            $a_hits[2][$key] = ($a_hits[0][$key] - $value);
        }
        
        $this->service_Template->set('hitsTitle',t('system/admin/statistics/hits'));
        $this->service_Template->set('lines',json_encode($a_lines));
        $this->service_Template->set('labels',json_encode($a_labels));
        $this->service_Template->set('hits', json_encode($a_hits));
    }
    
    /**
     * Displays the hits pro hour
     */
    private function hitsHours(){
        $a_lines = array(
            array(
                'color' => $this->a_colors[0],
                'text' => t('system/admin/statistics/hits')
            )
        );
        $a_labels = array();
        for($i=0; $i<=23; $i++){
            $a_labels[] = $i;
        }
        
        $a_hits = $this->model_Stats->getHitsHours($this->i_startDate,$this->i_endDate);
        
        $this->service_Template->set('hitsTitle',t('system/admin/statistics/hits_hours'));
        $this->service_Template->set('lines',json_encode($a_lines));
        $this->service_Template->set('labels',json_encode($a_labels));
        $this->service_Template->set('hits', json_encode($a_hits));
    }

    /**
     * Displays the operating systems
     */
    private function OS()
    {
        $a_ossesRaw = $this->model_Stats->getOS($this->i_startDate,$this->i_endDate);
        $a_lines = array();
        $a_osses = array();
        
        $i = -1;
        $a_types = array();
        foreach( $a_ossesRaw AS $a_os ){
            if( !in_array($a_os['type'], $a_types) ){
                $a_types[] = $a_os['type'];
                $i++;
            }
            
            $a_lines[] = array(
                    'color' => $this->a_colors[$i],
                    'type' => $a_os['type'],
                    'text' => $a_os['name']
            );
            
            $a_osses[] = array(
                'amount' => $a_os['amount']
            );
        }
        
        $this->service_Template->set('osTitle', $this->service_Language->get('system/admin/stats/OS'));
        $this->service_Template->set('lines',json_encode($a_lines));
        $this->service_Template->set('os',json_encode($a_osses));
    }

    /**
     * Displays the browsers
     */
    private function browser()
    {
        $a_browsersRaw = $this->model_Stats->getBrowsers($this->i_startDate,$this->i_endDate);
        $a_lines = array();
        $a_browsers = array();
        
        $i = -1;
        $a_types = array();
        foreach( $a_browsersRaw AS $a_browser ){
            if( !in_array($a_browser['type'], $a_types) ){
                $a_types[] = $a_browser['type'];
                $i++;
            }
            
            $a_lines[] = array(
                    'color' => $this->a_colors[$i],
                    'type' => $a_browser['type'],
                    'text' => $a_browser['name']
            );
            
            $a_browsers[] = array(
                'amount' => $a_browser['amount']
            );
        }
        
        $this->service_Template->set('browserTitle', $this->service_Language->get('system/admin/stats/browsers'));
        $this->service_Template->set('lines',json_encode($a_lines));
        $this->service_Template->set('browsers',json_encode($a_browsers));
    }

    /**
     * Displays the screen colors
     */
    private function screenColors()
    {
        $a_lines = array();
        $a_colors = array();
        $a_data = $this->model_Stats->getScreenColors($this->i_startDate,$this->i_endDate);
        krsort($a_data, SORT_STRING);
        
        $i=0;
        foreach ($a_data as $a_color) {
            $a_lines[] = array(
                'color' => $this->a_colors[$i],
                'text' => $a_color['name']
            );
            
            $a_colors[] = $a_color['amount'];
            $i++;
        }
        
        $this->service_Template->set('screenColorsTitle', $this->service_Language->get('system/admin/stats/screenColors'));
        $this->service_Template->set('lines',json_encode($a_lines));
        $this->service_Template->set('screenColors',json_encode($a_colors));
    }

    /**
     * Displays the screen sizes
     */
    private function screenSizes()
    {
        $a_lines = array();
        $a_sizes = array();
        $a_data = $this->model_Stats->getScreenSizes($this->i_startDate,$this->i_endDate);
        krsort($a_data, SORT_STRING);
        
        $i=0;
        foreach ($a_data as $a_size) {
            $a_lines[] = array(
                'color' => $this->a_colors[$i],
                'text' => $a_size['width'].'X'.$a_size['height']
            );
            
            $a_sizes[] = $a_size['amount'];
            $i++;
        }
        
        $this->service_Template->set('screenSizesTitle', $this->service_Language->get('system/admin/stats/screenSizes'));
        $this->service_Template->set('lines',json_encode($a_lines));
        $this->service_Template->set('screenSizes',json_encode($a_sizes));
    }

    /**
     * Displays the references
     */
    private function references()
    {
        $a_lines = array();
        $a_references = array();
        $a_data = $this->model_Stats->getReferences($this->i_startDate,$this->i_endDate);
        krsort($a_data, SORT_STRING);
        
        $i=0;
        foreach ($a_data as $a_reference) {
            $a_lines[] = array(
                'color' => $this->a_colors[$i],
                'text' => $a_reference['name']
            );
        
            $a_references[] = $a_reference['amount'];
            $i++;
        }
        
        $this->service_Template->set('referencesTitle', $this->service_Language->get('system/admin/statistics/references'));
        $this->service_Template->set('lines',json_encode($a_lines));
        $this->service_Template->set('references',json_encode($a_references));
    }

    /**
     * Displays the pages hits
     */
    private function pages()
    {
        $a_lines = array();
        $a_pages = array();
        $a_data = $this->model_Stats->getPages($this->i_startDate,$this->i_endDate);
        krsort($a_data, SORT_STRING);
        
        $i=0;
        foreach ($a_data as $a_page) {
            $a_lines[] = array(
                'color' => $this->a_colors[$i],
                'text' => $a_page['name']
            );
        
            $a_pages[] = $a_page['amount'];
            $i++;
        }
        
        $this->service_Template->set('pagesTitle', $this->service_Language->get('system/admin/statistics/pages'));
        $this->service_Template->set('lines',json_encode($a_lines));
        $this->service_Template->set('pages',json_encode($a_pages));
    }
}

$obj_Stats = new Stats();
unset($obj_Stats);