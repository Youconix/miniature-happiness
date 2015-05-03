<section id="settings">
    <section class="item_header">
       <h1 id="settings_sessions_title">{generalTitle}</h1>
    </section>
    
    <section class="item_body">        
        <h2 id="notice" class="notice"></h2>
        
        <fieldset>
            <label class="label" for="session_name">{sessionNameText} *</label>
            <input type="text" id="session_name" name="session_name" value="{sessionName}" required>
        </fieldset>
        <fieldset>
            <label class="label" for="session_path">{sessionPathText} *</label>
            <input type="text" id="session_path" name="session_path" value="{sessionPath}" required>
        </fieldset>
        <fieldset>
            <label class="label" for="session_expire">{sessionExpireText} *</label>
            <input type="number" id="session_expire" name="session_expire" min="60" step="1" value="{sessionExpire}" required>
        </fieldset>
        
        <p><input type="button" id="settings_sessions_save" value="{saveButton}"></p>
    </section>
</section>