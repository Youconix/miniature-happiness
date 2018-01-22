class SettingsNoCache {
  constructor(language, confirmBox) {
    this.language = language;
    this.confirmBox = confirmBox;
  }
  init() {
    this.form = $('#no_cache_form');
    this.address = this.form.prop('action');
    this.noCacheList = $('#nonCacheList');
    this.deleteAddress = this.noCacheList.data('path');
    this.styleDir = this.noCacheList.data('styledir');
    this.deleteText = this.noCacheList.data('delete');
    this.noCache = $('#noCachePage');

    $('#no_cache_submit').click(() => {
      this.addNoCache();
    });
    this.setNoCacheEvents();
  }
  setNoCacheEvents() {
    this.noCacheList.find('tr').each((i, tr) => {
      $(tr).off('click').click((item) => {
	item = $(item.currentTarget);
	this.deleteNoCache(item);
      });
    });
  }
  addNoCache() {
    let cacheItem = $.trim(this.noCache.val());
    if (cacheItem === '') {
      return;
    }

    $.post(this.address, {'page': cacheItem}, (response) => {
      if (response.id !== -1) {
	this.noCacheList.append('<tr data-id="' + response.id + '" data-name="' + response.name + '"> ' +
		'  <td><img src="' + this.styleDir + 'images/icons/delete.png" alt="' + this.deleteText + '" title="' + this.deleteText + '"></td>' +
		'  <td>' + response.name + '</td> ' +
		'</tr>');

	this.noCache.find('option[value="' + cacheItem + '"]').remove();
	this.noCache.val('');
	this.setNoCacheEvents();
      }
    });
  }
  deleteNoCache(item) {
    let id = item.data('id');
    let name = item.data('name');

    this.confirmBox.init(350, () => {
      item.remove();
      $.post(this.deleteAddress, {'id': id});
      this.noCache.append('<option value="' + name + '">' + name + '</option>');
    });
    this.confirmBox.show(this.language.cache_cache_again_title, this.language.cache_cache_again);
  }
}
var settingsNoCache;
$(document).ready(() => {
  settingsNoCache = new SettingsNoCache(languageAdmin, confirmBox);
});