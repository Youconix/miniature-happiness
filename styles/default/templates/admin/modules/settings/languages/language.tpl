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
        
        <p><input type="button" id="settings_database_save" value="{saveButton}"></p>
    </section>
</section>