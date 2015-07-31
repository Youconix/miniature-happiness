<section id="settings">
    <section class="item_header">
       <h1 id="settings_login_title">{generalTitle}</h1>
    </section>
    
    <section class="item_body">        
        <h2 id="notice" class="notice"></h2>
        
       <!-- login redirects -->
       <fieldset>
            <label class="label" for="login_redirect">{loginRedirectText} *</label>
            <input type="text" id="login_redirect" name="login_redirect" value="{loginRedirect}" data-validation="{redirectError}" required>
        </fieldset>
        <fieldset>
            <label class="label" for="logout_redirect">{logoutRedirectText} *</label>
            <input type="text" id="logout_redirect" name="logout_redirect" value="{logoutRedirect}" data-validation="{redirectError}" required>
        </fieldset>
        <fieldset>
            <label class="label" for="registration_redirect">{registrationRedirectText} *</label>
            <input type="text" id="registration_redirect" name="registration_redirect" value="{registrationRedirect}" data-validation="{redirectError}" required>
        </fieldset>
        <fieldset></fieldset>
        
        <!-- normal login -->
        <fieldset>
            <label class="label" for="normal_login">{normalLoginText} **</label>
            <input type="checkbox" id="normal_login" name="logger" value="1" {normalLogin}>
        </fieldset>
        
        <!-- Google+ login 
        <fieldset>
            <label class="label" for="google_login">{googleLoginText} **</label>
            <input type="checkbox" id="google_login" name="google_login" value="1" {googleLogin}>
        </fieldset>
        <div id="google_login_data" {google_login_data}>
        <fieldset>
            <label class="label" for="google_app_id">{appIDText} *</label>
            <input type="text" id="google_app_id" name="google_app_id" value="{googleAppID}" data-validation="{appError}" required>
        </fieldset>
        <fieldset>
            <label class="label" for="google_app_secret">{appSecretText} *</label>
            <input type="text" id="google_app_secret" name="google_app_secret" value="{googleAppSecret}" data-validation="{appSecretError}" required>
        </fieldset>
        <fieldset></fieldset>
        </div> -->
        
        <!-- Facebook login text -->
        <fieldset>
            <label class="label" for="facebook_login">{facebookLoginText} **</label>
            <input type="checkbox" id="facebook_login" name="facebook_login" value="1" {facebookLogin}">
        </fieldset>
        <div id="facebook_login_data" {facebook_login_data}>
        <fieldset>
            <label class="label" for="facebook_app_id">{appIDText} *</label>
            <input type="text" id="facebook_app_id" name="facebook_app_id" value="{facebookAppID}" data-validation="{appError}" required>
        </fieldset>
        <fieldset>
            <label class="label" for="facebook_app_secret">{appSecretText} *</label>
            <input type="text" id="facebook_app_secret" name="facebook_app_secret" value="{facebookAppSecret}" data-validation="{appSecretError}" required>
        </fieldset>
        <fieldset></fieldset>
        </div>
        
        <!-- Twitter login 
        <fieldset>
            <label class="label" for="twitter_login">{facebookLoginText} **</label>
            <input type="checkbox" id="twitter_login" name="twitter_login" value="1" {twitterLogin}">
        </fieldset>
        <div id="twitter_login_data" {twitter_login_data}>
        <fieldset>
            <label class="label" for="twitter_app_id">{appIDText} *</label>
            <input type="text" id="twitter_app_id" name="twitter_app_id" value="{twitterAppID}" data-validation="{appError}" required>
        </fieldset>
        <fieldset>
            <label class="label" for="twitter_app_secret">{appSecretText} *</label>
            <input type="text" id="twitter_app_secret" name="twitter_app_secret" value="{twitterAppSecret}" data-validation="{appSecretError}" required>
        </fieldset>
        <fieldset></fieldset>
        </div> -->
        
        <!-- LDAP login 
        <fieldset>
            <label class="label" for="ldap_login">{ldapLoginText} **</label>
            <input type="checkbox" id="ldap_login" name="ldap_login" value="1" {ldapLogin}">
        </fieldset>
        <div id="ldap_login_data" {ldap_login_data}>
        <fieldset>
            <label class="label" for="ldap_server">{ldapServerText} *</label>
            <input type="text" id="ldap_server" name="ldap_server" data-validation="{ldapServerError}" value="{ldapServer}">
        </fieldset>
        <fieldset>
            <label class="label" for="ldap_port">{ldapPortText} *</label>
            <input type="number" id="ldap_port" name="ldap_port" value="{ldapPort}" data-validation="{ldapPortError}" min="1" step="1">
        </fieldset>
        </div> -->
        
        <h5>** {loginChoiceText}</h5>
        
        <p><input type="button" id="settings_login_save" value="{saveButton}"></p>
    </section>
</section>