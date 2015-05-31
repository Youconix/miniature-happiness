"use strict";

function Statistics(){
	this.address = '../../admin/modules/statistics/stats/';
}
Statistics.prototype.init = function(){
	$('head').append('<script src="../../js/youconix/youconix_graph.js"></script>');
	$('head').append('<script src="../../js/youconix/youconix_graph_diagrams.js"></script>');
	
	$('#admin_statistics_hits h2').click(function(){
		admin.show(statistics.address+'hits',statistics.hits);
	});
	$('#admin_statistics_hits_hours h2').click(function(){
		admin.show(statistics.address+'hits_hours',statistics.hitsHour);
	});
	$('#admin_statistics_os h2').click(function(){
		admin.show(statistics.address+'os',statistics.os);
	});
	$('#admin_statistics_browsers h2').click(function(){
		admin.show(statistics.address+'browser',statistics.browser);
	});
	$('#admin_statistics_screen_colors h2').click(function(){
		admin.show(statistics.address+'screencolors',statistics.screenColors);
	});
	$('#admin_statistics_screen_sizes h2').click(function(){
		admin.show(statistics.address+'screensizes',statistics.screenSizes);
	});
	$('#admin_statistics_references h2').click(function(){
		admin.show(statistics.address+'references',statistics.references);
	});
	$('#admin_statistics_pages h2').click(function(){
		admin.show(statistics.address+'pages',statistics.pages);
	});
}
Statistics.prototype.hits = function(){	
	var canvas = new YouconixGraph.canvas('hitsCanvas');
	var diagram = new YouconixGraph.plugins.diagrams.Line(hits,lines,labels);
	diagram.draw(canvas);
}
Statistics.prototype.hitsHour = function(){
	var canvas = new YouconixGraph.canvas('hitsCanvas');
	var diagram = new YouconixGraph.plugins.diagrams.Bar(hits,lines,labels);
	diagram.draw(canvas);
}
Statistics.prototype.os = function(){
	var canvas = new YouconixGraph.canvas('osCanvas');
	var diagram = new YouconixGraph.plugins.diagrams.Circle(os,lines,1);
	diagram.draw(canvas);
}
Statistics.prototype.browser = function(){
	var canvas = new YouconixGraph.canvas('browserCanvas');
	var diagram = new YouconixGraph.plugins.diagrams.Circle(browser,lines,1);
	diagram.draw(canvas);
}
Statistics.prototype.screenColors	= function(){
	var canvas = new YouconixGraph.canvas('screenColorsCanvas');
	var diagram = new YouconixGraph.plugins.diagrams.BarHorizontal(screenColors,lines);
	diagram.draw(canvas);
}
Statistics.prototype.screenSizes	= function(){
	var canvas = new YouconixGraph.canvas('screenSizesCanvas');
	var diagram = new YouconixGraph.plugins.diagrams.BarHorizontal(screenSizes,lines);
	diagram.draw(canvas);
}
Statistics.prototype.references	= function(){
	var canvas = new YouconixGraph.canvas('referencesCanvas');
	var diagram = new YouconixGraph.plugins.diagrams.BarHorizontal(references,lines);
	diagram.draw(canvas);
}
Statistics.prototype.pages	= function(){
	var canvas = new YouconixGraph.canvas('pagesCanvas');
	var diagram = new YouconixGraph.plugins.diagrams.BarHorizontal(pages,lines);
	diagram.draw(canvas);
}

var statistics = new Statistics();
$(document).ready(function() {
	statistics.init();
});
