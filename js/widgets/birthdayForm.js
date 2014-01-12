function BirthdayForm(){
	this.callback   = null;
	this.dayID = null;
	this.monthID = null;
	this.yearID = null;
}
BirthdayForm.prototype.init	= function(dayID,monthID,yearID,callback){
	this.dayID		= dayID;
	this.monthID	= monthID;
	this.yearID		= yearID;
	this.callback	= callback;
	
	var _this = this;
	$("#"+dayID).on("change",function(){ _this.setBirthDay(); });
	$("#"+monthID).on("change",function(){ _this.setBirthMonth(); });
	$("#"+yearID).on("change",function(){ _this.setBirthYear(); });
}
BirthdayForm.prototype.setBirthDay  = function(){
	this.day    = $("#"+this.dayID).val();	
	this.check();
}            
BirthdayForm.prototype.setBirthMonth  = function(){
	this.month    = $("#"+this.monthID).val();
	this.check();
}            
BirthdayForm.prototype.setBirthYear  = function(){
	this.year    = $("#"+this.yearID).val();
	this.check();
}
            
BirthdayForm.prototype.check    = function(){
	if( this.validate() ){
		return;
	}
					
	if( this.callback != "" ){
		var call    = this.callback;
		eval(call+"("+this.day+","+this.month+","+this.year+")");
	}
}
           
BirthdayForm.prototype.validate  = function(){
	$("#"+this.dayID).removeClass("invalid");
	$("#"+this.monthID).removeClass("invalid");
	$("#"+this.yearID).removeClass("invalid");
	
	if( this.day == -1 || this.month == -1 || this.year == -1){
		return false;
	}
	
	birthdate = this.get();
			
	if( !validateDate(birthdate.day,birthdate.month, birthdate.year) ){
		$("#"+this.dayID).addClass("invalid");	
		$("#"+this.monthID).addClass("invalid");
		$("#"+this.yearID).addClass("invalid");
	}

	return true;
}

BirthdayForm.prototype.validateDifference  = function(months,days,years,future){
	if( !this.validate() ){
		return false;
	}
	
	birthdate = this.get();
			
	var check	= new Date(birthdate.year,(birthdate.month-1),birthdate.day);
	var date	= new Date();
	var allowedDate	= new Date((date.getFullYear()-years),(date.getMonth()-months),(date.getDate()-days));
	
	var dif		= (allowedDate.getTime() - check.getTime());
	if( (!future && dif < 0) || (future && dif > 0) ){
		alert("error 2");
		$("#"+this.dayID).addClass("invalid");	
		$("#"+this.monthID).addClass("invalid");
		$("#"+this.yearID).addClass("invalid");
		return false;
	}
	
	return true;
}
					
BirthdayForm.prototype.get  = function(){
	data = {"day":parseInt($("#"+this.dayID).val()),"month":parseInt($("#"+this.monthID).val()),"year":parseInt($("#"+this.yearID).val())};
	return data;			
}