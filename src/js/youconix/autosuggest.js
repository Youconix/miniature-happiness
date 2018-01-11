/**
 * Autosuggest class
 * Based on : http://www.brandspankingnew.net/archive/2006/08/ajax_auto-suggest_auto-complete.html
 */
function Autosuggest(){
  this.local;
	this.file	= null;
  this.fieldID;
	this.field		= null;
  this.callback;
	
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
	
	Autosuggest.prototype.init	= function(file,id,callback,local){
    this.local = local || 0;
		var _this	= this;
    this.callback = callback || null;
				
		if( document.getElementById(id) == null ){
			setTimeout(_this.init(file,id,local),100);
			return;
		}
		
		this.file		= file;
    this.fieldID  = id;
		this.field		= document.getElementById(id);
		
		/* Set events */
    $('#'+this.fieldID).on('keypress',function(e){
      _this.onKeyPress(e);
    });
    $('#'+this.fieldID).on('keyup',function(e){
      _this.onKeyUp(e);
    });
    $('#'+this.fieldID).on('blur',function(e){
	  setTimeout( function() { _this.clearSuggestions(); }, 400 );
    });
		
		/* Set default values */
		this.parameters.minchars = 3;
		this.parameters.method = "get";
		this.parameters.className = "autosuggest";
		this.parameters.timeout = 2500;
		this.parameters.delay = 500;
		this.parameters.offsety = -5;
		this.parameters.maxheight = 250;
	}
  
  Autosuggest.prototype.detach  = function(){
    $('#'+this.fieldID).off('keypress');
    $('#'+this.fieldID).off('keyup');
    $("#"+this.idAs+' a').off('click');
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
		
		if( length < this.parameters.minchars && length != 0 ){
			this.input	= "";
			return false;
		}
		
		if( length > this.inputCharacters && this.suggestions.length > 0 ){
			var temp	= [];
			for(i=0; i<this.suggestions.length; i++){
				if( this.suggestions[i].value.toLowerCase().indexOf( value.toLowerCase()  ) != -1 || this.suggestions[i].info.toLowerCase().indexOf( value.toLowerCase()  ) != -1 )
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
      
      if( this.local == 0 ){
        this.currentRequest = setTimeout( function() { _this.doAjaxRequest() }, this.parameters.delay );
      }
      else {
        this.doLocalRequest();
      }
		}
		
		return false;
	}
	
  Autosuggest.prototype.doLocalRequest  = function(){
    var results = this.file(this.field.value);
    this.setSuggestions({'results':results});
  }
  
	Autosuggest.prototype.doAjaxRequest = function (){
		var _this	= this;		
		var name	= new String(this.field.name);
		
		var params	= {"AJAX":"true", 'command' : 'searchResults' };
		params[name]	=  escape(this.field.value);
		
		var caller	= function(response){	var results	= JSON.parse(response);  _this.setSuggestions(results);	};		
		$.get(this.file,params,caller);
	}
	
	Autosuggest.prototype.setSuggestions	= function(results){
		if( results.results ){
			results = results.results;
		}
		
		this.suggestions	= [];
		
		var i;
		for( i in results ){
			this.suggestions.push( {'id':results[i].id, 'value':results[i].value, 'info':results[i].info}  );
		}
		
		this.idAs = "as_"+this.field.id;
		this.createList(this.suggestions);
	}
	
	Autosuggest.prototype.createList = function(data){
		var _this = this;		
		console.log('running');
		$("#"+this.idAs).remove();
		clearTimeout(this.currentRequest);
		
		if( data.length == 0 ){
			return;
		}
	
		var div = this.createElement("div", {id:this.idAs, className:this.parameters.className});			
		
		var ul = this.createElement("ul", {id:this.idAs});
		
		for(i=0; i<data.length; i++){
			var val = data[i].value;
			var st  = val.toLowerCase().indexOf( this.input.toLowerCase() );
			var output = val;
			if(st != -1) {
				output = val.substring(0,st) + "<em>" + val.substring(st, st+this.input.length) + "</em>" + val.substring(st+this.input.length);
			}
			
			var span 		= this.createElement("span", {}, output, true);
			if( data[i].info != "" ){
				var info = data[i].info;
				var st2  = info.toLowerCase().indexOf( this.input.toLowerCase() );
				if(st2 != -1) {
					info = data[i].info.substring(0,st2) + "<em>" + data[i].info.substring(st2, st2+this.input.length) + "</em>" + data[i].info.substring(st2+this.input.length);
				}
				
				var br			= this.createElement("br", {});
				span.appendChild(br);
				var small		= this.createElement("small", {}, info, true);
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
		
		var pos = this.getPosition(this.field);
		
		div.style.left 		= pos.x + "px";
		div.style.top 		= ( pos.y + this.field.offsetHeight + this.parameters.offsety ) + "px";
		div.style.width 	= this.field.offsetWidth + "px";
		console.log(div);
		document.getElementsByTagName("body")[0].appendChild(div);
		this.highlighted = 0;
    
	    var _this = this;
	    $("#"+this.idAs+' a').click(function(e){
	      var pos = $(this).attr('name');
	      _this.handleClick(pos);
	    });
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
		var id	= this.field.id;
    
    if( $("#"+id+"_id").length > 0 ){
			$("#"+id+"_id").val(this.suggestions[this.highlighted-1 ].id);
		}
			
		this.field.focus();
		if( this.field.selectionStart ){	
      this.field.setSelectionRange(this.input.length, this.input.length);
      
      if( this.callback != null ){
        this.callback(this.fieldID,this.suggestions[this.highlighted-1 ].id,this.suggestions[this.highlighted-1 ].value);
      }
    }
    
		this.clearSuggestions();
	}
  
  Autosuggest.prototype.handleClick = function (pos){
    this.input = this.field.value = this.suggestions[pos-1 ].value;
    if( this.callback != null ){
      this.callback(this.fieldID,this.suggestions[pos-1 ].id,this.suggestions[pos-1 ].value);
    }
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
		//$("#"+this.idAs).remove();
	}
}
