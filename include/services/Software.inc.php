<?php 
/** 
 * Services for installing and updating framework files                         
 *                                                                              
 * This file is part of Scripthulp framework                                    
 *                                                                              
 * @copyright 2012,2013,2014  Rachelle Scheijen                                
 * @author    Rachelle Scheijen                                                
 * @since     1.0                                                              
 * @changed   26/09/2012
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
class Service_Software extends Service {
	private $service_File;
	private $service_XmlSettings;
	private $service_CurlManager;
	private $service_FTP;
	private $fl_version;
	private $s_repository = 'http://framework.scripthulp.com/update.php';
	
	public function __construct(){
		$this->service_File	= Memory::services('File');
		$this->service_XmlSettings	= Memory::services('XmlSettings');
		$this->service_CurlManager	= Memory::services('CurlManager');
		$this->service_FTP	= Memory::services('FTP');
		
		$this->fl_version	= $this->service_XmlSettings->get('settings/version');
	}
	
	/**
	 * Checks for new updates
	 * 
	 * @return int	The status code
	 * 		-1	Connection error
	 * 		0	No updates
	 * 		1	Updates available
	 */
	public function checkUpdates(){
		$s_result	= $this->service_CurlManager->performGetCall($this->s_repository,array('command'=>'checkVersion'));
		if( $this->service_CurlManager->getHeader() != '200' ){
			return -1;
		}
		
		if( $s_result != $this->fl_version ){
			return 1;
		}
		
		return 0;
	}
	
	/**
	 * Updates the framework
	 * 
	 * @return int	The status code
	 * 		-2		Connection error
	 * 		-1		Updating failed
	 * 		0		No updates
	 * 		1		Update completed
	 */
	public function update(){
		/* Get update file */
		$s_result	= $this->service_CurlManager->performGetCall($this->s_repository,array('command'=>'getUpdates','version'=>$this->fl_version));
		if( $this->service_CurlManager->getHeader() != '200' ){
			return -2;
		}
		
		$s_file	= NIV.'admin/data/update.xml';
		
		try {
			$this->service_File->writeFile($s_file,$s_result);
			
			$service_Xml	= Memory::services('Xml');
			$service_Xml->load($s_file);
			
			/* Double check version */
			if( $service_Xml->get('update/version') == $this->fl_version ){
				$this->service_File->deleteFile($s_file);
				return 0;
			}
			
			/* Oke lets update */
			set_time_limit(0);
			
			$bo_ftp	= false;
			if( $this->service_XmlSettings->get('settings/ftp/enabled') != 0 ){
				$this->service_FTP->defaultConnect();
				$bo_ftp	= true;
				$s_temp	= NIV.'admin/data/temp';
			}
			
			$i=1;
			while($service_XML->exists('update/update'.$i.'') ){
				$s_filename	= $service_XML->get('update/update'.$i.'');
				if( !$this->service_File->exists(NIV.$s_filename) )	
					continue;
				
				$s_content	= $this->service_CurlManager->performGetCall($this->s_repository,array('command'=>'download','file'=>$s_filename));
				
				if( $this->service_CurlManager->getHeader() != '200' ){
					throw new IOException("Unable to download update ".$s_filename);
				}

				if( $bo_ftp ){					
					$this->service_File->writeFile($s_temp,$s_content);
					$this->put($s_filename,$s_temp,true);
				}
				else {
					$this->service_File->writeFile(NIV.$s_filename,$s_content,0775);
				}
				
				$i++;
			}
			
			if( $bo_ftp ){
				$this->service_File->deleteFile($s_temp);
			}
			
			$this->service_XmlSettings->set('settings/version',$this->fl_version); 
			$this->service_File->deleteFile($s_file);
		}
		catch(Exception $e){
			return -1;
		}
	}
}
?>