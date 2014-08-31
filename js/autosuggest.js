/**
 * Autosuggest class
 * Based on : http://www.brandspankingnew.net/archive/2006/08/ajax_auto-suggest_auto-complete.html
 */
function Autosuggest(){
	this.file	= null;
	this.field		= null;
	
	this.suggestions	= [];
	this.input	= "";
	this.inputCharacters	= 0;
	this.highlighted	= 0;
	this.parameters	= {};
	
	/* Set keys */
	this.RETURN	= 13;
	this.TAB = 9;
	this.ESC = 27;
	this.ARROW_UP = 38;
	this.ARROW_DOWN = 40;
	
	this.currentRequest	= null;
	
	Autosuggest.prototype.init	= function(file,id){
		var _this	= this;
				
		if( document.getElementById(id) == null ){
			setTimeout(_this.init(file,id),100);
			return;
		}
		
		this.file		= file;
		this.field		= document.getElementById(id);
		
		/* Set events */
		this.field.onkeypress	= function(e){ return _this.onKeyPress(e); }
		this.field.onkeyup		= function(e){ return _this.onKeyUp(e); }
		
		/* Set default values */
		this.parameters.minchars = 1;
		this.parameters.method = "get";
		this.parameters.className = "autosuggest";
		this.parameters.timeout = 2500;
		this.parameters.delay = 500;
		this.parameters.offsety = -5;
		this.parameters.maxheight = 250;
	}
	
	Autosuggest.prototype.onKeyPress	= function(e){
		var keyCode = (window.event) ? window.event.keyCode : e.keyCode;
		
		switch(keyCode){
			case this.RETURN:
				this.setHighlightedValue();
				return false;

			case this.ESC:
				this.clearSuggestions();
				return true;
		}
	}
	
	Autosuggest.prototype.onKeyUp	= function(e){
		var keyCode = (window.event) ? window.event.keyCode : e.keyCode;
		
		switch(keyCode){
			case this.ARROW_UP:
				this.changeHighlight(keyCode);
				return false;

			case this.ARROW_DOWN:
				this.changeHighlight(keyCode);
				return false;
				
			default:
				this.getSuggestions(this.field.value);
		}
		
		return true;
	}
	
	Autosuggest.prototype.getSuggestions	= function(value){
		if( value == this.input )	return false;
		
		var length	= value.length;
		
		if( length < this.parameters.minchars ){
			this.input	= "";
			return false;
		}
		
		if( length > this.inputCharacters && this.suggestions.length > 0 ){
			var temp	= [];
			for(i=0; i<this.suggestions.length; i++){
				if( this.suggestions[i].value.substr(0,value.length).toLowerCase() == value.toLowerCase() )
					temp.push(this.suggestions[i]);
			}
			
			this.input	= value;
			this.inputCharacters	= length;
			this.suggestions		= temp;
			
			this.createList(this.suggestions);
		}
		else {
			this.input	= value;
			this.inputCharacters	= length;
			
			var _this = this;
			clearTimeout(this.currentRequest);
			this.currentRequest = setTimeout( function() { _this.doAjaxRequest() }, this.parameters.delay );
		}
		
		return false;
	}
	
	Autosuggest.prototype.doAjaxRequest = function (){
		var _this	= this;		
		var name	= new String(this.field.name);
		
		var params	= {"AJAX":"true", 'command' : 'searchResults' };
		params[name]	=  escape(this.field.value);
		
		var caller	= function(response){	_this.setSuggestions(response);	};		
		$.get(this.file,params,caller);
	}
	
	Autosuggest.prototype.setSuggestions	= function(response){
		this.suggestions	= [];
		
		var results	= JSON.parse(response);
		
		for(i=0; i<results.results.length; i++){
			this.suggestions.push( {'id':results.results[i].id, 'value':results.results[i].value, 'info':results.results[i].info}  );
		}
		
		this.idAs = "as_"+this.field.id;
		this.createList(this.suggestions);
	}
	
	Autosuggest.prototype.createList = function(data){
		var _this = this;		
		
		$("#"+this.idAs).remove();
		clearTimeout(this.currentRequest);
		
		if( data.length == 0 ){
			return;
		}
		
		var div = this.createElement("div", {id:this.idAs, className:this.parameters.className});			
		var hcorner = this.createElement("div", {className:"as_corner"});
		var hbar = this.createElement("div", {className:"as_bar"});
		//header.appendChild(hcorner);
		//header.appendChild(hbar);
		
		var ul = this.createElement("ul", {id:"as_ul"});
		
		for(i=0; i<data.length; i++){
			var val = data[i].value;
			var st  = val.toLowerCase().indexOf( this.input.toLowerCase() );
			var output = val.substring(0,st) + "<em>" + val.substring(st, st+this.input.length) + "</em>" + val.substring(st+this.input.length);
			
			var span 		= this.createElement("span", {}, output, true);
			if( data[i].info != "" ){
				var br			= this.createElement("br", {});
				span.appendChild(br);
				var small		= this.createElement("small", {}, data[i].info);
				span.appendChild(small);
			}
			
			var a 		= this.createElement("a", { href:"#" });			
			var tl 		= this.createElement("span", {className:"tl"}, " ");
			var tr 		= this.createElement("span", {className:"tr"}, " ");
			a.appendChild(tl);
			a.appendChild(tr);			
			a.appendChild(span);
			
			a.name = i+1;
			a.onclick = function () { _this.setHighlightedValue(); return false; }
			a.onmouseover = function () { _this.clearHighlight(); _this.setHighlight(this.name); }			
			var li 			= this.createElement(  "li", {}, a  );			
			ul.appendChild(li);
		}		
		
		div.appendChild(ul);		
		
		var fcorner = this.createElement("div", {className:"as_corner"});
		var fbar = this.createElement("div", {className:"as_bar"});
		//footer.appendChild(fcorner);
		//footer.appendChild(fbar);

		var pos = this.getPosition(this.field);
		
		div.style.left 		= pos.x + "px";
		div.style.top 		= ( pos.y + this.field.offsetHeight + this.parameters.offsety ) + "px";
		div.style.width 	= this.field.offsetWidth + "px";
		
		document.getElementsByTagName("body")[0].appendChild(div);
		this.highlighted = 0;
	}
	
	Autosuggest.prototype.createElement = function(type, attributes, content, html){
		var element = document.createElement(type);
		if( !element ) 	return null;
			
		for(var a in attributes)
			element[a] = attributes[a];
			
		if( typeof(content) == "string" && !html )
			element.appendChild(document.createTextNode(content));
		else if( typeof(content) == "string" && html )
			element.innerHTML = content;
		else if( typeof(content) == "object" )
			element.appendChild(content);

		return element;
	}
	
	Autosuggest.prototype.changeHighlight = function(key){	
		var list = document.getElementById("as_ul");
		if (!list)	return false;
		
		var n;

		if (key == 40)
			n = this.highlighted + 1;
		else if (key == 38)
			n = this.highlighted - 1;
				
		if( n > list.childNodes.length )
			n = list.childNodes.length;
		if( n < 1 )
			n = 1;
				
		this.clearHighlight();
		this.setHighlight(n);
	}
	
	Autosuggest.prototype.setHighlight = function(number){
		var list = document.getElementById("as_ul");
		if (!list)	return false;
		
		if (this.iHighlighted > 0)
			this.clearHighlight();
		
		number	= parseInt(number);
		if( isNaN(number) )	return false;
		
		this.highlighted = number;
		
		list.childNodes[this.highlighted-1].className = "as_highlight";
	}
	
	Autosuggest.prototype.clearHighlight = function(){
		var list = document.getElementById("as_ul");
		if (!list)	return false;
		
		if( this.highlighted > 0 ){
			list.childNodes[this.highlighted-1].className = "";
			this.highlighted = 0;
		}
	}

	Autosuggest.prototype.setHighlightedValue = function (){
		if( this.highlighted == 0 )	return;
		
		this.input = this.field.value = this.suggestions[this.highlighted-1 ].value;
		id	= this.field.id;
		if( $("#"+id+"_id").length > 0 ){
			$("#"+id+"_id").val(this.suggestions[this.highlighted-1 ].id);
		}
			
		this.field.focus();
		if( this.field.selectionStart)	this.field.setSelectionRange(this.input.length, this.input.length);
		this.clearSuggestions();
	}
	
	Autosuggest.prototype.getPosition = function(field){
		var obj = field;

		var curleft = 0;
		if( obj.offsetParent ){
			while( obj.offsetParent ){
				curleft += obj.offsetLeft
				obj = obj.offsetParent;
			}
		}
		else if( obj.x )
			curleft += obj.x;


		var obj = field;		
		var curtop = 0;
		if( obj.offsetParent ){
			while( obj.offsetParent ){
				curtop += obj.offsetTop
				obj = obj.offsetParent;
			}
		}
		else if( obj.y )
			curtop += obj.y;

		return {x:curleft, y:curtop}
	}
	
	Autosuggest.prototype.clearSuggestions = function (){
		$("#"+this.idAs).remove();
	}
}
