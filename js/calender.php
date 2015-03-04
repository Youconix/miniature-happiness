<?php
define('NIV', '../');
require (NIV . 'js/generalJS.php');

class JS_Calender extends GeneralJS
{

    private $s_language;

    protected function display()
    {
        $s_months = '';
        for ($i = 1; $i <= 12; $i ++) {
            $s_months .= '"' . $i . '" : "' . $this->service_Language->get('language/months/month' . $i) . '", ';
        }
        
        $this->s_output = '		
		function Calender() {
			this.month;
			this.year;
			this.data = {};
			this.startPos = 0;
			this.months = {' . $s_months . '};
			this.caller = "";
		}
		
		Calender.prototype.setWeekStart	= function(startPos){
			this.startPos = startPos;
		}
		
		Calender.prototype.setMonth	= function(month){
			this.month = month;
		}
		
		Calender.prototype.setYear	= function(year){
			this.year = year;
		}
		
		Calender.prototype.setData	= function(data){
			this.data = data;
		}
		
		Calender.prototype.setCaller	= function(caller){
			this.caller	= caller;
		}
		
		Calender.prototype.increaseMonth = function(){
			this.month++;
			
			if( this.month == 13 ){
				this.month = 1;
				this.year++;
			}
			
			this.display();
		}
		
		Calender.prototype.decreaseMonth = function(){
			this.month--;
			
			if( this.month == 0){
				this.month = 12;
				this.year--;
			}
			
			this.display();
		}
		Calender.prototype.display	= function(){
			daysMonth = getDaysMonth(this.month,this.year);
						
			date = new Date();
			date.setDate(1);
			date.setFullYear(this.year);
			date.setMonth((this.month-1));
			
			startdate = date.getDay() - this.startPos;
			if( startdate < 0 ){	startdate = (7-(startdate*-1));	}
			
			$("#calender_days").empty();
			for(i=0; i<startdate; i++){
				$("#calender_days").append("<li><br/></li>");
			}
			
			for(i=1; i<=daysMonth; i++){
				if( this.data.hasOwnProperty(this.year) && this.data[this.year].hasOwnProperty(this.month) && this.data[this.year][this.month].hasOwnProperty(i) ){
					if( this.caller != "" ){
						$("#calender_days").append(\'<li class="bold" onclick="calender.callback(\'+i+\')">\'+i+\'</li>\');
					}
					else {
						$("#calender_days").append(\'<li class="bold">\'+i+\'</li>\');
					}
				}
				else { 
					$("#calender_days").append("<li>"+i+"</li>");
				}
			}
			
			$("#calender_month").html(this.months[this.month]+" "+this.year);
		}
		
		Calender.prototype.callback	= function(day){
			callback = this.caller+"("+this.month+","+day+","+this.year+")";
			eval(callback);
		}
		
		calender = new Calender();';
        
        echo ($this->s_output);
    }
}

$obj_JS_Calender = new JS_Calender();
unset($obj_JS_Calender);