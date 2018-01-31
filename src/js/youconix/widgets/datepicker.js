Date.prototype.getMonthsDay = function(){
  var d = new Date(this.getFullYear(),this.getMonth()+1,0);
  
  return d.getDate();
};

class DatePicker{
  constructor(){
    this.resultField;
    this.field;
    this.form;
    this.date;
    this.day = 0;

    this.format = '{d}-{m}-{Y}';
  }
  bind(field){
    this.field = $('#'+field);  
    let name = this.form.prop('id').replace('datepicker_','');

    this.field.css('display','none');
    this.field.before('<span id="datepicker_'+name+'_result" class="datepicker_result"></span>');
    this.resultField = $('#datepicker_'+name+'_result');

    this.show();
    this.save();

    this.bindFields();

    this.resultField.click(() => {
      this.showPicker();
    });
  }
  bindFields(){
    this.field.on('blur',() => {
      this.updateSavedDate();    
      this.saveDate();
      this.displayDate();
    });
  }
  init(name){
    this.form = $('#datepicker_'+name);
    this.date = new Date();

    this.setCalenderEvents();
  }
  setFormat(format){
    this.format = format;
  }
  setCalenderEvents(){
    this.form.find('.datepicker_left').click(() => {
      let month = this.date.getMonth();
      month--;
      if( month === -1 ){
	this.date.setFullYear( (this.date.getFullYear() -1) );
	this.date.setMonth(11);
      }
      else {
	this.date.setMonth(month);
      }

      this.setYearBar();
      this.show();
    });

    this.form.find('.datepicker_right').click(() => {
      let month = _this.date.getMonth();
      month++;
      if( month === 12 ){
	this.date.setFullYear( (this.date.getFullYear() + 1) );
	this.date.setMonth(0);
      }
      else {
	this.date.setMonth(month);
      }

      this.setYearBar();
      this.show();
    });

    this.form.find('.datepicker_oke').click(() => {
      this.save();
    });
    this.form.find('.datepicker_cancel').click(() => {
      _this.close();
    });
  };
  setYearBar(){
    let month = monthNames[this.date.getMonth()];

    this.form.find('.datepicker_month_year').html(month+' '+this.date.getFullYear());
  }
  createDays(){
    this.form.find('.datepicker_days span').each((i, item) => {
      $(item).off('click');
    });
    this.form.find('.datepicker_days').empty();
  
    let preDate = new Date();
    preDate.setDate(1);
    preDate.setMonth(this.date.getMonth());
    preDate.setYear(this.date.getYear());
    let pre = preDate.getDay();
    
    for(let i=0; i<pre; i++){
      this.form.find('.datepicker_days').append('<span class="datepicker_filler"></span>');
    }
  
    let days = this.date.getMonthsDay();
    for(let i=1; i<=days; i++){
      this.form.find('.datepicker_days').append('<span class="datepicker_day">'+i+'</span>');
    }
  
    this.form.find('.datepicker_days').find('.datepicker_day').each((i, item) => {
      $(item).click((day) => {
	this.setDay($(day).html());
      });
    });
  }
  show(){
    this.setYearBar();

    this.createDays();

    this.highLightCurrentDay();
  }
  setDay(value){
    this.date.setDate(value);

    this.highLightCurrentDay();
  }
  highLightCurrentDay(){
    let day = this.date.getDate();

    this.form.find('.datepicker_days span').each((i,item) => {
	item = $(item);
	if( parseInt(item.html()) === day ){
	  item.addClass('datepicker_day_selected');
	}
	else {
	  item.removeClass('datepicker_day_selected');
	}
    });
  }
  updateSavedDate(){
    let value = this.field.val();
  
    if( value !== '' ){
      this.date = new Date();
      value = value.split(' ');
      let dates = value[0].split('-');
      this.date.setFullYear(dates[0]);
      this.date.setMonth((dates[1])-1);
      this.date.setDate(dates[2]);

      if (value.length > 1) {
	let times = value[1].split(':');
	this.date.setHours(times[0]);
	this.date.setMinutes(times[1]);
	this.date.setSeconds(times[2]);
      }
    }
  }
  showPicker(){
    $('.datepicker').each((i, item) => {
      $(item).css('display','none');
    });

    this.updateSavedDate();

    let position = this.resultField.offset();

    this.form.css('top',(position.top - 40)+'px');
    this.form.css('left',(position.left-50)+'px');

    this.show();

    this.highLightCurrentDay();

    this.form.css('display','block');
  }
  saveDate(){
    let month = (this.date.getMonth()+1);
    let day = this.date.getDate();

    if( month < 10 ){
      month = '0'+month;
    }
    if( day < 10 ){
      day = '0'+day;
    }

    let result = this.date.getFullYear()+'-'+month+'-'+day;
    this.field.val(result);
  }
  save(){
    this.saveDate();

    let output = this.format.replace('{Y}',this.date.getFullYear()).replace('{m}',(this.date.getMonth()+1)).replace('{d}',this.date.getDate());
    this.resultField.html(output);

    this.close();
  }
  close(){
    this.form.css('display','none');
  }
  displayDate(){
    let output = this.format.replace('{Y}',this.date.getFullYear()).replace('{m}',(this.date.getMonth()+1)).replace('{d}',this.date.getDate());
    this.resultField.html(output);

    this.close();
  }
}

class DateTimePicker extends DatePicker{
  constructor(){
    this.resultField;
    this.field;
    this.form;
    this.date;
    this.day = 0;

    this.format = '{d}-{m}-{Y} {H}:{i}:{s}';
  }
  init(name){
    this.form = $('#datepicker_'+name);
    this.date = new Date();
    this.setCalenderEvents();

    this.form.find('.timepicker_hour_up').click(() => {
      this.manipulareHour(1);
    });
    this.form.find('.timepicker_hour_down').click(() => {
      this.manipulareHour(-1);
    })
    this.form.find('.timepicker_minute_up').click(() => {
      this.manipulateMinuteSecond('timepicker_minute',1);
    });
    this.form.find('.timepicker_minute_down').click(() => {
      this.manipulateMinuteSecond('timepicker_minute',-1);
    });
    this.form.find('.timepicker_second_up').click(() => {
      this.manipulateMinuteSecond('timepicker_second',1);
    });
    this.form.find('.timepicker_second_down').click(() => {
      this.manipulateMinuteSecond('timepicker_second',-1);
    });
  }
  bindFields(){
    this.field.on('blur',() => {
      this.updateSavedDate();    

      let hours = this.date.getHours();
      let minutes = this.date.getMinutes();
      let seconds = this.date.getSeconds();

      this.setFieldDate(hours,minutes,seconds);

      this.displayDate();
    });
  }
  show(){
    this.setYearBar();
    this.createDays();

    this.form.find('.timepicker_hour').html( this.date.getHours() );
    this.form.find('.timepicker_minute').html( this.date.getMinutes() );
    this.form.find('.timepicker_second').html( this.date.getSeconds());
  }
  manipulareHour(extraHour){
    let hourField = this.form.find('.timepicker_hour');
    let hour = parseInt(hourField.html());
    hour += extraHour;

    if( hour === 24 ){
      hour = 0;
    }
    else if( hour === -1 ){
      hour = 23;
    }

    hourField.html(hour);
  }
  manipulateMinuteSecond(fieldName,extra){
    let field = this.form.find('.'+fieldName);
    let time = parseInt(field.html());
    time += extra;

    if( time === 60 ){
      time = 0;
    }
    else if( time === -1 ){
      time = 59;
    }

    field.html(time);
  }
  save(){
    let hours = parseInt(this.form.find('.timepicker_hour').html());
    let minutes = parseInt(this.form.find('.timepicker_minute').html());
    let seconds = parseInt(this.form.find('.timepicker_second').html());

    this.setFieldDate(hours,minutes,seconds);
  }
  setFieldDate(hours,minutes,seconds){
    this.date.setHours(hours);
    this.date.setMinutes(minutes);
    this.date.setSeconds(seconds);

    if( hours < 10 ){
      hours = '0'+hours;
    }
    if( minutes < 10 ){
      minutes = '0'+minutes;
    }
    if( seconds < 10 ){
      seconds = '0'+seconds;
    }
    let month = (this.date.getMonth()+1);
    if( month < 10 ){
      month = '0'+month;
    }

    let days = this.date.getDate();
    if( days < 10 ){
      days = '0'+days;
    }

    let result = this.date.getFullYear()+'-'+month+'-'+days+' ';
    result += hours+':'+minutes+':'+seconds;
    this.field.val(result);

    this.displayDate();
  }
  displayDate(){
    let hours = this.date.getHours();
    let minutes = this.date.getMinutes();
    let seconds = this.date.getSeconds();

    if( hours < 10 ){
      hours = '0'+hours;
    }
    if( minutes < 10 ){
      minutes = '0'+minutes;
    }
    if( seconds < 10 ){
      seconds = '0'+seconds;
    }


    let output = this.format.replace('{Y}',this.date.getFullYear()).replace('{m}',(this.date.getMonth()+1)).replace('{d}',this.date.getDate());
    output = output.replace('{H}',hours).replace('{i}',minutes).replace('{s}',seconds);
    this.resultField.html(output);

    this.close();
  }
}