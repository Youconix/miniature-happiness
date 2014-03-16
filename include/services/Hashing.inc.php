<?php
class Service_Hashing extends Service {
	private $obj_hashing = null;
	private $s_systemSalt;
	
	public function __construct(){
		if( !function_exists('password_hash') ){
			$service_Logs	= Memory::services('Logs');
			
			if( CRYPT_BLOWFISH != 1 ){
				/* Fallback
				 * Security warning
				 */
				$this->obj_hashing	= new HashingFallback();
				$service_Logs->setLog('security','Missing bcrypt and CRYPT_BLOWFISH. Falling back to sha1 hashing. Upgrade your PHP-installation to min. 5.5 at ones!');
			}	
			else {
				/* Legancy
				 * Security warning
				 */
				$this->obj_hashing	= new HashLegancy();
				$service_Logs->setLog('security','Missing bcrypt. Falling back to crypt() with CRYPT_BLOWFISH hashing. Upgrade your PHP-installation to min. 5.5 as soon as possible!');
			}
		}
		else {
			$this->obj_hashing	= new HashNormal();
		}
		
		$service_XmlSettings  = Memory::services('XmlSettings');
		$this->s_systemSalt = $service_XmlSettings->get('settings/main/salt');
		
		$i_length = strlen($this->s_systemSalt); 
		if( $i_length < 22 ){ $this->s_systemSalt .= substr($this->s_systemSalt,0,(22-$i_length)); }
	}
	
	public function hash($s_text,$s_salt){
		return $this->obj_hashing->hash($s_text, $s_salt);
	}
	
	public function verify($s_text,$s_stored,$s_salt){		
		return $this->obj_hashing->verify($s_text, $s_stored,$s_salt);
	}
	
	public function hashUserPassword($s_password,$s_username){
		return $this->obj_hashing->hashUserPassword($s_username, $s_password, $this->s_systemSalt);
	}
	
	public function verifyUserPassword($s_username,$s_password,$s_stored){
		$s_text	= $this->hashUserPassword($s_password, $s_username);
		
		return $this->obj_hashing->verify($s_text, $s_stored,$this->s_systemSalt);
	}
	
	public function createSalt(){
		if( function_exists('openssl_random_pseudo_bytes') ){
			return bin2hex( openssl_random_pseudo_bytes(30) );
		}
		
		$service_Random	= Memory::services('Random');
		return $service_Random->randomAll(30);
	}
}

abstract class Hashing {
	abstract public function hash($s_text,$s_salt);
	
	public function verify($s_text,$s_stored,$s_salt){
		$s_input = $this->hash($s_text, $s_salt);
		return $s_input === $s_stored;
	}
	
	public function hashUserPassword($s_username,$s_password,$s_salt){
		$s_text = $this->createUserPassword($s_username, $s_password);
		$s_hash = $this->hash($s_text, $s_salt);
	
		$i_missing = (60 - strlen($s_hash));
		if( $i_missing > 0 ){
			$s_hash = $s_hash .= substr($s_hash,0,$i_missing);
		}
	
		return $s_hash;
	}
	
	public function verifyUserPassword($s_username,$s_password,$s_stored,$s_salt){
		$s_text = $this->hashUserPassword($s_username, $s_password, $s_salt);
		return $this->verify($s_text, $s_stored, $s_salt);
	}

	protected function createUserPassword($s_username,$s_password){
		return substr(md5($s_username),5,30).$s_password;
	}
}

class HashNormal extends Hashing{
	public function hash($s_text,$s_salt){
		$a_options	= array('salt'=>$s_salt);
		
		$s_hash = password_hash($s_text,PASSWORD_BCRYPT,$a_options);
		return $s_hash;
	}
	
	public function verify($s_text,$s_stored,$s_salt){
		return password_verify($s_input,$s_stored);
	}
	
	public function hashUserPassword($s_username,$s_password,$s_salt){
		$a_options	= array('salt'=>$s_salt);
		$s_text = $this->createUserPassword($s_username, $s_password);
		
		$s_hash = password_hash($s_text,PASSWORD_BCRYPT,$a_options);
		
		return $s_hash;
	}
	
	public function verifyUserPassword($s_username,$s_password,$s_stored,$s_salt){
		$s_text = $this->createUserPassword($s_username, $s_password);
		
		return password_verify($s_text,$s_stored);
	}
}

class HashLegancy  extends Hashing{
	public function hash($s_text,$s_salt){
		return crypt($s_text,$s_salt);
	}
}

class HashingFallback  extends Hashing {
	public function hash($s_text,$s_salt){
		return sha1($s_text,$s_salt);
	}
}
?>