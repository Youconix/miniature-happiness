<?php 
define('NIV','../../');
require(NIV.'js/generalJS.php');

class AdminStats extends GeneralJS {
	protected function display(){
		$s_file = 'AdminStats.prototype = new AdminMain();
		AdminStats.prototype.constructor = AdminStats;
				
		function AdminStats(){
			this.name	= "stats";
				
			AdminStats.prototype.view	= function(){
				this.view2(-1,-1);
			}
				
			AdminStats.prototype.view2	= function(month,year){
				var _this   = this;
				$.get(this.name+".php",{"AJAX":"true","command":"index","month":month,"year":year},_this.loadScreen);
			}
				
			AdminStats.prototype.viewOS	= function(month,year){
				var _this   = this;		        
				$.get(this.name+".php",{"AJAX":"true","command":"OS","month":month,"year":year},_this.loadScreen);
			}
				
			AdminStats.prototype.viewSizes	= function(month,year){
				var _this   = this;
				$.get(this.name+".php",{"AJAX":"true","command":"sizes","month":month,"year":year},_this.loadScreen);
			}
				
			AdminStats.prototype.viewBrowsers	= function(month,year){
				var _this   = this;
				$.get(this.name+".php",{"AJAX":"true","command":"browsers","month":month,"year":year},_this.loadScreen);
			}
		}
		
		var adminStats   = new AdminStats();';
		echo($s_file);
	}
}

$obj_AdminStats	 = new AdminStats();
unset($obj_AdminStats);
?>