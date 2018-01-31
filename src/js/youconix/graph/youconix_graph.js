var YouconixGraph = {};
YouconixGraph.Position = class Position {
  constructor(x,y){
    this.x = x;
    this.y = y;
  }
  getX(){
    return this.x;
  }
  getY(){
    return this.y;
  }
};
YouconixGraph.Canvas = class Canvas {
  constructor(id,backgroundColor = 'FFF'){
    this.id = id;
    this.canvas = document.getElementById(id);
    this.backgroundColor = '#'+backgroundColor;
    this.context = this.canvas.getContext('2d');
	
    this.clear();
  }
  getHeight(){
    return $('#'+this.id).css('height');
  }
  setHeight(height){
    $('#'+this.id).prop('height', height);
  }
  getWidth(){
    return $('#'+this.id).css('width');
  }
  clear(){
    let height = this.getHeight();
    let width = this.getWidth();
	
    this.context.fillStyle = this.backgroundColor;
    this.context.fillRect(0,0,width,height);
  }
  getCanvas(){
    this.context.globalAlpha = 1;
    
    return this.context;
  }
};

YouconixGraph.Figure = class Figure {
  constructor(){
    this.color;
    
    this.setColor();
  }
  setColor(color = '111'){
    this.color = '#'+color;
    
    return this;    
  }
}
YouconixGraph.FillableFigure = class FillableFigure extends YouconixGraph.Figure {
  constructor(){
    super();
    
    this.fillColor;
    this.transparenty;
    this.doFill;
    
    this.setFillColor();
    this.setTransparenty();
    this.setFill();
  }
  setFill(fill = false){
    this.doFill = fill;
    
    return this;
  }
  setFillColor(color = '111'){
    this.fillColor = '#'+color;
    
    return this;
  }
  setTransparenty(transparenty = 1){
    this.transparenty = transparenty;
    
    return this;
  }
}
YouconixGraph.Line = class Line extends YouconixGraph.Figure {
  constructor(start,end){
    super();
    
    this.start = start;
    this.end = end;
  }
  draw(canvas){
    let context = canvas.getCanvas();
	
    context.beginPath();
    context.moveTo(this.start.getX(),this.start.getY());
    context.lineTo(this.end.getX(),this.end.getY());
    context.strokeStyle = this.color;
    context.stroke();
  }
};
YouconixGraph.FreeLine = class Line extends YouconixGraph.FillableFigure {
  constructor(){
    super();
    
    this.positions = new Array();
  }
  addPosition(position){
    this.positions.push(position);
    
    return this;
  }
  draw(canvas){
    let context = canvas.getCanvas();
    
    let start = this.positions[0];
	
    context.beginPath();
    context.moveTo(start.getX(),start.getY());
    
    let position;
    for(let i=1; i<this.positions.length; i++){
      position = this.positions[i];
      context.lineTo(position.getX(),position.getY());
    }
    
    if (this.doFill) {
      context.globalAlpha = this.transparenty;
      context.fillStyle = this.fillColor;
      context.fill();
    }
    context.strokeStyle = this.color;
    context.stroke();
  }
};
YouconixGraph.Point = class Point extends YouconixGraph.FillableFigure {
  constructor(position){
    super();
    
    this.position = position;
    this.radius;
    
    this.setRadius();
  }
  setRadius(radius = 20){
    this.radius = radius;
    
    return this;
  }
  draw(canvas){
    let context = canvas.getCanvas();
	
    context.beginPath();
    context.arc(this.position.getX(),this.position.getY(),this.radius,0,2*Math.PI);
    
    if (this.doFill) {
      context.globalAlpha = this.transparenty;
      context.fillStyle = this.fillColor;
      context.fill();
    }
    context.strokeStyle = this.color;
    context.stroke();
  }
};
YouconixGraph.Arch = class Arch extends YouconixGraph.FillableFigure {
  constructor(position ,radius = 20, startAngle = 0,endAngle = 0.5){
    super();
    this.position = position;
    this.radius = radius;
    this.startAngle = startAngle*Math.PI;
    this.endAngle = endAngle*Math.PI;
  }
  draw(canvas){
    let context = canvas.getCanvas();
	
    context.beginPath();
    context.arc(this.position.getX(),this.position.getY(), this.radius, this.startAngle, this.endAngle, false);
    
    if (this.doFill) {
      context.globalAlpha = this.transparenty;
      context.fillStyle = this.fillColor;
      context.fill();
    }
    context.strokeStyle = this.color;
    context.stroke();
  }
};
YouconixGraph.PiePiece = class PiePiece extends YouconixGraph.FillableFigure {
  constructor(position, radius = 20, startAngle = 0, endAngle = 0.5){
    super();
    this.position = position;
    this.radius = radius;
    this.leftPosition = this.getPosition(startAngle);
    this.rightPosition = this.getPosition(endAngle);
    this.startAngle = (startAngle/180*Math.PI);
    this.endAngle = (endAngle/180*Math.PI);
  }
  getPosition(angle){
    if( angle === 0 || angle === 360 ){
      return new YouconixGraph.Position(this.position.getX() + this.radius , this.position.getY());
    }
    if( angle === 180 ){
      return new YouconixGraph.Position(this.position.getX() - this.radius , this.position.getY());
    }
    if( angle === 90 ){
      return new YouconixGraph.Position(this.position.getX() , this.position.getY() + this.radius);
    }
    if( angle === 270 ){
      return new YouconixGraph.Position(this.position.getX() , this.position.getY() - this.radius);
    }
	
    let rad=0,angle2=0;
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
    let vertSide, horSide;
	
    vertSide = (Math.sin(rad)*this.radius);
    horSide = (Math.cos(rad)*this.radius);
	
    if( angle < 90 ){
      return new YouconixGraph.Position(this.position.getX() + horSide , this.position.getY() + vertSide);
    }
    if( angle < 180 ){
      return new YouconixGraph.Position(this.position.getX() - horSide , this.position.getY() + vertSide);
    }
    if( angle < 270 ){
      return new YouconixGraph.Position(this.position.getX() - horSide , this.position.getY() - vertSide);
    }
    return new YouconixGraph.Position(this.position.getX() + horSide , this.position.getY() - vertSide);
  }
  draw(canvas){
    let context = canvas.getCanvas();
	
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
    
    if (this.doFill) {
      context.globalAlpha = this.transparenty;
      context.fillStyle = this.fillColor;
      context.fill();
    }
    context.strokeStyle = this.color;
    context.stroke();
  }
};
YouconixGraph.Rectangle = class Rectangle extends YouconixGraph.FillableFigure {
  constructor(start, end){
    super();
    
    this.start = start;
    this.end = end;
  }
  draw(canvas){
    let context = canvas.getCanvas();
	
    context.beginPath();
    context.rect(this.start.getX(),this.start.getY(),(this.end.getX()-this.start.getX()),(this.end.getY()-this.start.getY()) );
    
    if (this.doFill) {
      context.globalAlpha = this.transparenty;
      context.fillStyle = this.fillColor;
      context.fill();
    }
    context.strokeStyle = this.color;
    context.stroke();
  }
};
YouconixGraph.Text = class Text extends YouconixGraph.Figure {
  constructor(position,text,text_style = 'normal',text_size = '12px',font = 'Verdana'){
    super();
    
    this.position = position;
    this.text = text;
    this.font = text_style+' '+text_size+' '+font;
    this.rotation = 6;//no rotation
  }
  draw(canvas){
    let context = canvas.getCanvas();
	
    context.font = this.font;
    context.fillStyle = this.color;
    //context.rotate(Math.PI*2/(this.rotation*6));
    context.fillText(this.text, this.position.getX(), this.position.getY());
  }
};
YouconixGraph.diagrams = {};