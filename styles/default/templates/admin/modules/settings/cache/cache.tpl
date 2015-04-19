<section id="settings">
    <section class="item_header">
       <h1 id="settings_cache_title">{cacheTitle}</h1>
    </section>
    
    <section class="item_body">
        <h2 id="notice" class="notice"></h2>
        
        <fieldset>
	  <label class="label" for="cacheActive">{cacheActiveText}</label>
	  <input type="checkbox" id="cacheActive" name="cacheActive" {cacheActive}>
        </fieldset>
        <div id="cacheSettings">
        <fieldset>
	  <label class="label" for="expire">{cacheExpireText} *</label>
	  <input type="number" id="expire" name="expire" min="60" step="1" value="{cacheExpire}" required>
        </fieldset>
        </div>
        
        <p><input type="button" id="settings_cache_save" value="{saveButton}"></p>
        
        <h4>Pagina's uitgesloten van caching</h4>
        
        <table id="nonCacheList" data-styledir="{NIV}{style_dir}">
        <tbody>
        <block {noCache}>
	<tr>
	  <td data-id="{id}">{name}</td>
	  <td><img src="{NIV}{style_dir}images/icon/delete.png" alt="{delete}" title="{delete}"></td>
	</tr>
        </block>
        </tbody>
        </table>
        
        <fieldset>
	  <input type="text" id="noCachePage" placeholder="{page}"> <input type="button" id="no_cache_submit" value="{addButton}">
        </fieldset>
    </section>
</section>