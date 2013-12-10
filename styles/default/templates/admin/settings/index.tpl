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
	            <td >{basedir}</td>
	            <td><input type="text" name="base" value="{base}" class="formField" id="base"></td>
	        </tr>
	        <tr>
	            <td >{siteUrl}</td>
	            <td><input type="text" name="url" value="{url}" class="formField" id="url"></td>
	        </tr>
	        <tr>
	            <td >{timezoneText}</td>
	            <td><input type="text" name="timezone" value="{timezone}" class="formField" id="timezone"></td>
	        </tr>
	        <tr>
	            <td><br></td>
	        </tr>
	        <tr>
	            <td class="title" colspan="2">{sessionTitle}</td>
	        </tr>
	        <tr>
	            <td >{sessionNameText}</td>
	            <td><input type="text" name="sessionName" value="{sessionName}" class="formField" id="sessionName"></td>
	        </tr>
	        <tr>
	            <td >{sessionPathText}</td>
	            <td><input type="text" name="sessionPath" value="{sessionPath}" class="formField" id="sessionPath"></td>
	        </tr>
	        <tr>
	            <td >{sessionExpireText}</td>
	            <td><input type="text" name="sessionExpire" value="{sessionExpire}" class="formField" id="sessionExpire"></td>
	        </tr>
	        <tr>
	            <td><br></td>
	        </tr>
	        <tr>
	            <td class="title" colspan="2">{siteSettings}</td>
	        </tr>
	        <tr>
	            <td >{defaultLanguage}</td>
	            <td><select name="language" id="language">{posibleLanguages}</select></td>
	        </tr>
	        <tr>
	            <td >{templateDir}</td>
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
	            <td>{username}</td>
	            <td><input type="text" name="sqlUsername" value="{sqlUsername}" class="formField" id="sqlUsername" onblur="adminSettings.validateSQL()"></td>
	        </tr>
	        <tr>
	            <td >{password}</td>
	            <td><input type="text" name="sqlPassword" value="{sqlPassword}" class="formField" id="sqlPassword" onblur="adminSettings.validateSQL()"></td>
	        </tr>
	        <tr>
	            <td >{database}</td>
	            <td><input type="text" name="sqlDatabase" value="{sqlDatabase}" class="formField" id="sqlDatabase" onblur="adminSettings.validateSQL()"></td>
	        </tr>
	        <tr>
	            <td >{host}</td>
	            <td><input type="text" name="sqlHost" value="{sqlHost}" class="formField" id="sqlHost" onblur="adminSettings.validateSQL()"></td>
	        </tr>
	        <tr>
	            <td >{port}</td>
	            <td><input type="text" name="sqlPort" value="{sqlPort}" class="formField" id="sqlPort" onblur="adminSettings.validateSQL()"></td>
	        </tr>
	        <tr>
	            <td >{type}</td>
	            <td><select name="databaseType" id="sqlType" onchange="adminSettings.validateSQL()">{databases}</select></td>
	        </tr>
	        <tr>
	            <td  colspan="2"><input type="button" class="button" value="{buttonSave}" onclick="adminSettings.checkSettings()"></td>
	        </tr>
	    </tbody>
	    </table>
	</section>
</section>
