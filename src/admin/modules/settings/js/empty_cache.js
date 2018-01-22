class SettingsEmptyCache{
  init(){
    this.form = $('#remove_caches');
    this.address = this.form.prop('action');
    this.processNotice = $('#delete_process');
    this.processDone = $('#delete_done');
    this.button = $('#settings_cache_empty');
    
    this.button.click(() => {
      this.emptyCache();
    });
  }
  emptyCache(){
    this.processNotice.show();
    this.button.hide();
    
    setTimeout(() => {
      $.post(this.address,{}, () => {
	this.processNotice.hide();
	this.processDone.show();
	this.button.show();
      });
    },1500);
  }
}
var settingsEmptyCache = new SettingsEmptyCache();