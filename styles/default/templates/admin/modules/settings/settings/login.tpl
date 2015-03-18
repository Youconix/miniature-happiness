<section id="settings">
    <section class="item_header">
       <h1 id="settings_login_title">{generalTitle}</h1>
    </section>
    
    <section class="item_body">        
        <h2 id="notice" class="notice"></h2>
        
       <fieldset>
            <label class="label" for="login_redirect">{loginRedirectText} *</label>
            <input type="text" id="login_redirect" name="login_redirect" value="{loginRedirect}" data-error-message="{redirectError}" required>
        </fieldset>
        <fieldset>
            <label class="label" for="logout_redirect">{logoutRedirectText} *</label>
            <input type="text" id="logout_redirect" name="logout_redirect" value="{logoutRedirect}" data-error-message="{redirectError}" required>
        </fieldset>
        <fieldset>
            <label class="label" for="registration_redirect">{registrationRedirectText} *</label>
            <input type="text" id="registration_redirect" name="registration_redirect" value="{registrationRedirect}" data-error-message="{redirectError}" required>
        </fieldset>
        <fieldset>
            <label class="label" for="normal_login">{normalLoginText} **</label>
            <input type="checkbox" id="normal_login" name="logger" value="1" {normalLogin}>
        </fieldset>
        <fieldset>
            <label class="label" for="openid_login">{openidLoginText} **</label>
            <input type="checkbox" id="openid_login" name="openid_login" value="1" {openidLogin}>
        </fieldset>
        <fieldset>
            <label class="label" for="facebook_login">{facebookLoginText} **</label>
            <input type="checkbox" id="facebook_login" name="facebook_login" value="{facebookLogin}">
        </fieldset>
        <div id="facebook_login_data" {facebook_login_data}>
        <fieldset>
            <label class="label" for="facebook_app_id">{facebookAppIDText} *</label>
            <input type="text" id="facebook_app_id" name="facebook_app_id" value="{facebookAppID}" data-error-message="{facebookAppError}" required>
        </fieldset>
        </div>
        <fieldset>
            <label class="label" for="ldap_login">{ldapLoginText} **</label>
            <input type="checkbox" id="ldap_login" name="ldap_login" value="{ldapLogin}">
        </fieldset>
        <div id="ldap_login_data" {ldap_login_data}>
        <fieldset>
            <label class="label" for="ldap_server">{ldapServerText} *</label>
            <input type="text" id="ldap_server" name="ldap_server" data-error-message="{ldapServerError}" value="{ldapServer}">
        </fieldset>
        <fieldset>
            <label class="label" for="ldap_port">{ldapPortText} *</label>
            <input type="number" id="ldap_port" name="ldap_port" value="{ldapPort}" data-error-message="{ldapPortError}" min="1" step="1">
        </fieldset>
        </div>
        
        <h5>** {loginChoiceText}</h5>
        
        <p><input type="button" id="settings_login_save" value="{saveButton}"></p>
    </section>
</section>