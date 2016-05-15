<?php
namespace admin\modules\general;

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
 * Admin maintenance class
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 1.0
 */
class Updater extends \core\AdminLogicClass
{
    
    /**
     * 
     * @var \core\services\Updater
     */
    private $updater;

    /**
     * Starts the class Groups
     *
     * @param \Input $Input
     * @param \Config $config            
     * @param \Language $language            
     * @param \Output $template            
     * @param \Logger $logs
     * @param \core\services\Maintenance    $maintenance            
     */
    public function __construct(\Input $Input, \Config $config, \Language $language, \Output $template, 
        \Logger $logs,\core\services\Maintenance $maintenance,\core\services\Updater $updater)
    {
        parent::__construct($Input, $config, $language, $template, $logs);
        
        $this->maintenance = $maintenance;
        $this->updater = $updater;
    }

    /**
     * Routes the controller
     *
     * @see Routable::route()
     */
    public function route($s_command)
    {        
        switch ($s_command) {                
            case 'checkupdates' :
            	$this->checkUpdates();
            	break;
        }
    }
    
    private function checkUpdates(){
    	$xml = $this->updater->checkUpdates();
    	if( is_null($xml) ){
    		$this->template->set('status','could not connect to server');
    		return;
    	}
    	
    	$this->template->displayPart('ok');
    	if( $xml->get('updates/currentMajor') != $xml->get('updates/maxMajor') ){
    		$this->template->displayPart('major_upgrade');
    		$this->template->set('major',$xml->get('updates/maxMajor'));
    	}
    	if( $xml->get('updates/currentVersion') != $xml->get('updates/maxVersion') ){
    		$this->template->displayPart('minor_upgrade');
    		$this->template->set('currentVersion',$xml->get('updates/currentVersion'));
    		$this->template->set('maxVersion',$xml->get('updates/maxVersion'));
    		
    		$a_versions = $xml->getBlock('updates/versions');
    		foreach( $a_versions AS $version ){
    			foreach ($version->childNodes AS $versionItem ){
    				$a_item = array();
    				foreach($versionItem->childNodes AS $item) {
	    				if ($item->tagName == 'number') {
	    					$a_item['version'] = $item->nodeValue;
	    				}
	    				else if( $item->tagName == 'description' ){
	    					$a_item['description'] = htmlentities($item->nodeValue);
	    				}
	    			}
    				$this->template->setBlock('versions',$a_item);
    			}
    		}
    	}
    }
}