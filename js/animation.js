function Animation(){
}

Animation.prototype.animate	= function(){
	if( !this.hasCSS3() ){
		return;
	}
	
	this.radio();
	this.checkbox();
	this.select();
}

Animation.prototype.hasCSS3	= function(){
	var d = document.createElement("detect"),
	CSSprefix = ",webkit-,moz-,O-,ms-,Khtml-".split(",");
		
	for(var n = 0, np = CSSprefix.length; n < np; n++) {
		item = CSSprefix[n]+'box-shadow';
		if( $(d).css(item) !== undefined ){
			return true;
		}
	}

	return false;
}

Animation.prototype.radio	= function(){
	$(":radio").each(function(){
		checkbox = $(this);
		labelID = 'label_'+checkbox.attr("id");
		label = '<label id="'+labelID+'" class="radio"></label>';
		
		checkbox.addClass("animation");
		checkbox.after(label);
		
		$("#"+labelID).click(function(){
			id = $(this).prev().attr("id");
			animation.triggerSelect(id);
		});
	});
}

Animation.prototype.checkbox	= function(){
	$(":checkbox").each(function(){
		checkbox = $(this);
		labelID = 'label_'+checkbox.attr("id");
		label = '<label id="'+labelID+'" class="checkbox"></label>';
		
		checkbox.addClass("animation");
		checkbox.after(label);
		
		$("#"+labelID).click(function(){
			id = $(this).prev().attr("id");
			animation.triggerSelect(id);
		});
	});
}

Animation.prototype.triggerSelect = function(id){
	if( $("#"+id).is(":checked") ){
		$("#"+id).prop("checked",false);
	}
	else {
		$("#"+id).prop("checked",true);
	}
	$("#"+id).trigger("change",id);
}

Animation.prototype.select	= function(){
	$("select").each(function(){
		item = $(this);
		id = item.attr("id");
		width = item.width();
		if( width < 85 ){	width=85; }
		newWidth	= width+30;
		
		itemID = "select_"+id;
		item.wrap('<div class="select" id="'+itemID+'" style="width:'+width+'px"></div>');
		item.addClass("animation");
		item.css("width",newWidth+'px');
	});
}

var animation = new Animation();