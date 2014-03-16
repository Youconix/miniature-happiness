/* Get stats */
function loadStats() {
	var urlImage = "stats/stats.php";
	var screenSize = document.documentElement.clientHeight + 'X'
			+ document.documentElement.clientWidth;
	var screenColors = window.screen.colorDepth;

	urlImage = urlImage + "?ss=" + screenSize + "&sc=" + screenColors;

	document.getElementById("imageValid").src = urlImage;
	document.getElementById("imageValid").style.display = "inline";
}

/* String extensions */
String.prototype.trim = function() {
	return this.replace(/^\s+|\s+$/g, '');
}
String.prototype.endsWith = function(str) {
	var lastIndex = this.lastIndexOf(str);
	return (lastIndex != -1) && (lastIndex + str.length == this.length);
}

String.prototype.startsWith = function(str) {
	var firstIndex = this.firstIndexOf(str);
	return (firstIndex != -1) && (firstIndex + str.length == this.length);
}
/* Array functions */
function arraySearch(arrayName, value) {
	for (i = 0; i < arrayName.length; i++) {
		if (arrayName[i] == value)
			return i;
		else if (arrayName[i].indexOf(value) != -1)
			return i;
	}

	return -1;
}
function arrayDelete(arrayName, value) {
	var arrayDouble = new Array();

	j = 0;
	for (i = 0; i < arrayName.length; i++) {
		if (arrayName[i] == value)
			continue;

		arrayDouble[j] = arrayName[i];
		j++;
	}

	return arrayDouble;
}
function arrayDeleteI(arrayName, index) {
	var arrayDouble = new Array();

	j = 0;
	for (i = 0; i < arrayName.length; i++) {
		if (i == index)
			continue;

		arrayDouble[j] = arrayName[i];
		j++;
	}

	return arrayDouble;
}

function validateBoolean(value){
	if( value == "0" || value == "1" )
		return true;
	
	return false;
}

function checkFutureDate(day,month,year){
	var check	= new Date(year,month-1,day);
	
	var date	= new Date();
	var dif		= (check.getTime() - date.getTime());
	
	if( dif <= 0 ){
		return false;
	}
	
	return true;
}

function validateDate(day, month, year) {
	if( isNaN(day) || isNaN(month) || isNaN(year) )
		return false;
	
	if( month < 1 || month > 12 )
		return false;
	
	days	= getDaysMonth(month,year);
	
	if( day < 1 || day > days ){
		return false;
	}
		
	return true;
}

function getDaysMonth(month, year) {	
	if( month == 1 || month == 3 || month == 5 || month == 7 || month == 8 || month == 10 || month == 12 ){
		return 31;
	}
	
	if( month == 4 || month == 6 || month == 9 || month == 11 ){	
		return 30;
	}
	
	if (year % 400 == 0)
		return 29;
	if (year % 100 == 0)
		return 28;
	if (year % 4 == 0)
		return 29;
	
	return 28;
}



function encode(text){
	text = text.replace(/“/g,"&#8220;").replace(/”/g,"&#8221;");
	text = text.replace(/‘/g, "'");

	return encodeURI(text);
}

function shuffle(data) {
    for (var i = data.length - 1; i > 0; i--) {
        var j = Math.floor(Math.random() * (i + 1));
        var tmp = data[i];
        data[i] = data[j];
        data[j] = tmp;
    }

    return data;
}
function sortDroplist(id){
	if( $("#"+id).length == 0 ){	return;		}
	var selected = $("#"+id).val();
	items = [];
	options = $("option","#"+id);
	options.each(function(){ 
		items.push({"val" : $(this).val(),"text":$(this).text() });
	});
	items.sort(function(a,b){	
		a = a.val.toLowerCase();
		b = b.val.toLowerCase(); 
	
		if( a > b ){ 
			return 1; 
		}
		else if( a == b ){	
			return 0; 
		}
		else {	
			return -1; 
		}
	});
	for(i=0; i<items.length; i++){
		$(options[i]).val(items[i].val).text(items[i].text);
	}
	$("#"+id).val(selected);
}

function encodeChar(srcTxt){	
	srcTxt = uni2ent( $.trim(srcTxt) );
	srcTxt = encodeURI(srcTxt);
	return (srcTxt);
}

/* Encodes unicode characters
 * Found on : http://www.codeproject.com/Articles/34481/Posting-Unicode-Characters-via-AJAX
 */
function uni2ent(srcTxt) {
	var entTxt = '';
	var c, hi, lo;
	var len = 0;
	for (var i=0, code; code=srcTxt.charCodeAt(i); i++) {
		var rawChar = srcTxt.charAt(i);
	    // needs to be an HTML entity
	    if (code > 255) {
	      // normally we encounter the High surrogate first
	      if (0xD800 <= code && code <= 0xDBFF) {
	        hi  = code;
	        lo = srcTxt.charCodeAt(i+1);
	        // the next line will bend your mind a bit
	        code = ((hi - 0xD800) * 0x400) + (lo - 0xDC00) + 0x10000;
	        i++; // we already got low surrogate, so don't grab it again
	      }
	      // what happens if we get the low surrogate first?
	      else if (0xDC00 <= code && code <= 0xDFFF) {
	        hi  = srcTxt.charCodeAt(i-1);
	        lo = code;
	        code = ((hi - 0xD800) * 0x400) + (lo - 0xDC00) + 0x10000;
	      }
	      // wrap it up as Hex entity
	      c = "" + code.toString(16).toUpperCase() + ";";
	    }
	    else {
	      c = rawChar;
	    }
	    entTxt += c;
	    len++;
	}
	return entTxt;
}

function firstToUpper(text){
	return text.charAt(0).toUpperCase() + text.slice(1);
}