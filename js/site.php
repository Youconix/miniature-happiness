<?php 
define('NIV','../');
require(NIV.'js/generalJS.php');

class Site extends GeneralJS {
	private $s_language;
	
	public function __construct(){
		require(NIV.'include/Memory.php');
		Memory::startUp();
		
		$this->service_Language	= Memory::services('Language');
		$this->s_language	= $this->service_Language->getLanguage();
		$this->sendHeaders();
		
		$this->display();
	}
	
	protected function display(){
		$s_file = 'function Site(){        		
			Site.prototype.checkMessage	= function(){
				var receiver	= $.trim($("#receiver").val());
				var subject		= $.trim($("#subject").val());
				var message		= $.trim($("#message").val());
				error	= "";
				
				if( receiver == "" ){
					error	+= "'.$this->service_Language->get('language/messages/notices/noReceiver').'<br/>";
				}
				if( subject == "" ){
					error	+= "'.$this->service_Language->get('language/messages/notices/noSubject').'<br/>";
				}
				if( message	== "" ){
					error	+= "'.$this->service_Language->get('language/messages/notices/noMessage').'<br/>";
				}
				
				if( error != "" ){
					$("#errorNotice").html(error);
					return false;
				}
				
				return true;
			}
			
			Site.prototype.confirmMessageDelete	= function(){
				if( confirm("'.$this->service_Language->get('language/messages/notices/deleteConfirm').'") ){
					return true;
				}
				return false;
			}
		}
		
		var site	= new Site();
		$(document).ready(function() {
        	site.show();
        });
        
        /* Check session status */
		$(document).ready(function() {
			$(document).ajaxError(function(event, jqxhr, settings, exception) {
				if( jqxhr.status == 401 ){
					/* Session expired */					
					var div = document.createElement("div");
					$(div).attr("id","ajaxLoginBox")
						.html(\'<a href="javascript:removeAuthButton()">'.$this->service_Language->get('language/sessionExpired').'</a>\')
						.appendTo($("body"));
				}
				else if( jqxhr.status == 403 ){
                    window.location.href = "'.Memory::getBase().'errors/403.php";
                }
			});
        });
        
        function removeAuthButton(){
        	if( $("#ajaxLoginBox").length > 0 ){
        		$("#ajaxLoginBox").remove();
        		window.open("'.Memory::getBase().'login.php?ajaxLogin=true", "session", config="height=350,width=1100,toolbar=no,menubar=no,location=no,directories=no,status=no,left:40%,top:40%");
        	}        	        	
        }
        ';
		
		echo($s_file);
	}
}

$obj_Site	= new Site();
unset($obj_Site);
?>
