<section id="settings">
    <section class="item_header">
       <h1 id="settings_language_title">{languageTitle}</h1>
    </section>
    
    <section class="item_body">
        <h2 id="notice" class="notice"></h2>
        
        <fieldset>
            <label class="label" for="default_language">{defaultLanguageText} *</label>
            <select id="defaultLanguage" name="defaultLanguage">
            <block {defaultLanguage}>
            	<option value="{value}" {selected}>{text}</option>
            </block>
            </select>
        </fieldset>
        
        <div id="language_list_block">
	        <h2>Ge&iuml;nstalleerde talen</h2>
	        
	        <ul id="language_list">
	        <block {language}>
	            <li>{text}</li>
	        </block>
	        </ul>
	        
	        <p id="install_new_languages">Meer talen installeren</p>
        </div>
        
        <p><input type="button" id="settings_database_save" value="{saveButton}"></p>
    </section>
</section>