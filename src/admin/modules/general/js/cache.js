function Cache() {
  this.address = '../modules/general/cache/';
}
Cache.prototype.init = function () {
  $('#admin_general_cache_language').click(function () {
    confirmBox.init(350, cache.language);
    confirmBox.show(languageAdmin.cache_title, languageAdmin.cache_language);
  });
  $('#admin_general_cache_site').click(function () {
    confirmBox.init(350, cache.site);
    confirmBox.show(languageAdmin.cache_title, languageAdmin.cache_site);
  });
}
Cache.prototype.language = function () {
  $.post(cache.address + 'language');
}
Cache.prototype.site = function () {
  $.post(cache.address + 'site');
}

var cache = new Cache();