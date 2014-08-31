<section id="settings">
<h1>{title}</h1>

<h2 class="errorNotice">{generalError}</h2>

<h2>General</h2>

<div>
	<fieldset>
		<label>{basedir}</label>
    	<input type="text" id="base" value="{base}">
    </fieldset>
	<fieldset>
		<label>{siteUrl} *</label>
    	<input type="url" id="url" value="{url}">
    </fieldset>
    <fieldset>
		<label>{timezoneText} *</label>
    	<input type="text" id="timezone" value="{timezone}" pattern="^[a-zA-Z]+/{1}[a-zA-Z]+$">
	</fieldset>
</div>

<div>
	<h2>Login</h2>

	<fieldset>
		<label>Normal login</label>
		<input type="checkbox" id="normalLogin" checked="checked">
	</fieldset>
	</fieldset>
	<fieldset>
		<label>OpenID support</label>
		<input type="checkbox" id="openID">
	</fieldset>
	<fieldset>
		<label>LDAP support</label>
		<input type="checkbox" id="lDAP">
	</fieldset>
	
	<div id="LDAP_block">
		<fieldset>
			<label>{host} *</label>
			<input type="text" id="ldap_server">
		</fieldset>
		<fieldset>
			<label>Port *</label>
			<input type="text" id="ldap_port" pattern="^[0-9]+$">
		</fieldset>
	</div>
</div>
<div>
	<h2>{sessionTitle}</h2>

	<fieldset>
    	<label>{sessionNameText}</label>
    	<input type="text" id="sessionName" value="{sessionName}">
	</fieldset>
	<fieldset>
    	<label>{sessionPathText}</label>
    	<input type="text" id="sessionPath" value="{sessionPath}">
	</fieldset>
	<fieldset>
    	<label>{sessionExpireText}</label>
    	<input type="text" id="sessionExpire" value="{sessionExpire}" pattern="^[0-9]+$">
	</fieldset>
</div>
<div>
    <h2>{siteSettings}</h2>

	<fieldset>
		<label>{defaultLanguage}</label>
    	<select id="language">{languages}</select>
	</fieldset>
	<fieldset>
    	<label>{templateDir}</label>
    	<select id="template">{templates}</select>
	</fieldset>
</div>

<div>
	<h2>Mail instellingen</h2>
	
	<fieldset>
		<label>Afzender naam</label>
		<input type="text" id="mail_name">
	</fieldset>
	<fieldset>
		<label>Afzender adres *</label>
		<input type="email" id="mail_email" required>
	</fieldset>
	
	<fieldset>
		<label>Gebruik SMTP</label>
		<input type="checkbox" id="smtp">
	</fieldset>
	<div id="smtp_box">
		<fieldset>
			<label>{host} *</label>
			<input type="text" id="smtp_host">
		</fieldset>
		<fieldset>
			<label>Port *</label>
			<input type="text" id="smtp_port" pattern="^[0-9]+$">
		</fieldset>
		<fieldset>
			<label>{username} *</label>
			<input type="text" id="smtp_username">
		</fieldset>
		<fieldset>
			<label>{password} *</label>
			<input type="password" id="smtp_password">
		</fieldset>
	</div>
</div>

<div>
    <h2>{databaseSettings}</h2>

    <h2 class="errorNotice">{sqlError}</h2>

	<fieldset>
		<label>{username} *</label>
    	<input type="text" id="sqlUsername" value="{sqlUsername}" required>
	</fieldset>
	<fieldset>
    	<label>{password} *</label>
    	<input type="password" id="sqlPassword" value="{sqlPassword}" required>
	</fieldset>
	<fieldset>
    	<label>{database} *</label>
    	<input type="text" id="sqlDatabase" value="{sqlDatabase}" required>
	</fieldset>
	<fieldset>
    	<label>{host} *</label>
    	<input type="text" id="sqlHost" value="{sqlHost}" required>
    </fieldset>
	<fieldset>
    	<label>{port}</label>
   		<input type="text" id="sqlPort" value="{sqlPort}" pattern="^[0-9]+$">
	</fieldset>
	<fieldset>
    	<label>{type}</label>
    	<select id="databaseType">{databases}</select>
	</fieldset>
	<fieldset>
		<label>{databasePrefixText} *</label>
		<input type="text" id="databasePrefix" value="{databasePrefix}" required>
	</fieldset>
</div>
<div>
    <p><input type="button" id="settings_save" value="{buttonSave}"></p>
</div>
</settings>