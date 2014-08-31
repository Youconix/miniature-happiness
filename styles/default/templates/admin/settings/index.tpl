<section id="settingsView">
    <h1>{settingsTitle}</h1>

	<section>
	    <table>
	    <tbody>
	        <tr>
	            <td class="Notice" colspan="2" class="title" id="notice"></td>
	        </tr>
	        <tr>
	            <td class="title" colspan="2">General</td>
	        </tr>
	        <tr>
	            <td><label>{basedir}</label></td>
	            <td><input type="text" name="base" value="{base}" id="base"></td>
	        </tr>
	        <tr>
	            <td><label>{siteUrl}</label></td>
	            <td><input type="url" name="url" value="{url}" id="url" required></td>
	        </tr>
	        <tr>
	            <td>{timezoneText}</td>
	            <td><input type="text" name="timezone" value="{timezone}" id="timezone" required></td>
	        </tr>
	        <tr>
	            <td><br></td>
	        </tr>
	        <tr>
	            <td class="title" colspan="2">{sessionTitle}</td>
	        </tr>
	        <tr>
	            <td><label>{sessionNameText}</label></td>
	            <td><input type="text" name="sessionName" value="{sessionName}" id="sessionName"></td>
	        </tr>
	        <tr>
	            <td><label>{sessionPathText}</label></td>
	            <td><input type="text" name="sessionPath" value="{sessionPath}" id="sessionPath"></td>
	        </tr>
	        <tr>
	            <td><label>{sessionExpireText}</label></td>
	            <td><input type="text" name="sessionExpire" value="{sessionExpire}" id="sessionExpire" pattern="^[0-9]+$"></td>
	        </tr>
	        <tr>
	            <td><br></td>
	        </tr>
	        <tr>
	            <td class="title" colspan="2">{siteSettings}</td>
	        </tr>
	        <tr>
	            <td><label>{defaultLanguage}</label></td>
	            <td><select name="language" id="language">{posibleLanguages}</select></td>
	        </tr>
	        <tr>
	            <td><label>{templateDir}</label></td>
	            <td><select name="template" id="template">{templates}</select></td>
	        </tr>
	        <tr>
	            <td><br></td>
	        </tr>
	        <tr>
	            <td class="title" colspan="2">{databaseSettings}</td>
	        </tr>
	        <tr>
	            <td class="errorNotice" colspan="2" id="sqlError">{sqlError}</td>
	        </tr>
	        <tr>
	            <td><label>{username}</label></td>
	            <td><input type="text" name="sqlUsername" value="{sqlUsername}" id="sqlUsername" onblur="adminSettings.validateSQL()" required></td>
	        </tr>
	        <tr>
	            <td><label>{password}</label></td>
	            <td><input type="text" name="sqlPassword" value="{sqlPassword}" id="sqlPassword" onblur="adminSettings.validateSQL()" required></td>
	        </tr>
	        <tr>
	            <td><label>{database}</label></td>
	            <td><input type="text" name="sqlDatabase" value="{sqlDatabase}" id="sqlDatabase" onblur="adminSettings.validateSQL()" required></td>
	        </tr>
	        <tr>
	            <td><label>{host}</label></td>
	            <td><input type="text" nae="sqlHost" value="{sqlHost}" id="sqlHost" onblur="adminSettings.validateSQL()" required></td>
	        </tr>
	        <tr>
	            <td><label>{port}</label></td>
	            <td><input type="text" name="sqlPort" value="{sqlPort}" id="sqlPort" onblur="adminSettings.validateSQL()" pattern="^[0-9]+$"></td>
	        </tr>
	        <tr>
	            <td><label>{type}</label></td>
	            <td><select name="databaseType" id="sqlType" onchange="adminSettings.validateSQL()">{databases}</select></td>
	        </tr>
	        <tr>
	            <td  colspan="2"><input type="button" class="button" value="{buttonSave}" onclick="adminSettings.checkSettings()"></td>
	        </tr>
	    </tbody>
	    </table>
	</section>
</section>