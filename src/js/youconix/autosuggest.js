/**
 * Autosuggest class
 * Based on : http://www.brandspankingnew.net/archive/2006/08/ajax_auto-suggest_auto-complete.html
 */
class Autosuggest{
  constructor(){
    this.local;
    this.file = null;
    this.fieldID;
    this.field = null;
    this.callback;
	
    this.suggestions = [];
    this.input	= "";
    this.inputCharacters = 0;
    this.highlighted = 0;
    this.parameters = {};
	
    /* Set keys */
    this.RETURN	= 13;
    this.TAB = 9;
    this.ESC = 27;
    this.ARROW_UP = 38;
    this.ARROW_DOWN = 40;
	
    this.currentRequest = null;
  }
  init(file,id,callback = null,local = 0){
    this.local = local;
    this.callback = callback;
				
    if( document.getElementById(id) === null ){
      setTimeout(()=> {
	this.init(file,id,local);
      },100);
    return;
    }
		
    this.file = file;
    this.fieldID = id;
    this.field = document.getElementById(id);
		
    /* Set events */
    $('#'+this.fieldID).on('keypress',(event) => {
      this.onKeyPress(event);
    });
    $('#'+this.fieldID).on('keyup',(event) => {
      this.onKeyUp(event);
    });
    $('#'+this.fieldID).on('blur',() => {
      setTimeout(() => { 
	this.clearSuggestions(); 
      }, 400 );
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
  detach(){
    $('#'+this.fieldID).off('keypress');
    $('#'+this.fieldID).off('keyup');
    $("#"+this.idAs+' a').off('click');
  }
  onKeyPress(event){
    let keyCode = (window.event) ? window.event.keyCode : event.keyCode;
		
    switch(keyCode){
      case this.RETURN:
	this.setHighlightedValue();
	return false;

      case this.ESC:
	this.clearSuggestions();
	return true;
    }
  }
  onKeyUp(event){
    let keyCode = (window.event) ? window.event.keyCode : event.keyCode;
		
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
  getSuggestions(value){
    if( value === this.input )	return false;
		
    let length = value.length;
		
    if( length < this.parameters.minchars && length !== 0 ){
      this.input = "";
      return false;
    }
		
    if( length > this.inputCharacters && this.suggestions.length > 0 ){
      let temp	= [];
      for(let i=0; i<this.suggestions.length; i++){
	if( this.suggestions[i].value.toLowerCase().indexOf( value.toLowerCase()  ) !== -1 || this.suggestions[i].info.toLowerCase().indexOf( value.toLowerCase()  ) !== -1 ){
	  temp.push(this.suggestions[i]);
	}
      }
			
      this.input = value;
      this.inputCharacters = length;
      this.suggestions = temp;
			
      this.createList(this.suggestions);
    }
    else {
      this.input = value;
      this.inputCharacters = length;
			
      clearTimeout(this.currentRequest);
      
      if( this.local === 0 ){
        this.currentRequest = setTimeout( () => { 
	  this.doAjaxRequest() ;
	}, this.parameters.delay );
      }
      else {
        this.doLocalRequest();
      }
    }
		
    return false;
  }
  doLocalRequest(){
    let results = this.file(this.field.value);
    this.setSuggestions({'results':results});
  }
  doAjaxRequest(){
    let name = new String(this.field.name);
    let params	= {"AJAX":"true", 'command' : 'searchResults' };
    params[name]	=  escape(this.field.value);
		
    let caller	= (response) => {
      let results = JSON.parse(response); 
      this.setSuggestions(results);
    };
    $.get(this.file,params,caller);
  }
  setSuggestions(results){
    if( results.results ){
      results = results.results;
    }
		
    this.suggestions = [];
    for(let i in results ){
      this.suggestions.push( {'id':results[i].id, 'value':results[i].value, 'info':results[i].info}  );
    }
		
    this.idAs = "as_"+this.field.id;
    this.createList(this.suggestions);
  }
  createList(data){
    $("#"+this.idAs).remove();
    clearTimeout(this.currentRequest);
		
    if( data.length === 0 ){
      return;
    }
	
    let div = this.createElement("div", {id:this.idAs, className:this.parameters.className});			
    let ul = this.createElement("ul", {id:this.idAs});
		
    for(let i=0; i<data.length; i++){
      let val = data[i].value;
      let st  = val.toLowerCase().indexOf( this.input.toLowerCase() );
      let output = val;
      if(st !== -1) {
	output = val.substring(0,st) + "<em>" + val.substring(st, st+this.input.length) + "</em>" + val.substring(st+this.input.length);
      }
			
      let span = this.createElement("span", {}, output, true);
      if( data[i].info !== "" ){
	let info = data[i].info;
	let st2  = info.toLowerCase().indexOf( this.input.toLowerCase() );
	if(st2 !== -1) {
	  info = data[i].info.substring(0,st2) + "<em>" + data[i].info.substring(st2, st2+this.input.length) + "</em>" + data[i].info.substring(st2+this.input.length);
	}
				
	let br = this.createElement("br", {});
	span.appendChild(br);
	let small = this.createElement("small", {}, info, true);
	span.appendChild(small);
      }
      let a = this.createElement("a", { href:"#" });
      let tl = this.createElement("span", {className:"tl"}, " ");
      let tr = this.createElement("span", {className:"tr"}, " ");
      a.appendChild(tl);
      a.appendChild(tr);			
      a.appendChild(span);
			
      a.name = i+1;
      a.onclick = () => { this.setHighlightedValue(); return false; };
      a.onmouseover = () => { this.clearHighlight(); this.setHighlight(this.name); };		
      let li = this.createElement(  "li", {}, a  );			
      ul.appendChild(li);
    }		
		
    div.appendChild(ul);		
		
    let pos = this.getPosition(this.field);
		
    div.style.left = pos.x + "px";
    div.style.top = ( pos.y + this.field.offsetHeight + this.parameters.offsety ) + "px";
    div.style.width = this.field.offsetWidth + "px";
    document.getElementsByTagName("body")[0].appendChild(div);
    this.highlighted = 0;
    
    $("#"+this.idAs+' a').click((item) => {
      let pos = $(item).attr('name');
      this.handleClick(pos);
    });
  }
  createElement(type, attributes, content, html){
    let element = document.createElement(type);
    if( !element ){
      return null;
    }
			
    for(let a in attributes){
      element[a] = attributes[a];
    }
    if( typeof(content) === "string" && !html ){
      element.appendChild(document.createTextNode(content));
    }
    else if( typeof(content) === "string" && html ){
      element.innerHTML = content;
    }
    else if( typeof(content) === "object" ){
      element.appendChild(content);
    }

    return element;
  }
  changeHighlight(key){	
    let list = document.getElementById("as_ul");
    if (!list){
      return false;
    }
		
    let n;
    if (key === 40){
      n = this.highlighted + 1;
    }
    else if (key === 38){
      n = this.highlighted - 1;
    }
				
    if( n > list.childNodes.length ){
      n = list.childNodes.length;
    }
    if( n < 1 ){
      n = 1;
    }
				
    this.clearHighlight();
    this.setHighlight(n);
  }
  setHighlight(number){
    let list = document.getElementById("as_ul");
    if (!list){
      return false;
    }
		
    if (this.iHighlighted > 0){
      this.clearHighlight();
    }
		
    number = parseInt(number);
    if( isNaN(number) ){
      return false;
    }
		
    this.highlighted = number;		
    list.childNodes[this.highlighted-1].className = "as_highlight";
  }
  clearHighlight(){
    let list = document.getElementById("as_ul");
    if (!list){
      return false;
    }
		
    if( this.highlighted > 0 ){
      list.childNodes[this.highlighted-1].className = "";
      this.highlighted = 0;
    }
  }
  setHighlightedValue(){
    if( this.highlighted === 0 ){
      return;
    }
		
    this.input = this.field.value = this.suggestions[this.highlighted-1 ].value;
    let id = this.field.id;
    
    if( $("#"+id+"_id").length > 0 ){
      $("#"+id+"_id").val(this.suggestions[this.highlighted-1 ].id);
    }
			
    this.field.focus();
    if( this.field.selectionStart ){	
      this.field.setSelectionRange(this.input.length, this.input.length);
      
      if( this.callback !== null ){
        this.callback(this.fieldID,this.suggestions[this.highlighted-1 ].id,this.suggestions[this.highlighted-1 ].value);
      }
    }
    
    this.clearSuggestions();
  }
  handleClick(pos){
    this.input = this.field.value = this.suggestions[pos-1 ].value;
    if( this.callback !== null ){
      this.callback(this.fieldID,this.suggestions[pos-1 ].id,this.suggestions[pos-1 ].value);
    }
    this.clearSuggestions();
  }
  getPosition(field){
    let obj = field;
    let curleft = 0;
    if( obj.offsetParent ){
      while( obj.offsetParent ){
	curleft += obj.offsetLeft;
	obj = obj.offsetParent;
      }
    }
    else if( obj.x ){
      curleft += obj.x;
    }

    obj = field;
    let curtop = 0;
    if( obj.offsetParent ){
      while( obj.offsetParent ){
	curtop += obj.offsetTop;
	obj = obj.offsetParent;
      }
    }
    else if( obj.y ){
      curtop += obj.y;
    }

    return {x:curleft, y:curtop};
  }
  clearSuggestions(){
    $("#"+this.idAs).remove();
  }
}
