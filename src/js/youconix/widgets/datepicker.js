Date.prototype.getMonthsDay = function(){
  var d = new Date(this.getFullYear(),this.getMonth()+1,0);
  
  return d.getDate();
};

function DatePicker(){
  this.resultField;
  this.field;
  this.form;
  this.date;
  this.day = 0;
  
  this.format = '{d}-{m}-{Y}';
}
DatePicker.prototype.bind = function(field){
  this.field = $('#'+field);  
  var name = this.form.prop('id').replace('datepicker_','');
  
  this.field.css('display','none');
  this.field.before('<span id="datepicker_'+name+'_result" class="datepicker_result"></span>');
  this.resultField = $('#datepicker_'+name+'_result');
  
  this.show();
  this.save();
  
  var _this = this;
  
  this.bindFields();
  
  this.resultField.click(function(){
    _this.showPicker();
  });
};
DatePicker.prototype.bindFields = function(){
  var _this = this;
  
  this.field.on('blur',function(){
    _this.updateSavedDate();    
    _this.saveDate();
    _this.displayDate();
  });
};
DatePicker.prototype.init = function(name){
  this.form = $('#datepicker_'+name);
  this.date = new Date();
  
  this.setCalenderEvents();
};
DatePicker.prototype.setFormat = function(format){
  this.format = format;
};
DatePicker.prototype.setCalenderEvents = function(){
  var _this = this;
  
  this.form.find('.datepicker_left').click(function(){
    var month = _this.date.getMonth();
    month--;
    if( month === -1 ){
      _this.date.setFullYear( (_this.date.getFullYear() -1) );
      _this.date.setMonth(11);
    }
    else {
      _this.date.setMonth(month);
    }
    
    _this.setYearBar();
    _this.show();
  });
  
  this.form.find('.datepicker_right').click(function(){
    var month = _this.date.getMonth();
    month++;
    if( month === 12 ){
      _this.date.setFullYear( (_this.date.getFullYear() + 1) );
      _this.date.setMonth(0);
    }
    else {
      _this.date.setMonth(month);
    }
    
    _this.setYearBar();
    _this.show();
  });
  
  this.form.find('.datepicker_oke').click(function(){
    _this.save();
  });
  this.form.find('.datepicker_cancel').click(function(){
    _this.close();
  });
};
DatePicker.prototype.setYearBar = function(){
  var month = monthNames[this.date.getMonth()];
  
  this.form.find('.datepicker_month_year').html(month+' '+this.date.getFullYear());
};
DatePicker.prototype.createDays = function(){
  this.form.find('.datepicker_days span').each(function(){
    $(this).off('click');
  });
  this.form.find('.datepicker_days').empty();
  
  var preDate = new Date();
  preDate.setDate(1);
  preDate.setMonth(this.date.getMonth());
  preDate.setYear(this.date.getYear());
  var pre = preDate.getDay();
  var i;
  
  for(i=0; i<pre; i++){
    this.form.find('.datepicker_days').append('<span class="datepicker_filler"></span>');
  }
  
  var days = this.date.getMonthsDay();
  for(i=1; i<=days; i++){
    this.form.find('.datepicker_days').append('<span class="datepicker_day">'+i+'</span>');
  }
  
  var _this = this;
  this.form.find('.datepicker_days').find('.datepicker_day').each(function(){
    $(this).click(function(){
      _this.setDay($(this).html());
    });
  });
};
DatePicker.prototype.show = function(){
  this.setYearBar();
  
  this.createDays();
  
  this.highLightCurrentDay();
};
DatePicker.prototype.setDay = function(value){
  this.date.setDate(value);
  
  this.highLightCurrentDay();
};
DatePicker.prototype.highLightCurrentDay = function(){
  var day = this.date.getDate();
  
  this.form.find('.datepicker_days span').each(function(){
      if( parseInt($(this).html()) === day ){
	$(this).addClass('datepicker_day_selected');
      }
      else {
	$(this).removeClass('datepicker_day_selected');
      }
  });
};
DatePicker.prototype.updateSavedDate = function(){
  var value = this.field.val();
  
  if( value !== '' ){
	this.date = new Date();
    value = value.split(' ');
    var dates = value[0].split('-');
	this.date.setFullYear(dates[0]);
	this.date.setMonth((dates[1])-1);
	this.date.setDate(dates[2]);
	
	if (value.length > 1) {
		var times = value[1].split(':');
		this.date.setHours(times[0]);
		this.date.setMinutes(times[1]);
		this.date.setSeconds(times[2]);
	}
  }
};
DatePicker.prototype.showPicker = function(){
  $('.datepicker').each(function(){
    $(this).css('display','none');
  });
  
  this.updateSavedDate();
  
  var position = this.resultField.offset();
  
  this.form.css('top',(position.top - 40)+'px');
  this.form.css('left',(position.left-50)+'px');
  
  this.show();
  
  this.highLightCurrentDay();
  
  this.form.css('display','block');
};
DatePicker.prototype.saveDate = function(){
  var month = (this.date.getMonth()+1);
  var day = this.date.getDate();
  
  if( month < 10 ){
    month = '0'+month;
  }
  if( day < 10 ){
    day = '0'+day;
  }
  
  var result = this.date.getFullYear()+'-'+month+'-'+day;
  this.field.val(result);
};
DatePicker.prototype.save = function(){
  this.saveDate();
  
  var output = this.format.replace('{Y}',this.date.getFullYear()).replace('{m}',(this.date.getMonth()+1)).replace('{d}',this.date.getDate());
  this.resultField.html(output);
  
  this.close();
};
DatePicker.prototype.close = function(){
  this.form.css('display','none');
};
DatePicker.prototype.displayDate = function(){
  var output = this.format.replace('{Y}',this.date.getFullYear()).replace('{m}',(this.date.getMonth()+1)).replace('{d}',this.date.getDate());
  this.resultField.html(output);
  
  this.close();
};

function DateTimePicker(){
  this.resultField;
  this.field;
  this.form;
  this.date;
  this.day = 0;
  
  this.format = '{d}-{m}-{Y} {H}:{i}:{s}';
}
DateTimePicker.prototype = new DatePicker();
DateTimePicker.prototype.constructor = DateTimePicker;
DateTimePicker.prototype.init = function(name){
  this.form = $('#datepicker_'+name);
  this.date = new Date();
  
  this.setCalenderEvents();
  
  var _this = this;
  this.form.find('.timepicker_hour_up').click(function(){
    _this.manipulareHour(1);
  });
  this.form.find('.timepicker_hour_down').click(function(){
    _this.manipulareHour(-1);
  });
  this.form.find('.timepicker_minute_up').click(function(){
    _this.manipulateMinuteSecond('timepicker_minute',1);
  });
  this.form.find('.timepicker_minute_down').click(function(){
    _this.manipulateMinuteSecond('timepicker_minute',-1);
  });
  this.form.find('.timepicker_second_up').click(function(){
    _this.manipulateMinuteSecond('timepicker_second',1);
  });
  this.form.find('.timepicker_second_down').click(function(){
    _this.manipulateMinuteSecond('timepicker_second',-1);
  });
};
DateTimePicker.prototype.bindFields = function(){
  var _this = this;
  
  this.field.on('blur',function(){
    _this.updateSavedDate();    

    var hours = _this.date.getHours();
    var minutes = _this.date.getMinutes();
    var seconds = _this.date.getSeconds();
    
    _this.setFieldDate(hours,minutes,seconds);

     _this.displayDate();
  });
};
DateTimePicker.prototype.show = function(){
  this.setYearBar();
  
  this.createDays();
  
  this.form.find('.timepicker_hour').html( this.date.getHours() );
  this.form.find('.timepicker_minute').html( this.date.getMinutes() );
  this.form.find('.timepicker_second').html( this.date.getSeconds());
};
DateTimePicker.prototype.manipulareHour = function(extraHour){
  var hourField = this.form.find('.timepicker_hour');
  var hour = parseInt(hourField.html());
  hour += extraHour;
  
  if( hour === 24 ){
    hour = 0;
  }
  else if( hour === -1 ){
    hour = 23;
  }
  
  hourField.html(hour);
};
DateTimePicker.prototype.manipulateMinuteSecond = function(fieldName,extra){
  var field = this.form.find('.'+fieldName);
  var time = parseInt(field.html());
  time += extra;
  
  if( time === 60 ){
    time = 0;
  }
  else if( time === -1 ){
    time = 59;
  }
  
  field.html(time);
};
DateTimePicker.prototype.save = function(){
  var hours = parseInt(this.form.find('.timepicker_hour').html());
  var minutes = parseInt(this.form.find('.timepicker_minute').html());
  var seconds = parseInt(this.form.find('.timepicker_second').html());
  
  this.setFieldDate(hours,minutes,seconds);
};
DateTimePicker.prototype.setFieldDate = function(hours,minutes,seconds){
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
  var month = (this.date.getMonth()+1);
  if( month < 10 ){
    month = '0'+month;
  }
    
  var days = this.date.getDate();
  if( days < 10 ){
    days = '0'+days;
  }
    
  var result = this.date.getFullYear()+'-'+month+'-'+days+' ';
  result += hours+':'+minutes+':'+seconds;
  this.field.val(result);
  
  this.displayDate();
};
DateTimePicker.prototype.displayDate = function(){
  var hours = this.date.getHours();
  var minutes = this.date.getMinutes();
  var seconds = this.date.getSeconds();
  
  if( hours < 10 ){
    hours = '0'+hours;
  }
  if( minutes < 10 ){
    minutes = '0'+minutes;
  }
  if( seconds < 10 ){
    seconds = '0'+seconds;
  }
  
  
  var output = this.format.replace('{Y}',this.date.getFullYear()).replace('{m}',(this.date.getMonth()+1)).replace('{d}',this.date.getDate());
  output = output.replace('{H}',hours).replace('{i}',minutes).replace('{s}',seconds);
  this.resultField.html(output);
  
  this.close();
};
