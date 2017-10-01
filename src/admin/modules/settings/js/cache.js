function SettingsCache(){
	this.address = '../../admin/modules/settings/cache/';
}
SettingsCache.prototype.init = function(){
	$('#cacheActive').on('change',function(){
		if( $(this).is(':checked') ){
			$('#cacheSettings').show();
		}
		else {
			$('#cacheSettings').hide();
		}
	});
	$('#settings_cache_save').click(function(){
		settingsCache.cacheSave();
	});
	$('#no_cache_submit').click(function(){
		settingsCache.addNoCache();  
	});
	$('#nonCacheList tr').click(function(){
		var item = $(this);
		settingsCache.deleteNoCache(item);
	});
}
SettingsCache.prototype.cacheSave	= function(){
	if( !validation.html5Validation('expire') ){
		return;
	}
	var data = {
		 'cache' : 0, 'expire' : $('#expire').val()
	};
	if( $('#cacheActive').is(':checked') ){
		data['cache'] = 1;
	}
  
	$.post(settingsCache.address+'cache',data);
	$('#notice').addClass('notice').html(languageAdmin.admin_settings_saved);
}
SettingsCache.prototype.addNoCache	= function(){
	var cacheItem = $.trim($('#noCachePage').val());
	if( cacheItem == '' ){
		return;
	}
	
	$('#noCachePage').val('');
	$.post(settingsCache.address+'addNoCache',{'page':cacheItem},function(response){
		response = JSON.parse(response);
		if( response.id != -1 ){
			$('#nonCacheList').append('<tr data-id="'+response.id+'"> '+
			'  <td><img src="'+response.style_dir+'images/icons/delete.png" alt="'+response.deleteText+'" title="'+response.deleteText+'"></td>'+
			'  <td>'+response.name+'</td> '+
			'</tr>');
		}
	});
}
SettingsCache.prototype.deleteNoCache	= function(item){
	var id = item.data('id');
	confirmBox.init(350,function(){
		item.remove();
		$.post(settingsCache.address+'deleteNoCache',{'id':id});
	});
	confirmbox.show('Cache pagina verwijderen',languageAdmin.cache_cache_again);
}
var settingsCache = new SettingsCache();