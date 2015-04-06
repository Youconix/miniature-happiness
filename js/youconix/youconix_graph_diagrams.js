"use strict";

YouconixGraph.plugins.diagrams = {};
YouconixGraph.plugins.diagrams.Line = function(data,lines,labels){
	data = data || {};
	this.data = new Array();
	this.lines = lines;
	this.max;
	this.step = 1;
	this.labels = labels;
	
	this.items = new Array();
	
	this.parse(data);
}
YouconixGraph.plugins.diagrams.Line.prototype.parse = function(data){
	var max = 0;
	
	var collection;
	var line;
	var i;
	var steps;
	
	for(collection in data){
		this.data.push(data[collection]);
		
		steps = 0;
		for( i in data[collection]){
			if( data[collection][i] > max ){
				max = data[collection][i];
			}
			steps++;
		}
	}
	
	this.max = max;
	this.step = Math.round(max/10);
}
YouconixGraph.plugins.diagrams.Line.prototype.draw = function(canvas,decimals){
	if( decimals == undefined ){ decimals = 0 }
	
	/* Legenda */
	var canvasHeight = parseInt(canvas.getHeight().replace('px',''));
	var i;
	var textitem;
	for( i in this.lines ){
		textitem = new YouconixGraph.text(new YouconixGraph.position(10,(20+(i*20))),
			this.lines[i].text,
			this.lines[i].color
		);
		textitem.draw(canvas);
	}
	
	/* Vertical line */
	var step = this.max/10;
	var intervalY = canvasHeight/12;
	var line = new YouconixGraph.line(new YouconixGraph.position('200',intervalY),
			new YouconixGraph.position('200',(canvasHeight-(intervalY*2) +intervalY)) );
	line.draw(canvas);
	
	var factor = 10 * decimals;
	if( factor == 0 ){ factor = 1 }
	
	var top;
	var text;
	for(var i=0; i<=10; i++){
		top = (i*intervalY)+intervalY;
		text = (this.max-step*i);
		text = (Math.round(text*factor)/factor);
		
		textitem = new YouconixGraph.text(new YouconixGraph.position(170,top),text );
		textitem.draw(canvas);
		
		line = new YouconixGraph.line(new YouconixGraph.position(195,top),new YouconixGraph.position(205,top) );
		line.draw(canvas);
	}
	
	/* Horizontal line */
	var width = parseInt(canvas.getWidth().replace('px',''))-15;
	line = new YouconixGraph.line(new YouconixGraph.position('200',(canvasHeight-(intervalY*2) +intervalY)),
			new YouconixGraph.position(width+5,(canvasHeight-(intervalY*2) +intervalY)) );
	line.draw(canvas);
	
	
	step = (Math.round((width-200)/this.labels.length*factor)/factor);
	for( i in this.labels ){
		width = (step*i)+step;
		
		line = new YouconixGraph.line(new YouconixGraph.position((200+width),(canvasHeight-intervalY) ),
				new YouconixGraph.position((200+width),(canvasHeight-intervalY +10)) );
		line.draw(canvas);
		
		textitem = new YouconixGraph.text(new YouconixGraph.position((200+width-5),(canvasHeight-intervalY) + 20 ),this.labels[i] );
		textitem.draw(canvas);
	}
	
	/* Draw items */
	var stepVert = (canvasHeight-(intervalY*2))/this.max;
	var itemHeight;
	var lastPos = null,pos,j,circle;
	for( i in this.data ){
		lastPos = null;
		
		for( j in this.data[i] ){
			width = (step*j)+step;			
			itemHeight = ((this.max-this.data[i][j])*stepVert+intervalY);
			
			textitem = new YouconixGraph.text(new YouconixGraph.position(200+width-5,itemHeight-10) ,this.data[i][j] );
			textitem.draw(canvas);
			
			pos = new YouconixGraph.position((200+width),itemHeight );
			circle = new YouconixGraph.point(pos,3,this.lines[i].color,this.lines[i].color);
			circle.draw(canvas);
			
			if( lastPos != null ){
				line = new YouconixGraph.line(lastPos,pos,this.lines[i].color);
				line.draw(canvas);
			}
			
			lastPos = pos;
		}
	}
}
YouconixGraph.plugins.diagrams.Bar = function(data,lines,labels){
	this.data = new Array();
	this.lines = lines;
	this.labels = labels;
	this.max = 0;
	
	this.parse(data);
}
YouconixGraph.plugins.diagrams.Bar.prototype.parse = function(data){
	var max = 0;	
	var i;
	this.data = data;
		
	for( i in data){
		if( data[i] > max ){
			max = data[i];
		}
	}
	
	this.max = max;
	this.step = Math.round(max/10);
}
YouconixGraph.plugins.diagrams.Bar.prototype.draw	= function(canvas,decimals){
	if( decimals == undefined ){ decimals = 0 }
		
	/* Vertical line */
	var canvasHeight = parseInt(canvas.getHeight().replace('px',''));
	var step = this.max/10;
	var intervalY = canvasHeight/12;
	var line = new YouconixGraph.line(new YouconixGraph.position(70,intervalY),
			new YouconixGraph.position(70,(canvasHeight-(intervalY*2) +intervalY)) );
	line.draw(canvas);
	
	var factor = 10 * decimals;
	if( factor == 0 ){ factor = 1 }
	var top,text,textitem;
	for(var i=0; i<=10; i++){
		top = (i*intervalY)+intervalY;
		text = (this.max-step*i);
		text = (Math.round(text*factor)/factor);
		
		textitem = new YouconixGraph.text(new YouconixGraph.position(40,top),text );
		textitem.draw(canvas);
		
		line = new YouconixGraph.line(new YouconixGraph.position(65,top),new YouconixGraph.position(75,top) );
		line.draw(canvas);
	}
	
	/* Horizontal line */
	var bottom = (canvasHeight-(intervalY*2) +intervalY);
	var width = parseInt(canvas.getWidth().replace('px',''))-20;
	line = new YouconixGraph.line(new YouconixGraph.position(70,bottom),new YouconixGraph.position(width+5,bottom) );
	line.draw(canvas);
	
	
	step = (Math.round((width-70)/this.labels.length*factor)/factor);
	for( i in this.labels ){
		width = (step*i)+step;
		
		textitem = new YouconixGraph.text(new YouconixGraph.position((70+width-5),(canvasHeight-intervalY) + 20 ),this.labels[i] );
		textitem.draw(canvas);
	}
	
	/* Draw items */
	var offset = (step/2)-5;
	var stepVert = (canvasHeight-(intervalY*2))/this.max;
	var itemHeight;
	var pos,barItem;
	for( i in this.data ){
		width = (step*i)+step;			
		itemHeight = ((this.data[i])*stepVert);
			
		textitem = new YouconixGraph.text(new YouconixGraph.position(70+width-(offset/2),bottom-itemHeight-10) ,this.data[i] );
		textitem.draw(canvas);
			
		barItem = new YouconixGraph.rectangle(new YouconixGraph.position((70+width-offset),bottom ),
				new YouconixGraph.position((70+width+offset),bottom-itemHeight )
		,this.lines[0].color,this.lines[0].color);
		barItem.draw(canvas);
	}
}
YouconixGraph.plugins.diagrams.Circle = function(data,lines,decimals){
	if( decimals == undefined ){ decimals = 0 }
	
	this.data = new Array();
	this.lines = lines;
	this.max = 0;
	this.decimals = decimals;
	
	this.parse(data);
}
YouconixGraph.plugins.diagrams.Circle.prototype.parse = function(data){
	var total = 0;
	var i;
	for(i in data){
		total += parseInt(data[i]['amount']);
	}
	this.data = data;
	for(i in data){
		this.data[i]['percentage'] = ( Math.round(parseInt(data[i]['amount'])/total*100*this.decimals)/this.decimals);
	}
}
YouconixGraph.plugins.diagrams.Circle.prototype.draw	= function(canvas){	
	/* Legenda */
	var canvasHeight = parseInt(canvas.getHeight().replace('px',''));
	var i;
	var textitem;
	var count = 0;
	for( i in this.lines ){
		textitem = new YouconixGraph.text(new YouconixGraph.position(10,(20+(count*20))),
				this.lines[i].type,
				this.lines[i].color
			);
			textitem.draw(canvas);
		
		textitem = new YouconixGraph.text(new YouconixGraph.position(100,(20+(count*20))),
			this.lines[i].text,
			this.lines[i].color
		);
		textitem.draw(canvas);
		count++;
	}
	
	count = 0;
	for(i in this.data){		
		textitem = new YouconixGraph.text(new YouconixGraph.position(230,(20+(count*20))),
			this.data[i].percentage+'%',
			this.lines[i].color
		);
		textitem.draw(canvas);
		
		textitem = new YouconixGraph.text(new YouconixGraph.position(270,(20+(count*20))),
			this.data[i].amount,
			this.lines[i].color
		);
		textitem.draw(canvas);
		
		count++;
	}
	
	/* Main circle */
	var radius = 200;
	var position =  new YouconixGraph.position(600,(250));
	
	/* Draw pieces */
	var pos,piece,lastPos = 0;
	var gradesPercentage = (360/100);
	count = 0;
	for( i in this.data ){
		pos = ((Math.round((gradesPercentage*this.data[i]['percentage'])*10)/10)+lastPos);
		
		piece = new YouconixGraph.piePiece(position,radius,lastPos,pos,this.lines[i].color);
		piece.draw(canvas);
		
		lastPos = pos;
		count++;
	}
}
YouconixGraph.plugins.diagrams.BarHorizontal = function(data,lines){
	this.data = new Array();
	this.lines = lines;
	this.max = 0;
	this.total = 0;
	
	this.parse(data);
}
YouconixGraph.plugins.diagrams.BarHorizontal.prototype.parse = function(data){
	var max = 0;	
	var i;
	this.data = data;
		
	for( i in data){
		if( data[i] > max ){
			max = data[i];
		}
		this.total += parseFloat(data[i]);
	}
	
	this.max = max;
	this.step = Math.round(max/10);
}
YouconixGraph.plugins.diagrams.BarHorizontal.prototype.draw	= function(canvas,decimals){
	if( decimals == undefined ){ decimals = 0 }
	
	/* Legenda */
	var canvasHeight = parseInt(canvas.getHeight().replace('px',''));
	var intervalY = canvasHeight/12;
	
	var i;
	var textitem;
	for( i in this.lines ){
		textitem = new YouconixGraph.text(new YouconixGraph.position(10,(2*intervalY+(i*intervalY))),
			this.lines[i].text,
			this.lines[i].color
		);
		textitem.draw(canvas);
	}
	
	/* Vertical line */	
	var step = this.max/10;
	var bottom = (canvasHeight-(intervalY*2) +intervalY);
	var line = new YouconixGraph.line(new YouconixGraph.position(150,intervalY),
			new YouconixGraph.position(150,bottom) );
	line.draw(canvas);
		
	/* Horizontal line */
	var width = parseInt(canvas.getWidth().replace('px',''))-40;
	line = new YouconixGraph.line(new YouconixGraph.position(150,bottom),new YouconixGraph.position(width,bottom) );
	line.draw(canvas);
	
	var factor = 10 * decimals;
	if( factor == 0 ){ factor = 1 }
	var left,text,textitem;
	var intervalX = Math.round((width-150)/10);
	var percentage;
	
	for(var i=1; i<=10; i++){
		left = (i*intervalX)+150;
		text = (10*i)+'%';
		
		textitem = new YouconixGraph.text(new YouconixGraph.position(left-5,bottom+20),text );
		textitem.draw(canvas);
		
		line = new YouconixGraph.line(new YouconixGraph.position(left,bottom-5),new YouconixGraph.position(left,bottom+5) );
		line.draw(canvas);
	}
	
	/* Draw items */
	var pos,barItem,itemWidth,top;
	for( i in this.data ){
		top = (2*intervalY+(i*intervalY));
		itemWidth = Math.round((this.data[i]/this.total*10)*intervalX)+150;
		
		text = this.data[i];
			
		textitem = new YouconixGraph.text(new YouconixGraph.position(20+itemWidth,top) ,text );
		textitem.draw(canvas);
			
		barItem = new YouconixGraph.rectangle(new YouconixGraph.position(150,top-8 ),
				new YouconixGraph.position(itemWidth,top+8 )
		,this.lines[0].color,this.lines[i].color);
		barItem.draw(canvas);
	}
}