<?php
namespace core\helpers;

class AdminUserViews extends \core\helpers\Helper {
    protected $language;
    
    protected $template;
    
    protected $groups;
    
    protected $s_modus = 'add';
    
    /**
     * 
     * @var \core\models\data\DataUser
     */
    protected $obj_User;
    
    public function __construct(\Language $language, \Output $template,\core\models\Groups $groups)
    {
        $this->language = $language;
        $this->template = $template;
        $this->groups = $groups;
    }
    
    public function setModus($s_modus){
        if( in_array($s_modus,array('add','view','edit')) ){
            $this->s_modus = $s_modus;
        }
    }
    
    public function run()
    {
        $this->template->set('userid', USERID);
    
        $this->headersGeneral();
        
        if( $this->s_modus == 'add' ){
        	$this->add();
        }
        else if($this->s_modus == 'edit' ){
        	$this->edit();
        }
        
        if( $this->s_modus == 'add' ){
            return;
        }
        
        $this->runView();
        
        $this->checkDeleteOption();
        
        if( $this->s_modus == 'view' ){
            $this->setGroupsView();
            return;
        }
    
        $a_bot = array(
            0 => '',
            1 => ''
        );
        $a_bot[$this->obj_User->isBot()] = 'checked="checked"';
        
        $a_blocked = array(
            0 => '',
            1 => ''
        );
        $a_blocked[$this->obj_User->isBlocked()] = 'checked="checked"';
        
        $this->template->set('bot0', $a_bot[0]);
        $this->template->set('bot1', $a_bot[1]);
        $this->template->set('blocked0', $a_blocked[0]);
        $this->template->set('blocked1', $a_blocked[1]);
        
        $this->setGroupsEdit();
    }
    
    private function runView(){
        $this->template->set('blockedHeader', $this->language->get('system/admin/users/blocked'));
        $this->template->set('loggedinHeader', $this->language->get('system/admin/users/loggedIn'));
        $this->template->set('registratedHeader', $this->language->get('system/admin/users/registrated'));
        $this->template->set('activeHeader', $this->language->get('system/admin/users/activated'));
        $this->template->set('headerText', $this->language->get('system/admin/users/headerView'));
        
        $this->template->set('username', $this->a_data['username']);
        $this->template->set('email', $this->a_data['email']);
        $this->template->set('bot', $this->a_data['bot']);
        $this->template->set('registrated', $this->a_data['registrated']);
        $this->template->set('loggedIn', $this->a_data['loggedin']);
        $this->template->set('active', $this->a_data['active']);
        $this->template->set('blocked', $this->a_data['blocked']);
        $this->template->set('id', $this->a_data['id']);
        
        $this->template->set('edit',$this->language->get('system/buttons/edit'));
        $this->template->set('loginAss','Inloggen als');
    }

    private function checkDeleteOption()
    {
        (USERID == $this->a_data['id']) ? $s_deleteRejected = 'style="color:grey; text-decoration: line-through; cursor:auto"' : $s_deleteRejected = '';
        
        $this->template->set('deleteRejected', $s_deleteRejected);
        $this->template->set('delete', t('system/buttons/delete'));
    }
    
    protected function setGroupsView(){
        $a_groups = $this->obj_User->getGroups();
        
        foreach ($a_groups as $i_id => $i_level) {
            $obj_group = $this->groups->getGroup($i_id);
        
            $this->template->setBlock('userGroup', array(
                'name' => $obj_group->getName(),
                'level' => $this->language->get('system/rights/level_' . $i_level)
            ));
        }
    }

    protected function setGroupsEdit()
    {
        $a_groups = $this->obj_User->getGroups();
        
        $a_currentGroups = array();
        foreach ($a_groups as $i_id => $i_level) {
            $obj_group = $this->groups->getGroup($i_id);
            
            $a_data = array(
                'name' => $obj_group->getName(),
                'level' => $this->language->get('system/rights/level_' . $i_level),
                'levelNr' => $i_level,
                'id' => $obj_group->getID()
            );
            
            if (($obj_group->getID() == 0) && ($this->obj_User->getID() == USERID)) {
                $this->template->setBlock('userGroupBlocked', $a_data);
            } else {
                $this->template->setBlock('userGroup', $a_data);
            }
            
            $a_currentGroups[] = $obj_group->getID();
        }
        
        $a_groups = $this->groups->getGroups();
        foreach ($a_groups as $obj_group) {
            if (in_array($obj_group->getID(), $a_currentGroups)) {
                continue;
            }
            
            $this->template->setBlock('newGroup', array(
                'value' => $obj_group->getID(),
                'text' => $obj_group->getName()
            ));
        }
        
        for ($i = 0; $i <= 2; $i ++) {
            $this->template->setBlock('newLevel', array(
                'value' => $i,
                'text' => $this->language->get('system/rights/level_' . $i)
            ));
        }
    }
    
    private function headersGeneral(){
        $this->template->set('usernameHeader', $this->language->get('system/admin/users/username'));
        $this->template->set('emailHeader', $this->language->get('system/admin/users/email'));
        $this->template->set('headerText', $this->language->get('system/admin/users/headerAdd'));
        $this->template->set('botHeader', $this->language->get('system/admin/users/bot'));
        
        $this->template->set('buttonBack', $this->language->get('system/buttons/back'));
        
        $this->template->set('no', $this->language->get('system/admin/users/no'));
        $this->template->set('yes', $this->language->get('system/admin/users/yes'));
    }
    
    private function add(){
    	$this->template->set('usernameError','De gebruikersnaam is niet ingevuld');
    	$this->template->set('saveButton',$this->language->get('system/buttons/save'));
    	
    	$this->template->set('passwordHeader','Wachtwoord');
    	$this->template->set('passwordRepeatHeader','Wachtwoord herhalen');
    	$this->template->set('passwordError','Het wachtwoord is niet ingevuld');
    	$this->template->set('emailError','Het E-mail adres is niet ingevuld of ongeldig');
    }
    
    private function edit(){
    	if( $this->obj_User->getLoginType() == 'normal' ){
    		$this->template->displayPart('passwords');
    	
    		$this->template->set('passwordChangeHeader','Wachtwoord veranderen');
    		$this->template->set('passwordChangeText','Laat leeg om de wachtwoorden hetzelfde te laten');
    		$this->template->set('passwordHeader','Wachtwoord');
    		$this->template->set('passwordRepeatHeader','Wachtwoord herhalen');
    		$this->template->set('passwordError','Het wachtwoord is niet ingevuld');
    	}
    	$this->template->set('emailError','Het E-mail adres is niet ingevuld of ongeldig');
    	$this->template->set('updateButton',$this->language->get('system/buttons/update'));
    }
    
    public function setData($obj_User)
    {
        $this->obj_User = $obj_User;
    
        $s_yes = $this->language->get('system/admin/users/yes');
        $s_no = $this->language->get('system/admin/users/no');
    
        $this->a_data = array(
            'id' => $obj_User->getID(),
            'username' => $obj_User->getUsername(),
            'email' => $obj_User->getEmail(),
            'bot' => ($obj_User->isBot() ? $s_yes : $s_no),
            'registrated' => ($obj_User->getRegistrated() == 0) ? '-' : $this->a_data['registrated'] = date('d-m-Y H:i', $obj_User->getRegistrated()),
            'loggedin' => ($obj_User->lastLoggedIn() == 0) ? '-' : $this->a_data['loggedin'] = date('d-m-Y H:i', $obj_User->lastLoggedIn()),
            'active' => ($obj_User->isEnabled()) ? $s_yes : $s_no,
            'blocked' => ($obj_User->isBlocked()) ? $s_yes : $s_no
        );
    }
}