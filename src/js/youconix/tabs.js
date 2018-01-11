class Tabs {
  constructor(){
    this.current = -1;
    this.config = null;
    this.storageKey;
  }
  init(config){
    if( !config || !config.hasOwnProperty('id') ){
      return;
    }
    
    this.storageKey = 'tab_'+config['id']+'_start';
    config['start'] = this.getDefaultTab(config);
    this.config = config;

    $('#'+config['id']+' .tab_header div').each((i, item) => {
      $(item).click((event) => {
	let tab = $(event.currentTarget);
	let id = tab.data('id');
		
	this.clear(tab);
	this.click(id);
      });
    });
    
    $('#'+config['id']+' .tab_header div').each((i, item) => {
      item = $(item);
      if (item.data('id') == config['start']){
	item.trigger('click');
	return true;
      }
    });
  }
  getDefaultTab(config){
    let tabId = 1;
    
    if( config.hasOwnProperty('start') ){
      tabId = config['start'];
    }
    else if( window.localStorage && (window.localStorage.getItem(this.storageKey) !== null)){
      tabId = window.localStorage.getItem(this.storageKey);
    }
    
    this.setCurrentTab(tabId);
    return tabId;
  }
  setCurrentTab(id){
    window.localStorage.setItem(this.storageKey, id);
  }
  click(id){
    $('#tab_'+this.current).css('display','none');
    $('#tab_'+id).css('display','block');
	
    this.current = id;
    this.setCurrentTab(id);
  }
  clear(item){
    $('#'+this.config['id']+' .tab_header div.tab_header_active').removeClass('tab_header_active');
    item.addClass('tab_header_active');
  }
}

new Tabs();