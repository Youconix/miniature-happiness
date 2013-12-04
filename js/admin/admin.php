<?php 
define('NIV','../../');
require(NIV.'js/generalJS.php');

class Admin extends GeneralJS {
	private $s_language;
	
	protected function display(){
		$this->s_language	= $this->service_Language->getLanguage();
		
		$s_file = 'function Admin(){
			
			Admin.prototype.init	= function(){
				var	files	= new Array("groups","users","logs","maintenance","stats","settings");
				var head	= document.getElementsByTagName(\'head\')[0];
				
				var script;
				for(i in files){
					script	= document.createElement(\'script\');
					script.src	= \'../js/admin/\'+files[i]+\'.php?lang='.$this->s_language.'\';
					head.appendChild(script);
				}
							
				var files	= new Array("text");

				for(i in files){
					script	= document.createElement(\'script\');
					script.src	= \'../js/admin/\'+files[i]+\'.js\';
					head.appendChild(script);
				}
							
				this.checkUpdates();
			}
							
			Admin.prototype.checkUpdates	= function(){
				var _this	= this;
				$.get("software.php",{"AJAX" : "true"},_this.checkUpdatesResult);
			}
							
			Admin.prototype.checkUpdatesResult	= function(response){
				$("#updateScreen").html(response);		
			}
							
			Admin.prototype.update	= function(){
				var _this	= this;
				$.get("software.php",{"AJAX" : "true"},_this.checkUpdatesResult);			
			}
		}
		
		var admin	= new Admin();
		admin.init();
		
		function AdminMain(){
			AdminMain.prototype.loadScreen	= function(response){
				if( response.request ){
					response = response.request.responseText;
				}
				$("#adminContent").html(response);
			}
		}';
		
		echo($s_file);
	}
}

$obj_Admin	= new Admin();
unset($obj_Admin);
?>