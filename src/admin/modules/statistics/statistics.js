class Statistics{
  init(lib){
    this.lib = lib;
  }
  createCanvas(id){
    return new this.lib.Canvas(id);
  }
  visitors(visitors, lines, labels){	
    let canvas = this.createCanvas('hitsCanvas');
    let diagram = new this.lib.diagrams.Line(canvas, visitors,lines,labels);
    diagram.draw();
  }
  hitsHour(hits, lines, labels){
    let canvas = this.createCanvas('hitsCanvas');
    let diagram = new this.lib.diagrams.Bar(canvas, hits,lines,labels);
    diagram.draw();
  }
  os(os, lines){
    let canvas = this.createCanvas('osCanvas');
    let diagram = new this.lib.diagrams.BarHorizontal(canvas, os,lines);
    diagram.draw();
  }
  browser(browsers, lines){
    let canvas = this.createCanvas('browserCanvas');
    let diagram = new this.lib.diagrams.BarHorizontal(canvas, browsers,lines);
    diagram.draw();
  }
  screenColors(colors, lines){
    let canvas = this.createCanvas('screenColorsCanvas');
    let diagram = new this.lib.diagrams.BarHorizontal(canvas, colors,lines);
    diagram.draw();
  }
  screenSizes(sizes, lines){
    let canvas = this.createCanvas('screenSizesCanvas');
    let diagram = new this.lib.diagrams.BarHorizontal(canvas, sizes,lines);
    diagram.draw();
  }
  references(references, lines){
    let canvas = this.createCanvas('referencesCanvas');
    let diagram = new this.lib.diagrams.BarHorizontal(canvas, references,lines);
    diagram.draw();
  }
  pages(pages, lines){
    let canvas = this.createCanvas('pagesCanvas');
    let diagram = new this.lib.diagrams.BarHorizontal(canvas, pages,lines);
    diagram.draw(canvas);
  }
}

var statistics = new Statistics();
$(document).ready(() => {
  statistics.init(YouconixGraph);
});