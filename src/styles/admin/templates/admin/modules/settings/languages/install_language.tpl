<section id="settings">
    <section class="item_header">
       <h1 id="settings_language_title">{languageTitle}</h1>
    </section>
    
    <section class="item_body">
        <h2 id="notice" class="notice"></h2>
        
        <if {file_available}>
            <h2>Beschikbare talen</h2>
	        
	        <block {language}>
	           <fieldset>
	               <input type="checkbox" value="{location}" data-name="{name}" name="newLanguage" {disabled}>
	               <label class="label">{name}</label>
		        </fieldset>
		    </block>
	        
        
            <p><input type="button" id="settings_language_install" value="{installButton}"></p>
        </if>
        <else>
            <h2 class="errorNotice">404 Server not found</h2>
        
            <p class="errorNotice">Could not reach remote server</p>
        </else>
    </section>
</section>