"use strict";

var YouconixGraph = {}
YouconixGraph.position = function(x,y){
	this.x = x;
	this.y = y;
}
YouconixGraph.position.prototype.getX = function(){
	return this.x;
}
YouconixGraph.position.prototype.getY = function(){
	return this.y;
}
YouconixGraph.canvas = function(id,backgroundColor){
	this.id = id;
	this.canvas = document.getElementById(id);
	this.backgroundColor = '#'+backgroundColor || '#FFF';
	this.context = this.canvas.getContext('2d');
	
	this.clear();
}
YouconixGraph.canvas.prototype.getHeight = function(){
	return $('#'+this.id).css('height');
}
YouconixGraph.canvas.prototype.getWidth = function(){
	return $('#'+this.id).css('width');
}
YouconixGraph.canvas.prototype.clear = function(){
	var height = this.getHeight();
	var width = this.getWidth();
	
	this.context.fillStyle = this.backgroundColor;
	this.context.fillRect(0,0,width,height);
}
YouconixGraph.canvas.prototype.getCanvas = function(){
	return this.context;
}
YouconixGraph.line = function(start,end,color){
	this.start = start;
	this.end = end;
	
	if( color == undefined ){
		color = '111';
	}
	this.color = '#'+color;
}
YouconixGraph.line.prototype.draw = function(canvas){
	var context = canvas.getCanvas();
	
	context.beginPath();
	context.moveTo(this.start.getX(),this.start.getY());
	context.lineTo(this.end.getX(),this.end.getY());
	context.strokeStyle = this.color;
	context.stroke();
}
YouconixGraph.point = function(position,radius,linecolor,fillcolor){
	this.position = position;
	if( radius == undefined ){ radius = 20; }
	if( linecolor == undefined ){ linecolor = '111'; }
	if( fillcolor == undefined ){ fillcolor = '111'; }
	
	this.radius = radius;
	this.linecolor = '#'+linecolor;
	this.fillcolor = '#'+fillcolor;
}
YouconixGraph.point.prototype.draw = function(canvas){
	var context = canvas.getCanvas();
	
	context.beginPath();
	context.arc(this.position.getX(),this.position.getY(),this.radius,0,2*Math.PI);
	context.fillStyle = this.fillcolor;
	context.fill();
	context.strokeStyle = this.linecolor;
	context.stroke();
}
YouconixGraph.arch	= function(position,radius,startAngle,endAngle,linecolor,fillcolor){
	this.position = position;
	if( radius == undefined ){ radius = 20; }
	if( startAngle == undefined){ startAngle = 0; }
	if( endAngle == undefined ){ startAngle = 0.5; }
	if( linecolor == undefined ){ linecolor = '111'; }
	if( fillcolor == undefined ){ fillcolor = '111'; }
	
	this.radius = radius;
	this.startAngle = startAngle*Math.PI;
	this.endAngle = endAngle*Math.PI;
	this.linecolor = '#'+linecolor;
	this.fillcolor = '#'+fillcolor;
}
YouconixGraph.arch.prototype.draw	= function(canvas){
	var context = canvas.getCanvas();
	
	context.beginPath();
    context.arc(this.position.getX(),this.position.getY(), this.radius, this.startAngle, this.endAngle, false);
    context.fillStyle = this.fillcolor;
	context.fill();
	context.strokeStyle = this.fillcolor;
	context.stroke();
}
YouconixGraph.piePiece = function(position,radius,startAngle,endAngle,color){
	this.position = position;
	if( radius == undefined ){ radius = 20; }
	if( startAngle == undefined){ startAngle = 0; }
	if( endAngle == undefined ){ startAngle = 0.5; }
	if( color == undefined ){ color = '111'; }
	
	this.radius = radius;
	this.leftPosition = this.getPosition(startAngle);
	this.rightPosition = this.getPosition(endAngle);
	this.startAngle = (startAngle/180*Math.PI);
	this.endAngle = (endAngle/180*Math.PI);
	this.fillcolor = '#'+color;
}
YouconixGraph.piePiece.prototype.getPosition = function(angle){
	if( angle == 0 || angle == 360 ){
		return new YouconixGraph.position(this.position.getX() + this.radius , this.position.getY());
	}
	if( angle == 180 ){
		return new YouconixGraph.position(this.position.getX() - this.radius , this.position.getY());
	}
	if( angle == 90 ){
		return new YouconixGraph.position(this.position.getX() , this.position.getY() + this.radius);
	}
	if( angle == 270 ){
		return new YouconixGraph.position(this.position.getX() , this.position.getY() - this.radius);
	}
	
	var rad=0,angle2=0;
	angle2 = angle;
	if( angle < 270 ){
		while( angle2 > 90 ){
			angle2 -= 90;
		}
	}
	else {
		angle2 = (90 - (angle-270));
	}
	rad = (angle2/180*Math.PI);	
	var vertSide, horSide;
	
	vertSide = (Math.sin(rad)*this.radius);
	horSide = (Math.cos(rad)*this.radius);
	
	if( angle < 90 ){
		return new YouconixGraph.position(this.position.getX() + horSide , this.position.getY() + vertSide);
	}
	if( angle < 180 ){
		return new YouconixGraph.position(this.position.getX() - horSide , this.position.getY() + vertSide);
	}
	if( angle < 270 ){
		return new YouconixGraph.position(this.position.getX() - horSide , this.position.getY() - vertSide);
	}
	return new YouconixGraph.position(this.position.getX() + horSide , this.position.getY() - vertSide);
}
YouconixGraph.piePiece.prototype.draw	= function(canvas){
	var context = canvas.getCanvas();
	
	context.beginPath();
	/* Create arch */
    context.arc(this.position.getX(),this.position.getY(), this.radius, this.startAngle, this.endAngle, false);
    
    /* Create left line */
    context.moveTo(this.position.getX(),this.position.getY());
    context.lineTo(this.leftPosition.getX(),this.leftPosition.getY());
    
    /* Create right line */
    context.moveTo(this.position.getX(),this.position.getY());
    context.lineTo(this.rightPosition.getX(),this.rightPosition.getY());
    
    context.lineTo(this.leftPosition.getX(),this.leftPosition.getY());
    
    context.fillStyle = this.fillcolor;
	context.fill();
}
YouconixGraph.rectangle = function(start,end,linecolor,fillcolor){
	this.start = start;
	this.end = end;
	this.linecolor = '#'+linecolor || '#111';
	this.fillcolor = '#'+fillcolor || '#111';
}
YouconixGraph.rectangle.prototype.draw = function(canvas){
	var context = canvas.getCanvas();
	
	context.beginPath();
	context.rect(this.start.getX(),this.start.getY(),(this.end.getX()-this.start.getX()),(this.end.getY()-this.start.getY()) );
	context.fillStyle = this.fillcolor;
	context.fill();
	context.strokeStyle = this.fillcolor;
	context.stroke();
}
YouconixGraph.text = function(position,text,color,text_style,text_size,font){
	if( text_style == undefined ){
		text_style = 'normal';
	}
	if( text_size == undefined) {
		text_size = '12px';
	}
	if( font == undefined) {
		font = 'Verdana';
	}
	
	this.position = position;
	this.text = text;
	if( color == undefined ){
		color = '111';
	}
	this.color = '#'+color;
	this.font = text_style+' '+text_size+' '+font;
	this.rotation = 6;//no rotation
}
YouconixGraph.text.prototype.rotateLeft = function(){
	this.rotation = 3;
}
YouconixGraph.text.prototype.draw = function(canvas){
	var context = canvas.getCanvas();
	
	context.font = this.font;
	context.fillStyle = this.color;
	//context.rotate(Math.PI*2/(this.rotation*6));
    context.fillText(this.text, this.position.getX(), this.position.getY());
}
YouconixGraph.plugins = {};