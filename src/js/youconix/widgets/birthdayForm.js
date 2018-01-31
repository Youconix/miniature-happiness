class BirthdayForm{
  constructor(){
    this.callback   = null;
    this.dayID = null;
    this.monthID = null;
    this.yearID = null;
  }
  init(dayID,monthID,yearID,callback){
    this.dayID = dayID;
    this.monthID = monthID;
    this.yearID = yearID;
    this.callback = callback;
	
    $("#"+dayID).on("change",() => {this.setBirthDay(); });
    $("#"+monthID).on("change",() => { this.setBirthMonth(); });
    $("#"+yearID).on("change",() => { _this.setBirthYear(); });
  }
  setBirthDay(){
    this.day = $("#"+this.dayID).val();	
    this.check();
  }
  setBirthMonth(){
    this.month    = $("#"+this.monthID).val();
    this.check();
  }
  setBirthYear(){
    this.year = $("#"+this.yearID).val();
    this.check();
  }
  check(){
    if( this.validate() ){
      return;
    }
					
    if( this.callback != "" ){
      let call    = this.callback;
      eval(call+"("+this.day+","+this.month+","+this.year+")");
    }
  }           
  validate(){
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
  validateDifference(months,days,years,future){
    if( !this.validate() ){
      return false;
    }
	
    birthdate = this.get();
			
    let check = new Date(birthdate.year,(birthdate.month-1),birthdate.day);
    let date = new Date();
    let allowedDate = new Date((date.getFullYear()-years),(date.getMonth()-months),(date.getDate()-days));
	
    let dif = (allowedDate.getTime() - check.getTime());
    if( (!future && dif < 0) || (future && dif > 0) ){
      $("#"+this.dayID).addClass("invalid");	
      $("#"+this.monthID).addClass("invalid");
      $("#"+this.yearID).addClass("invalid");
      return false;
    }
	
    return true;
  }
  get(){
    let data = {"day":parseInt($("#"+this.dayID).val()),"month":parseInt($("#"+this.monthID).val()),"year":parseInt($("#"+this.yearID).val())};
    return data;			
  }
}