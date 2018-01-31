class Animation{
  animate(){
    if( !this.hasCSS3() ){
      return;
    }
	
    /* Check for stylesheet */
    if( !$("link[href='/resources/css/widgets.css']").length ){
      $('<link href="/resources/css/widgets.css" rel="stylesheet">').appendTo("head");
    }
	
    this.radio();
    this.checkbox();
    this.select();
  }
  hasCSS3(){
    let d = document.createElement("detect"),
    CSSprefix = ",webkit-,moz-,O-,ms-,Khtml-".split(",");
		
    for(let n = 0, np = CSSprefix.length; n < np; n++) {
      item = CSSprefix[n]+'box-shadow';
      if( $(d).css(item) !== undefined ){
	return true;
      }
    }

    return false;
  }
  radio(){
    $(":radio").each((i,radio) => {
      radio = $(radio);
      let labelID = 'label_'+radio.prop("id");
      let label = '<label id="'+labelID+'" class="radio"></label>';
		
      radio.addClass("animation");
      radio.after(label);
		
      $("#"+labelID).click((item) => {
	let id = $(item).prev().prop("id");
	this.triggerSelect(id);
      });
    });
  }
  checkbox(){
    $(":checkbox").each((i,checkbox) => {
      checkbox = $(checkbox);
      let labelID = 'label_'+checkbox.prop("id");
      let label = '<label id="'+labelID+'" class="checkbox"></label>';
		
      checkbox.addClass("animation");
      checkbox.after(label);
		
      $("#"+labelID).click((item) => {
	let id = $(item).prev().prop("id");
	this.triggerSelect(id);
      });
    });
  }
  triggerSelect(id){
    if( $("#"+id).is(":checked") ){
      $("#"+id).prop("checked",false);
    }
    else {
      $("#"+id).prop("checked",true);
    }
    $("#"+id).trigger("change",id);
  }
  select(){
    $("body").append('<div id="animationCalc" style="display:none"></div>');
	
    $("select").each((i,item) => {
      item = $(item);
      let id = item.prop("id");
      if( !id ){
	id = item.prop("name");
      }
		
      itemID = "select_"+id;
      item.wrap('<div class="select" id="'+itemID+'"></div>');
      item.addClass("animation");
		
      this.updateSelect( $(this));
    });
	
    $("#animationCalc").remove();
  }
  updateSelect(item){    
    let added = false;
    if( $("#animationCalc").length === 0 ){
      $("body").append('<div id="animationCalc" style="display:none"></div>');
      added = true;
    }
	
    let id = item.prop("id");
    let width = parseInt($(item).css("width"));
    if( width === 0 ){
	let maxlength = 0;
	let maxValue = '';
		
	$("#"+id+" option").each((i,option) => {
	  option = $(option);
	  let w = option.text().length;
	  if( w > maxlength ){
	    maxValue = option.text();
	    maxlength = w;
	  }
	});
		
	$("#animationCalc").html(maxValue);
	width = $("#animationCalc").width()+20;
    }
	
    if( width < 85 ){	width=85; }
    let newWidth = width+30;
	
    item.parent().css("width",width+"px");
    item.css("width",newWidth+'px');
	
    if( added ){
      $("#animationCalc").remove();
    }
  }
}

var animation = new Animation();