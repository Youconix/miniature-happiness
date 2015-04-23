<section id="settings">
    <section class="item_header">
       <h1 id="settings_general_title">{generalTitle}</h1>
    </section>
    
    <section class="item_body">        
        <h2 id="notice" class="notice"></h2>
        
        <fieldset>
            <label class="label" for="name_site">{nameSiteText} *</label>
            <input type="text" id="name_site" name="name_site" value="{nameSite}" data-error-message="{nameSiteError}" required>
        </fieldset>
        <fieldset>
            <label class="label" for="site_url">{siteUrlText} *</label>
            <input type="text" id="site_url" name="site_url" value="{siteUrl}" data-error-message="{siteUrlError}" required>
        </fieldset>
        <fieldset>
            <label class="label" for="site_base">{siteBaseText}</label>
            <input type="text" id="site_base" name="site_base" value="{siteBase}">
        </fieldset>
        <fieldset>
            <label class="label" for="timezone">{timezoneText} *</label>
            <input type="text" id="timezone" name="timezone" value="{timezone}" pattern="^[A-Z]{1}[a-z]+/{1}[A-Z]{1}[a-z]+$" data-error-message="{timezoneError}" required>
        </fieldset>
        <fieldset><br></fieldset>
        <fieldset>
            <label class="label" for="template">{templateText} *</label>
            <select id="template" name="template">
            <block {template}>
                <option value="{value}" {selected}>{text}</option>
            </block>
            </select>
        </fieldset>
        <fieldset><br></fieldset>
        <fieldset>
            <label class="label" for="logger">{loggerText} *</label>
            <input type="text" list="logger_list" id="logger" name="logger" value="{logger}" data-error-message="{loggerError}" required>
            <datalist id="logger_list">
            <option label="Default" value="default">
            <option label="Syslog" value="syslog">
            <option label="Errorlog" value="errorlog">
            </datalist>
        </fieldset>
        <div id="location_log_default" {location_log_default}>
        <fieldset>
            <label class="label" for="log_location">{logLocationText} *</label>
            <input type="text" id="log_location" name="log_location" value="{logLocation}" data-error-message="{logLocationError}" required>
        </fieldset>
        </div>
        <fieldset>
            <label class="label" for="log_size">{logSizeText} *</label>
            <input type="number" id="log_size" name="log_size" value="{logSize}" min="1000" step="1000" data-error-message="{logSizeError}" required>
        </fieldset>
        
        <p><input type="button" id="settings_general_save" value="{saveButton}"></p>
    </section>
</section>