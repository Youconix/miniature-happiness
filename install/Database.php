
<?php
/**
 * Database filler class
 * This file should be edited for every website
 *
 * This file is part of the Scripthulp framework installer
 *
 * @copyright 2012,2013,2014  Rachelle Scheijen
 * @author    Rachelle Scheijen
 * @since     1.0
 * @changed   04/06/2013
 *
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
class Database {
	private $service_Database;
	private $service_QueryBuilder;
	private $s_defaultLanguage;
	
	private $a_countries;
	private $a_nationalities;

	/**
	 * PHP 5 constructor
	 */
	public function __construct(){
		require(NIV.'include/Memory.php');
		Memory::startUp();

		$this->service_QueryBuilder	= Memory::services('QueryBuilder')->createBuilder();
		$this->service_Database	= Memory::services('Database');
		$this->s_defaultLanguage	= Memory::services('XmlSettings')->get('defaultLanguage');
	}

	/**
	 * Populates the database
	 */
	public function populateDatabase(){
		$service_File = Memory::services('File');
		$this->a_countries	= explode("\n",$service_File->readFile(NIV.'install/countries.csv'));
		$this->a_nationalities = explode("\n",$service_File->readFile(NIV.'install/nationality.csv'));
		
		$this->populateFramework();
		$this->populateSite();

		$obj_create	= $this->service_QueryBuilder->commit();
	}

	/**
	 * Creates the framework tables and data
	 *
	 * @throws DBException If the queries failes
	 */
	private function populateFramework(){
		$obj_create	= $this->service_QueryBuilder->getCreate('ipban',true);
		$obj_create->addRow('id','int',11,'',false,false,true);
		$obj_create->addRow('ip','varchar',250);
		$obj_create->addPrimary('id');
		$this->service_QueryBuilder->getResult();
			
		$obj_create	= $this->service_QueryBuilder->getCreate('groups',true);
		$obj_create->addRow('id','int',11);
		$obj_create->addRow('name','varchar',100);
		$obj_create->addRow('description','text');
		$obj_create->addEnum('automatic',array('0','1'),'0');
		$obj_create->addPrimary('id');
		$this->service_QueryBuilder->getResult();
			
		$this->service_QueryBuilder->insert('groups',array('id','name','description','automatic'),array('i','s','s','s'),array(0, 'Admin', 'Admin group', '0'))->getResult();
		$this->service_QueryBuilder->insert('groups',array('id','name','description','automatic'),array('i','s','s','s'),array(1, 'Site', 'General site group', '1'))->getResult();
			
		$obj_create = $this->service_QueryBuilder->getCreate('group_pages',true);
		$obj_create->addRow('id','int',11,'',false,false,true);
		$obj_create->addRow('groupID','int',11);
		$obj_create->addRow('page','varchar',255);
		$obj_create->addRow('minLevel','smallint',6,-1);
		$obj_create->addPrimary('id')->addIndex('groupID')->addIndex('page');
		$this->service_QueryBuilder->getResult();
			
		$this->service_QueryBuilder->insert('group_pages',array('groupID','page','minLevel'),array('i','s','s'),array(0,'admin/index.php', 2))->getResult();
		$this->service_QueryBuilder->insert('group_pages',array('groupID','page','minLevel'),array('i','s','s'),array(0,'admin/groups.php', 2))->getResult();
		$this->service_QueryBuilder->insert('group_pages',array('groupID','page','minLevel'),array('i','s','s'),array(0,'admin/logs.php', 2))->getResult();
		$this->service_QueryBuilder->insert('group_pages',array('groupID','page','minLevel'),array('i','s','s'),array(0,'admin/maintenance.php', 2))->getResult();
		$this->service_QueryBuilder->insert('group_pages',array('groupID','page','minLevel'),array('i','s','s'),array(0,'admin/notices.php', 2))->getResult();
		$this->service_QueryBuilder->insert('group_pages',array('groupID','page','minLevel'),array('i','s','s'),array(0,'admin/settings.php', 2))->getResult();
		$this->service_QueryBuilder->insert('group_pages',array('groupID','page','minLevel'),array('i','s','s'),array(0,'admin/stats.php', 2))->getResult();
		$this->service_QueryBuilder->insert('group_pages',array('groupID','page','minLevel'),array('i','s','s'),array(0,'admin/users.php', 2))->getResult();
		$this->service_QueryBuilder->insert('group_pages',array('groupID','page','minLevel'),array('i','s','s'),array(0,'admin/software.php', 2))->getResult();
		$this->service_QueryBuilder->insert('group_pages',array('groupID','page','minLevel'),array('i','s','s'),array(1,'index.php', -1))->getResult();
		$this->service_QueryBuilder->insert('group_pages',array('groupID','page','minLevel'),array('i','s','s'),array(1,'login.php', -1))->getResult();
		$this->service_QueryBuilder->insert('group_pages',array('groupID','page','minLevel'),array('i','s','s'),array(1,'logout.php', 0))->getResult();
		$this->service_QueryBuilder->insert('group_pages',array('groupID','page','minLevel'),array('i','s','s'),array(1,'activate.php', -1))->getResult();
		$this->service_QueryBuilder->insert('group_pages',array('groupID','page','minLevel'),array('i','s','s'),array(1,'forgot_password.php', -1))->getResult();
		$this->service_QueryBuilder->insert('group_pages',array('groupID','page','minLevel'),array('i','s','s'),array(1,'install/index.php', -1))->getResult();
		$this->service_QueryBuilder->insert('group_pages',array('groupID','page','minLevel'),array('i','s','s'),array(1,'registration.php', -1))->getResult();
		$this->service_QueryBuilder->insert('group_pages',array('groupID','page','minLevel'),array('i','s','s'),array(1,'styles/default/images/captcha.php',-1))->getResult();
		$this->service_QueryBuilder->insert('group_pages',array('groupID','page','minLevel'),array('i','s','s'),array(1,'errors/403.php',-1))->getResult();
		$this->service_QueryBuilder->insert('group_pages',array('groupID','page','minLevel'),array('i','s','s'),array(1,'errors/404.php',-1))->getResult();
		$this->service_QueryBuilder->insert('group_pages',array('groupID','page','minLevel'),array('i','s','s'),array(1,'errors/500.php',-1))->getResult();

		$obj_create	= $this->service_QueryBuilder->getCreate('group_users',true);
		$obj_create->addRow('id','bigint',20,'',false,false,true);
		$obj_create->addRow('groupID','int',11);
		$obj_create->addRow('userid','int',11);
		$obj_create->addEnum('level',array('0','1','2'),'0');
		$obj_create->addPrimary('id')->addIndex('groupID')->addIndex('userid');
		$this->service_QueryBuilder->getResult();
			
		$obj_create	= $this->service_QueryBuilder->getCreate('users',true);
		$obj_create->addRow('id','int',11,'',false,false,true);
		$obj_create->addRow('nick','varchar',100);
		$obj_create->addRow('password','varchar',100);
		$obj_create->addRow('email','varchar',150);
		$obj_create->addRow('avatar','varchar',100);
		$obj_create->addEnum('bot',array('0','1'),'0');
		$obj_create->addRow('registrated','int',11,'0');
		$obj_create->addEnum('active',array('0','1'),'0');
		$obj_create->addEnum('blocked',array('0','1'),'0');
		$obj_create->addEnum('password_expired',array('0','1'),'0');
		$obj_create->addRow('activation','varchar',150);
		$obj_create->addRow('lastLogin','int',11,'0');
		$obj_create->addRow('profile','text');
		$obj_create->addRow('language','varchar',10,$this->s_defaultLanguage);
		$obj_create->addRow('loginType','varchar',100,'normal');
		$obj_create->addPrimary('id');
		$this->service_QueryBuilder->getResult();
			
		$obj_create = $this->service_QueryBuilder->getCreate('password_codes',true);
		$obj_create->addRow('id','int',11,'',false,false,true);
		$obj_create->addRow('userid','int',11);
		$obj_create->addRow('code','varchar',150);
		$obj_create->addRow('password','varchar',150);
		$obj_create->addRow('expire','int',11);
		$obj_create->addPrimary('id');
		$this->service_QueryBuilder->getResult();
			
		$obj_create	= $this->service_QueryBuilder->getCreate('login_tries',true);
		$obj_create->addRow('hash','varchar',100);
		$obj_create->addRow('ip','varchar',100);
		$obj_create->addRow('timestamp','int',11);
		$obj_create->addRow('tries','int',11);
		$obj_create->addPrimary('hash');
		$this->service_QueryBuilder->getResult();
			
		$obj_create = $this->service_QueryBuilder->getCreate('pm',true);
		$obj_create->addRow('id','int',11,'',false,false,true);
		$obj_create->addRow('fromUserid','int',11);
		$obj_create->addRow('toUserid','int',11);
		$obj_create->addRow('title','varchar',150);
		$obj_create->addRow('message','text');
		$obj_create->addRow('send','int',11);
		$obj_create->addEnum('unread',array('0','1'),'1');
		$obj_create->addPrimary('id')->addIndex('fromUserid')->addIndex('toUserid');
		$this->service_QueryBuilder->getResult();
			
		$obj_create	= $this->service_QueryBuilder->getCreate('smileys',true);
		$obj_create->addRow('id','int',11,'',false,false,true);
		$obj_create->addRow('code','varchar',15);
		$obj_create->addRow('url','varchar',100);
		$obj_create->addPrimary('id');
		$this->service_QueryBuilder->getResult();

		$this->service_QueryBuilder->insert('smileys',array('code','url'),array('s','s'),array(':)', 'blij.png'))->getResult();
		$this->service_QueryBuilder->insert('smileys',array('code','url'),array('s','s'),array(':-)', 'blij.png'))->getResult();
		$this->service_QueryBuilder->insert('smileys',array('code','url'),array('s','s'),array(':(', 'nietblij.png'))->getResult();
		$this->service_QueryBuilder->insert('smileys',array('code','url'),array('s','s'),array(';)', 'knipoog.png'))->getResult();
		$this->service_QueryBuilder->insert('smileys',array('code','url'),array('s','s'),array(';-)', 'knipoog.png'))->getResult();
		$this->service_QueryBuilder->insert('smileys',array('code','url'),array('s','s'),array(':-P', 'tong.png'))->getResult();
		$this->service_QueryBuilder->insert('smileys',array('code','url'),array('s','s'),array(':P', 'tong.png'))->getResult();
		$this->service_QueryBuilder->insert('smileys',array('code','url'),array('s','s'),array(':D', 'heelblij.png'))->getResult();
		$this->service_QueryBuilder->insert('smileys',array('code','url'),array('s','s'),array(':-D', 'heelblij.png'))->getResult();
		$this->service_QueryBuilder->insert('smileys',array('code','url'),array('s','s'),array('8-)', 'cool.png'))->getResult();
		$this->service_QueryBuilder->insert('smileys',array('code','url'),array('s','s'),array('8)', 'cool.png'))->getResult();
		$this->service_QueryBuilder->insert('smileys',array('code','url'),array('s','s'),array(':|', 'verbaasd.png'))->getResult();
		$this->service_QueryBuilder->insert('smileys',array('code','url'),array('s','s'),array(':-|', 'verbaasd.png'))->getResult();
		$this->service_QueryBuilder->insert('smileys',array('code','url'),array('s','s'),array(':$', 'blozen.png'))->getResult();
		$this->service_QueryBuilder->insert('smileys',array('code','url'),array('s','s'),array(':-$', 'blozen.png'))->getResult();
		$this->service_QueryBuilder->insert('smileys',array('code','url'),array('s','s'),array('>:)', 'devil.png'))->getResult();
		$this->service_QueryBuilder->insert('smileys',array('code','url'),array('s','s'),array('(6)', 'devil.png'))->getResult();
		$this->service_QueryBuilder->insert('smileys',array('code','url'),array('s','s'),array(':@', 'angry.png'))->getResult();
		$this->service_QueryBuilder->insert('smileys',array('code','url'),array('s','s'),array(':\'(', 'cry.png'))->getResult();
		$this->service_QueryBuilder->insert('smileys',array('code','url'),array('s','s'),array('(l)', 'love.png'))->getResult();
		$this->service_QueryBuilder->insert('smileys',array('code','url'),array('s','s'),array(':party:', 'party.png'))->getResult();
		$this->service_QueryBuilder->insert('smileys',array('code','url'),array('s','s'),array(':sick:', 'sick.png'))->getResult();
		$this->service_QueryBuilder->insert('smileys',array('code','url'),array('s','s'),array(':S', 'verbaasd.png'))->getResult();
			
		$obj_create	= $this->service_QueryBuilder->getCreate('autologin',true);
		$obj_create->addrow('id','int',11,'',false,false,true);
		$obj_create->addRow('userID','int',11);
		$obj_create->addRow('username','varchar',255);
		$obj_create->addRow('IP','varchar', 40 );
		$obj_create->addPrimary('id')->addIndex('IP');
		$this->service_QueryBuilder->getResult();
			
		/* Stats */
		$obj_create = $this->service_QueryBuilder->getCreate('stats_browser',true);
		$obj_create->addRow('id','int',11,'',false,false,true);
		$obj_create->addRow('name','varchar',100);
		$obj_create->addRow('version','varchar',10);
		$obj_create->addRow('amount','int',11,'0');
		$obj_create->addRow('datetime','int',11);
		$obj_create->addPrimary('id');
		$this->service_QueryBuilder->getResult();
			
		$obj_create	= $this->service_QueryBuilder->getCreate('stats_hits',true);
		$obj_create->addRow('id','int',11,'',false,false,true);
		$obj_create->addRow('amount','int',11,'0');
		$obj_create->addRow('datetime','int',11);
		$obj_create->addPrimary('id');
		$this->service_QueryBuilder->getResult();
			
		$obj_create	= $this->service_QueryBuilder->getCreate('stats_OS',true);
		$obj_create->addrow('id','int',11,'',false,false,true);
		$obj_create->addRow('name','varchar',100);
		$obj_create->addRow('amount','int',11,'0');
		$obj_create->addRow('datetime','int',11);
		$obj_create->addrow('type','varchar',100);
		$obj_create->addPrimary('id');
		$this->service_QueryBuilder->getResult();
			
		$obj_create	= $this->service_QueryBuilder->getCreate('stats_pages',true);
		$obj_create->addRow('id','int',11,'',false,false,true);
		$obj_create->addRow('name','varchar',150);
		$obj_create->addRow('amount','int',11,'0');
		$obj_create->addRow('datetime','int',11);
		$obj_create->addPrimary('id');
		$this->service_QueryBuilder->getResult();
			
		$obj_create = $this->service_QueryBuilder->getCreate('stats_reference',true);
		$obj_create->addRow('id','int',11,'',false,false,true);
		$obj_create->addRow('name','varchar',255);
		$obj_create->addRow('amount','int',11,'0');
		$obj_create->addRow('datetime','int',11);
		$obj_create->addPrimary('id');
		$this->service_QueryBuilder->getResult();
			
		$obj_create	= $this->service_QueryBuilder->getCreate('stats_screenColors',true);
		$obj_create->addRow('id','int',11,'',false,false,true);
		$obj_create->addRow('name','varchar',100);
		$obj_create->addRow('amount','int',11,'0');
		$obj_create->addRow('datetime','int',11);
		$obj_create->addPrimary('id');
		$this->service_QueryBuilder->getResult();
			
		$obj_create	= $this->service_QueryBuilder->getCreate('stats_screenSizes',true);
		$obj_create->addRow('id','int',11,'',false,false,true);
		$obj_create->addRow('width','int',11);
		$obj_create->addRow('height','int',11);
		$obj_create->addRow('amount','int',11,'0');
		$obj_create->addRow('datetime','int',11);
		$obj_create->addPrimary('id')->addIndex('width')->addIndex('height')->addIndex('datetime');
		$this->service_QueryBuilder->getResult();
			
		$obj_create	= $this->service_QueryBuilder->getCreate('stats_unique',true);
		$obj_create->addRow('id','int',11,'',false,false,true);
		$obj_create->addRow('ip','varchar',100);
		$obj_create->addRow('datetime','int',11);
		$obj_create->addPrimary('id');
		$this->service_QueryBuilder->getResult();
		
		/* Countries */
		$obj_create	= $this->service_QueryBuilder->getCreate('countries',true);
		$obj_create->addRow('id','int',11,'',false,false,true);
		$obj_create->addRow('country','varchar',250);
		$obj_create->addPrimary('id');
		$this->service_QueryBuilder->getResult();

		foreach($this->a_countries AS $a_country){
			$a_country = explode(',',$a_country);
			$i_id	= str_replace('"','',$a_country[0]);
			$s_country = str_replace('"','',$a_country[1]);
				
			$this->service_QueryBuilder->insert('countries',array('id','country'),array('i','s'),array($i_id,$s_country))->getResult();
		}

		/* Nationalities */
		$obj_create	= $this->service_QueryBuilder->getCreate('nationalities',true);
		$obj_create->addRow('id','int',11,'',false,false,true);
		$obj_create->addRow('nationality','varchar',250);
		$obj_create->addPrimary('id');
		$this->service_QueryBuilder->getResult();

		foreach($this->a_nationalities AS $a_nationality){
			$a_nationality = explode(',',$a_nationality);
			$i_id	= str_replace('"','',$a_nationality[0]);
			$s_nationality = str_replace('"','',$a_nationality[1]);
			
			$this->service_QueryBuilder->insert('nationalities',array('id','nationality'),array('i','s'),array($i_id,$s_nationality))->getResult();
		}
			
		return true;
	}

	/**
	 * Creates the site tables and data
	 *
	 * @throws DBException If the queries failes
	 */
	private function populateSite(){

	}
	
	/**
	 * Creates the admin user
	 *
	 * @param String	$s_nick			The username
	 * @param String	$s_email		The email address
	 * @param String	$s_password		The plain text password
	 */
	public function createUser($s_nick,$s_email,$s_password){
		try {
			$this->service_QueryBuilder->transaction();
				
			$obj_User	= Memory::models('User')->createUser();
			$obj_User->setUsername($s_nick);
			$obj_User->setEmail($s_email);
			$obj_User->setPassword($s_password);
			$obj_User->enableAccount();
			$obj_User->setLoginType('normal');
			$obj_User->save();

			$this->service_QueryBuilder->update('users','active','s','1')->getWhere()->addAnd('id','i',1);
			$this->service_QueryBuilder->getResult();
			
			$this->service_QueryBuilder->insert('group_users',array('groupID','userid','level'),array('i','i','s'),array(0,1,'2'))->getResult();
			
			$this->service_QueryBuilder->update('group_users','level','s','2')->getWhere()->addAnd('groupID','i',1);
			$this->service_QueryBuilder->getResult();
			
			$this->service_QueryBuilder->insert('users',array('id','nick','bot','active','registrated'),array('i','s','s','s','i'),array(0,'System','1','1',time()))->getResult();
				
			$this->service_QueryBuilder->commit();
			
		}
		catch(Exception $e){
			$this->service_QueryBuilder->rollback();
			
			throw $e;
		}
	}
}
?>
