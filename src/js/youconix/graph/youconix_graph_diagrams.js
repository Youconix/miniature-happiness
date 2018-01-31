YouconixGraph.diagrams = {};
YouconixGraph.diagrams.Figure = class Figure {
  constructor(canvas, data,lines,labels){
    this.canvas = canvas;
    this.data = new Array();
    this.lines = lines;
    this.max;
    this.step = 1;
    this.labels = labels;	
    this.items = new Array();	
    this.canvasHeight;
    this.canvasWidth;
    this.intervalY;
    this.legendaTextSize = 12;
    this.marginLeft = 100;
    
    this.canvasHeight = parseInt(canvas.getHeight().replace('px',''));
    this.canvasWidth = (parseInt(canvas.getWidth().replace('px',''))-15);
    this.intervalY = this.canvasHeight/11;
    this.parse(data);
  }
  parse(data){
    let max = 0;
    let steps;
	
    for(let collection in data){
      this.data.push(data[collection]);		
      steps = 0;
      for( let i in data[collection]){
	if( data[collection][i] > max ){
	  max = parseInt(data[collection][i]);
	}
	steps++;
      }
    }
	
    this.max = max;
    this.step = Math.round(max/10);    
    
    
  }
  drawLegenda(){
    let textSize = this.legendaTextSize + 4;
    let top = this.canvasHeight - (this.lines.length * textSize);
    let textItem;
    for( let i in this.lines ){
      textItem = new YouconixGraph.Text(new YouconixGraph.Position(10,top),this.lines[i].text);
      textItem.setColor(this.lines[i].color);
      textItem.draw(this.canvas);
      
      top += textSize;
    }
    
    this.canvasHeight -= ((this.lines.length+2) * textSize);    
    this.intervalY = this.canvasHeight/11;
  }
  drawVerticalLine(){    
    var line = new YouconixGraph.Line(
      new YouconixGraph.Position(this.marginLeft,0 + this.intervalY),
      new YouconixGraph.Position(this.marginLeft,this.canvasHeight) 
    );
    line.draw(this.canvas);
  }
  drawVerticalLineItems(){
    let step = this.max/10;
	
    let top;
    let text;
    let textItem, line;
    for(let i=0; i<10; i++){
      top = (i*this.intervalY)+this.intervalY;
      text = Math.round(this.max-step*i);
      textItem = new YouconixGraph.Text(new YouconixGraph.Position((this.marginLeft-30),top),text );
      textItem.draw(this.canvas);
		
      line = new YouconixGraph.Line(
	new YouconixGraph.Position((this.marginLeft-5),top),
	new YouconixGraph.Position((this.marginLeft+5),top)
      );
      line.draw(this.canvas);
    }
  }
  drawHorizontalLine(){
    let line = new YouconixGraph.Line(
      new YouconixGraph.Position(this.marginLeft,this.canvasHeight),
      new YouconixGraph.Position(this.canvasWidth+5,this.canvasHeight)
    );
    line.draw(this.canvas);
  }
  drawHorizontalLineItems(){
    let step = Math.round((this.canvasWidth-this.marginLeft)/this.labels.length);
    let width, line, textItem;
    for( let i in this.labels ){
      width = (step*i)+step;
    		
      line = new YouconixGraph.Line(
	new YouconixGraph.Position((this.marginLeft+width),(this.canvasHeight - 5) ),
	new YouconixGraph.Position((this.marginLeft+width),(this.canvasHeight + 5) )
      );
      line.draw(this.canvas);
		
      textItem = new YouconixGraph.Text(
	new YouconixGraph.Position((this.marginLeft+width-10),this.canvasHeight + 20 ),
	this.labels[i]
      );
      textItem.draw(this.canvas);
    }
  }
  drawItems(){}
  draw(){	
    this.drawLegenda();
    this.drawVerticalLine();
    this.drawVerticalLineItems();
    this.drawHorizontalLine();
    this.drawHorizontalLineItems();
    this.drawItems();
  }
};
YouconixGraph.diagrams.Line = class Line extends YouconixGraph.diagrams.Figure {  
  drawItems(){
    let step = Math.round((this.canvasWidth-this.marginLeft)/this.labels.length);
    let stepVert = (this.canvasHeight-this.intervalY)/this.max;
    let itemHeight;
    let lastPos = null,pos,line,circle, textItem, width;
    for( let i in this.data ){
      lastPos = null;
		
      for( let j in this.data[i] ){
	width = (step*j)+step;			
	itemHeight = ((this.max-this.data[i][j])*stepVert+this.intervalY);
			
	textItem = new YouconixGraph.Text(
	  new YouconixGraph.Position(this.marginLeft+width-5,itemHeight-10),
	  this.data[i][j]
	);
	textItem.draw(this.canvas);
			
	pos = new YouconixGraph.Position((this.marginLeft+width),itemHeight );
	circle = new YouconixGraph.Point(pos);
	circle.setFill(true).setRadius(3).setColor(this.lines[i].color).setFillColor(this.lines[i].color);
	circle.draw(this.canvas);
			
	if( lastPos != null ){
	  line = new YouconixGraph.Line(lastPos,pos);
	  line.setColor(this.lines[i].color);
	  line.draw(this.canvas);
	}
			
	lastPos = pos;
      }
    }
  }
};
YouconixGraph.diagrams.Bar = class Bar extends YouconixGraph.diagrams.Figure {
  constructor(canvas, data,lines,labels){
    super(canvas, data, lines, labels);
    
    let step = Math.round((this.canvasWidth-this.marginLeft)/this.labels.length);
    this.barWidth = (step - 30);
  }
  parse(data){
    let max = 0;
    for(let collection in data[0]){
      this.data.push(data[0][collection]);		
      if( data[0][collection] > max ){
	max = parseInt(data[0][collection]);
      }
    }

    this.max = max;
    this.step = Math.round(max/10);
  }
  drawVerticalLine(){    
    var line = new YouconixGraph.FreeLine();
    line.addPosition(new YouconixGraph.Position(this.marginLeft,0 + this.intervalY-5))
      .addPosition(new YouconixGraph.Position(this.marginLeft,this.canvasHeight))
      .addPosition(new YouconixGraph.Position(this.marginLeft+10,this.canvasHeight-10))
      .addPosition(new YouconixGraph.Position(this.marginLeft+10,0 + this.intervalY-15))
      .addPosition(new YouconixGraph.Position(this.marginLeft,0 + this.intervalY-5));
    line.draw(this.canvas);
  }
  drawVerticalLineItems(){
    let step = this.max/10;
	
    let top;
    let text;
    let textItem, line;
    for(let i=0; i<10; i++){
      top = (i*this.intervalY)+this.intervalY;
      text = Math.round(this.max-step*i);
      textItem = new YouconixGraph.Text(new YouconixGraph.Position((this.marginLeft-30),top),text );
      textItem.draw(this.canvas);
		
      line = new YouconixGraph.FreeLine();
      line.addPosition(new YouconixGraph.Position(this.marginLeft-10,top-5))
	.addPosition(new YouconixGraph.Position(this.marginLeft,top-5))
      line.draw(this.canvas);
    }
  }
  drawHorizontalLine(){
    let line = new YouconixGraph.FreeLine();
    line.addPosition(new YouconixGraph.Position(this.marginLeft,this.canvasHeight))
      .addPosition(new YouconixGraph.Position(this.canvasWidth+10,this.canvasHeight))
      .addPosition(new YouconixGraph.Position(this.canvasWidth+20,this.canvasHeight-10))
      .addPosition(new YouconixGraph.Position(this.marginLeft+10,this.canvasHeight-10))
      .addPosition(new YouconixGraph.Position(this.marginLeft,this.canvasHeight));
    line.draw(this.canvas);
  }
  drawHorizontalLineItems(){
    let step = Math.round((this.canvasWidth-this.marginLeft)/this.labels.length);
    let width, line, textItem;
    for( let i in this.labels ){
      width = (step*i)+step;
    		
      line = new YouconixGraph.FreeLine();
      line.addPosition(new YouconixGraph.Position((this.marginLeft+width+5),(this.canvasHeight) ))
	.addPosition(new YouconixGraph.Position((this.marginLeft+width+5),(this.canvasHeight + 10) ))
      line.draw(this.canvas);
		
      textItem = new YouconixGraph.Text(
	new YouconixGraph.Position((this.marginLeft+width-5),this.canvasHeight + 20 ),
	this.labels[i]
      );
      textItem.draw(this.canvas);
    }
  }
  drawItems(){
    let step = Math.round((this.canvasWidth-this.marginLeft)/this.labels.length);
    let offset = (step/2)-5;
    let stepVert = (this.canvasHeight-this.intervalY)/this.max;
    let itemHeight, barItem, textItem,width, line;
    for(let i in this.data ){
      width = (step*i)+step;			
      itemHeight = ((this.data[i])*stepVert);
			
      textItem = new YouconixGraph.Text(
	new YouconixGraph.Position(this.marginLeft+width-(offset/2)+5,this.canvasHeight-itemHeight-15),
	this.data[i]
      );
      textItem.draw(this.canvas);
			
      barItem = new YouconixGraph.Rectangle(
	new YouconixGraph.Position((this.marginLeft+width-offset),this.canvasHeight ),
	new YouconixGraph.Position((this.marginLeft+width+offset),this.canvasHeight-itemHeight )
      );
      barItem.setColor(this.lines[0].color).setFillColor(this.lines[0].color).setFill(true);
      barItem.draw(this.canvas);
      
      line = new YouconixGraph.FreeLine();
      line.setColor(this.lines[0].color).setFillColor(this.lines[0].color).setFill(true).setTransparenty(0.7)
	      .addPosition(new YouconixGraph.Position((this.marginLeft+width-offset),(this.canvasHeight-itemHeight)))
	      .addPosition(new YouconixGraph.Position((this.marginLeft+width+offset),(this.canvasHeight-itemHeight )))
	      .addPosition(new YouconixGraph.Position((this.marginLeft+width+offset+10),(this.canvasHeight-itemHeight-10)))
	      .addPosition(new YouconixGraph.Position((this.marginLeft+width-offset+10),(this.canvasHeight-itemHeight-10)))
	      .addPosition(new YouconixGraph.Position((this.marginLeft+width-offset),(this.canvasHeight-itemHeight)));
      line.draw(this.canvas);
      
      line = new YouconixGraph.FreeLine();
      line.setColor(this.lines[0].color).setFillColor(this.lines[0].color).setFill(true).setTransparenty(0.7)
	      .addPosition(new YouconixGraph.Position((this.marginLeft+width+offset),this.canvasHeight-itemHeight ))
	      .addPosition(new YouconixGraph.Position((this.marginLeft+width+offset),this.canvasHeight))
	      .addPosition(new YouconixGraph.Position((this.marginLeft+width+offset+10),this.canvasHeight-10))
	      .addPosition(new YouconixGraph.Position((this.marginLeft+width+offset+10),this.canvasHeight-itemHeight-10))
	      .addPosition(new YouconixGraph.Position((this.marginLeft+width+offset),this.canvasHeight-itemHeight ));
      line.draw(this.canvas);
    }
  }
};
YouconixGraph.diagrams.Circle = class Circle extends YouconixGraph.diagrams.Figure { 
  parse(data){
    let total = 0;
    for(let i in data){
      total += parseInt(data[i]['amount']);
    }
    this.data = data;
    for(let i in data){
      this.data[i]['percentage'] = ( Math.round(parseInt(data[i]['amount'])/total*100*this.decimals)/this.decimals);
    }
  }
  draw(){
    this.drawLegenda();
    this.drawItems();
  }
  drawItems(){	
    let radius = 200;
    let position =  new YouconixGraph.position(600,(250));
	
    /* Draw pieces */
    let pos,piece,lastPos = 0;
    let gradesPercentage = (360/100);
    let count = 0;
    for( let i in this.data ){
      pos = ((Math.round((gradesPercentage*this.data[i]['percentage'])*10)/10)+lastPos);
		
      piece = new YouconixGraph.PiePiece(position,radius,lastPos,pos);
      piece.setColor(this.lines[i].color);
      piece.draw(canvas);
		
      lastPos = pos;
      count++;
    }
  }
};  
YouconixGraph.diagrams.BarHorizontal = class BarHorizontal extends YouconixGraph.diagrams.Figure {
  constructor(canvas, data,lines){
    super(canvas, data, lines, {});
    
    let step = Math.round((this.canvasWidth-this.marginLeft)/this.labels.length);
    this.barWidth = (step - 30);
    this.barHeight = 50;
    this.margin = 10;
  }
  parse(data){
    let max = 0;
    let total = 0;
    for(let collection in data){
      this.data.push(data[collection].amount);
      if( data[collection].amount > max ){
	max = parseInt(data[collection].amount);
      }
      total += parseInt(data[collection].amount);
    }

    this.max = max;
    this.total = total;
    this.step = Math.round(max/10);
  }
  drawLegenda(){    
    let textSize = this.legendaTextSize + 4;
    let legendaHeight = ((this.lines.length+2) * textSize);
    
    this.canvasHeight = ((this.data.length+1)*(this.barHeight+2*this.margin)+legendaHeight);
    this.canvas.setHeight(this.canvasHeight);
    this.canvas.clear();
    
    let top = this.canvasHeight - (this.lines.length * textSize);
    let textItem;
    for( let i in this.lines ){
      textItem = new YouconixGraph.Text(new YouconixGraph.Position(10,top),this.lines[i].text);
      textItem.setColor(this.lines[i].color);
      textItem.draw(this.canvas);
      
      top += textSize;
    }
    
    this.canvasHeight -= legendaHeight;
    this.intervalY = (this.barHeight+2*this.margin);
  }
  drawVerticalLine(){    
    var line = new YouconixGraph.FreeLine();
    line.addPosition(new YouconixGraph.Position(this.marginLeft,0 + this.intervalY-5))
      .addPosition(new YouconixGraph.Position(this.marginLeft,this.canvasHeight))
      .addPosition(new YouconixGraph.Position(this.marginLeft+10,this.canvasHeight-10))
      .addPosition(new YouconixGraph.Position(this.marginLeft+10,0 + this.intervalY-15))
      .addPosition(new YouconixGraph.Position(this.marginLeft,0 + this.intervalY-5));
    line.draw(this.canvas);
  }
  drawHorizontalLine(){
    let line = new YouconixGraph.FreeLine();
    line.addPosition(new YouconixGraph.Position(this.marginLeft,this.canvasHeight))
      .addPosition(new YouconixGraph.Position(this.canvasWidth+10,this.canvasHeight))
      .addPosition(new YouconixGraph.Position(this.canvasWidth+20,this.canvasHeight-10))
      .addPosition(new YouconixGraph.Position(this.marginLeft+10,this.canvasHeight-10))
      .addPosition(new YouconixGraph.Position(this.marginLeft,this.canvasHeight));
    line.draw(this.canvas);
  }
  drawItems(){
    let barItem,itemWidth,top, text, textItem;
    let textOffset = Math.floor(this.barHeight/2);
    for( let i in this.data ){
      top = (this.intervalY+(i*this.intervalY)+this.margin);
      text = (this.data[i]/this.total*100);
      text = (Math.round(text*10)/10);
      itemWidth = Math.round((this.canvasWidth-this.marginLeft)*(text/100));
			
      barItem = new YouconixGraph.Rectangle(
	new YouconixGraph.Position(this.marginLeft,top ),
	new YouconixGraph.Position(itemWidth,top+this.barHeight )
      );
      barItem.setColor(this.lines[i].color).setFillColor(this.lines[i].color).setFill(true);
      barItem.draw(this.canvas);
      
      barItem = new YouconixGraph.FreeLine();
      barItem.addPosition(new YouconixGraph.Position(this.marginLeft,top))
	      .addPosition(new YouconixGraph.Position(itemWidth,top))
	      .addPosition(new YouconixGraph.Position(itemWidth,(top+this.barHeight)))
	      .addPosition(new YouconixGraph.Position((itemWidth+10),(top+this.barHeight-10)))
	      .addPosition(new YouconixGraph.Position((itemWidth+10),(top-10)))
	      .addPosition(new YouconixGraph.Position((this.marginLeft+10),(top-10)))
	      .addPosition(new YouconixGraph.Position(this.marginLeft,top))
	      .setColor(this.lines[i].color).setFillColor(this.lines[i].color)
	      .setFill(true).setTransparenty(0.7)
      barItem.draw(this.canvas);
      
      textItem = new YouconixGraph.Text(new YouconixGraph.Position((20+this.marginLeft),(top+textOffset)) ,text+'%' );
      if (itemWidth > 30){
	textItem.setColor('FFF');
      }
      textItem.draw(this.canvas);
    }
  }
  draw(){	
    this.drawLegenda();
    this.drawVerticalLine();
    this.drawHorizontalLine();
    this.drawItems();
  }
};