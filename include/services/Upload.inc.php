<?php
/**
 * File upload service
 *
* This file is part of Scripthulp framework                                    
 *                                                                              
 * @copyright 2012,2013,2014  Rachelle Scheijen                                
 * @author    Rachelle Scheijen                                                
 * @since     1.0                                                              
 * @changed   10/01/13                                                          
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
class Service_Upload extends Service {
	protected $service_FileData;
	
	/**
	 * PHP 5 constructor
	 */
	public function __construct(){
		$this->service_FileData	= Memory::services('FileData');
	}
	
	/**
	 * Checks if the file is correct uploaded
	 * 
	 * @param string $s_name	The form field name
	 * @return boolean	True if the file is uploaded,otherwise false
	 */
	public function isUploaded($s_name){
		return (array_key_exists($s_name, $_FILES) && $_FILES[$s_name]['error'] == 0);
	}
	
	/**
	 * Checks if the file is valid
	 * 
	 * @param string	$s_name	The form field name
	 * @param array		$a_extensions	The accepted extensions with the mimetype as value
	 * @param int		$i_maxSize		The maximun filesize in bytes, optional
	 * @return boolean	True if the file is valid, otherwise false
	 */
	public function isValid($s_name,$a_extensions,$i_maxSize = -1){
		$s_mimetype	= $this->service_FileData->getMimeType($_FILES[$s_name]['tmp_name']);
		
		$a_data = explode('/',$s_mimetype);
		$a_fileExtensions	= explode('.',$_FILES[$s_name]['name']);
		$s_extension = strtolower(end( $a_fileExtensions ));
		
		if( !array_key_exists($s_extension,$a_extensions) || ($a_extensions[$s_extension] != $s_mimetype && (is_array($a_extensions[$s_extension]) && !in_array($s_mimetype,$a_extensions[$s_extension])) ) ){
			unlink($_FILES[$s_name]['tmp_name']);
			return false;
		}
			
		if( $i_maxSize != -1 && $_FILES[$s_name]['size'] > $i_maxSize ){
			unlink($_FILES[$s_name]['tmp_name']);
			return false;
		}
		
		return true;
	}
	
	/**
	 * Moves the uploaded file to the target directory
	 * Does NOT overwrite files with the same name.
	 * 
	 * @param string	$s_name			The form field name
	 * @param string	$s_targetDir	The target directory
	 * @param string 	$s_targetName	The name to use. Do not provide an extension, optional. Default the filename
	 * @return string	The choosen filename without directory
	 */
	public function moveFile($s_name,$s_targetDir,$s_targetName = ''){
		if( empty($s_targetName) ){
			$s_targetName	= $_FILES[$s_name]['name'];
		}
		else {
			$a_fileExtensions	= explode('.',$_FILES[$s_name]['name']);
			$s_extension = '.'.strtolower(end( $a_fileExtensions ));
			
			$s_targetName .= $s_extension;
		}
			
		if( file_exists($s_targetDir.'/'.$s_targetName) ){
			$a_fileExtensions	= explode('.',$_FILES[$s_name]['name']);
			$s_extension = '.'.strtolower(end( $a_fileExtensions ));
			
			$i=1;
			$s_testname	= $s_targetName;
			while( file_exists($s_targetDir.'/'.str_replace($s_extension,'__'.$i.$s_extension,$s_testname) ) ){
				$i++;
			}
			
			$s_targetName	= str_replace($s_extension,'__'.$i.$s_extension,$s_testname);
		}
		
		move_uploaded_file($_FILES[$s_name]['tmp_name'],$s_targetDir.'/'.$s_targetName);
		
		if( !file_exists($s_targetDir.'/'.$s_targetName) ){
			return '';
		}
		
		return $s_targetName;
	}
	
	/**
	 * Resizes the given picture
	 * Works only with jpg, gif and png
	 * 
	 * @param string $s_file	The file url
	 * @param int	$i_maxWidth	The max width
	 * @param int	$i_maxHeight The max height
	 * @throws Exception	If the file is not a jpg,gif or png image
	 */
	public function resizeImage($s_file,$i_maxWidth,$i_maxHeight){
		$a_mimetype	= explode('/',$this->service_FileData->getMimeType($s_file));
		$obj_image = null;
		
		if( $a_mimetype[1] == 'jpg' || $a_mimetype[1] == 'jpeg' ){
			$s_type	= 'jpg';
			$obj_image  = imagecreatefromjpeg($s_file);			
		}
		else if( $a_mimetype[1] == 'gif' ){
			$s_type = 'gif';
			$obj_image  = imagecreatefromgif($s_file);
		} 
		else if( $a_mimetype[1] == 'png' ){
			$s_type = 'png';
			$obj_image  = imagecreatefrompng($s_file);
		}
		else {
			throw new Exception("Invalid file ".$s_file);
		}
		
		$i_ratio	= 1;
		$i_width = imagesx($obj_image);
		$i_height = imagesy($obj_image);
		
		if( $i_width > $i_height ){
			if( $i_width > $i_maxWidth ){
				$i_ratio	= $i_maxWidth/$i_width;		
			}
			else if( $i_height > $i_maxHeight ){
				$i_ratio = $i_maxHeigh/$i_height;
			}
		}
		else if( $i_height > $i_width ){
			if( $i_height > $i_maxHeigh ){
				$i_ratio = $i_maxHeigh/$i_height;
			}
			else if( $i_width > maxWidth ){
				$i_ratio	= $i_maxWidth/$i_width;		
			} 
		}
			        	
		$i_maxWidth	= round($i_width*$i_ratio);
		$i_maxHeigh	= round($i_height*$i_ratio);
		
		$obj_trumb	= null;
		
		if( $i_ratio != 1 ){
			if( $a_mimetype[1] == 'jpg' || $a_mimetype[1] == 'jpeg' ){
				$obj_trumb	= $this->resize($obj_image, $s_type,$i_maxWidth, $i_maxHeigh);
				imagejpeg($obj_trumb, $s_file,83);
			}
			else if( $a_mimetype[1] == 'gif' ){
				$obj_trumb	= $this->resize($obj_image, $s_type, $i_maxWidth, $i_maxHeigh);
				imagegif($obj_trumb, $s_file);
			} 
			else if( $a_mimetype[1] == 'png' ){
				$obj_trumb	= $this->resize($obj_image, $s_type, $i_maxWidth, $i_maxHeigh);
				imagepng($obj_trumb, $s_file,3);
			}
		}
		
		if( !is_null($obj_image) ){
			imagedestroy($obj_image);
		}
		
		if( !is_null($obj_trumb) ){
			imagedestroy($obj_trumb);
		}
	}
	
	/**
	 * Generates a trumbnail with max width 50 pixels.
	 * Works only with jpg, gif and png
	 * 
	 * @param string $s_file	The file url
	 */
	public function makeTrumb($s_file){
		$a_mimetype	= explode('/',$this->service_FileData->getMimeType($s_file));
		
		$s_extension	= substr($s_file, strrpos($s_file,'.'));
		$s_destination	= str_replace($s_extension,'_trumb'.$s_extension,$s_file);
		$obj_image = null;
		
		if( $a_mimetype[1] == 'jpg' || $a_mimetype[1] == 'jpeg' ){
			$obj_image  = imagecreatefromjpeg($s_file);
			$obj_trumb	= $this->makeTrumbProcess($obj_image,'jpeg');
			imagejpeg($obj_trumb, $s_destination,83);
		}
		else if( $a_mimetype[1] == 'gif' ){
			$obj_image  = imagecreatefromgif($s_file);
			$obj_trumb	= $this->makeTrumbProcess($obj_image,'gif');
			imagegif($obj_trumb, $s_destination);
		} 
		else if( $a_mimetype[1] == 'png' ){
			$obj_image  = imagecreatefrompng($s_file);
			$obj_trumb	= $this->makeTrumbProcess($obj_image,'png');
			imagepng($obj_trumb, $s_destination,3);
		}
		
		if( !is_null($obj_image) ){
			imagedestroy($obj_image);
			imagedestroy($obj_trumb);
		}
	}
	
	/**
	 * Makes the trumb
	 * 
	 * @param  resource $obj_image	The image
	 * @param	string	$s_type 	The type (gif|png|jpeg)
	 * @return resource	 The trumbnail
	 */
	protected function makeTrumbProcess($obj_image,$s_type){
		$i_width = imagesx($obj_image);
		$i_height = imagesy($obj_image);
		
		$i_desiredWidth	= 110;
		
		$fl_factor	= $i_desiredWidth/$i_width;
		$i_desiredHeight	= round($fl_factor*$i_height);
		
		return $this->resize($obj_image, $s_type,$i_desiredWidth, $i_desiredHeight);
	}
	
	/**
	 * Resizes the given image
	 * 
	 * @param  resource $obj_image	The image
	 * @param	string	$s_type 	The type (gif|png|jpeg)
	 * @param int $i_desiredWidth		The width
	 * @param int $i_desiredHeight		The height
	 * @return resource		The resized image
	 */
	protected function resize($obj_image,$s_type,$i_desiredWidth,$i_desiredHeight){
		$i_width = imagesx($obj_image);
		$i_height = imagesy($obj_image);
		
		/* create a new, "virtual" image */
		$obj_virtualImage = imagecreatetruecolor($i_desiredWidth, $i_desiredHeight);
		
		/* Check alpha */
		if( ($s_type == 'gif') || ($s_type == 'png') ){
      		$i_alpha = imagecolortransparent($obj_image);
   
      		if( $i_alpha >= 0 ){
   				// Get the original image's transparent color's RGB values
        		$a_alphaColors    = imagecolorsforindex($obj_image, $i_alpha);
   				$i_alpha    = imagecolorallocate($obj_virtualImage, $a_alphaColors['red'], $a_alphaColors['green'], $a_alphaColors['blue']);
   				imagefill($obj_virtualImage, 0, 0, $i_alpha);
   				imagecolortransparent($obj_virtualImage, $i_alpha);  
      		}
      	}
      	else if( $s_type == 'png' ){
        	imagealphablending($obj_virtualImage, false);
			$i_color = imagecolorallocatealpha($obj_virtualImage, 0, 0, 0, 127);
			imagefill($obj_virtualImage, 0, 0, $i_color);
			imagesavealpha($obj_virtualImage, true);
    	}
		
		/* copy source image at a resized size */
		imagecopyresampled($obj_virtualImage, $obj_image, 0, 0, 0, 0, $i_desiredWidth, $i_desiredHeight, $i_width, $i_height);
		
		return $obj_virtualImage;
	}
}
?>