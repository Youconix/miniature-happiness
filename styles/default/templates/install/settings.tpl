<h1>{title}</h1>

<form action="index.php?step=3" method="post">
<table>
<tr>
    <td class="errorNotice" colspan="2">{generalError}</td>
</tr>
<tr>
    <td class="title" colspan="2">General</td>
</tr>
<tr>
    <td >{basedir}</td>
    <td><input type="text" name="base" value="{base}"/></td>
</tr>
<tr>
    <td >{siteUrl}</td>
    <td><input type="text" name="url" value="{url}"/></td>
</tr>
<tr>
    <td >{timezoneText}</td>
    <td><input type="text" name="timezone" value="{timezone}"/></td>
</tr>
<tr>
    <td><br/></td>
</tr>
<tr>
    <td class="title" colspan="2">{sessionTitle}</td>
</tr>
<tr>
    <td >{sessionNameText}</td>
    <td><input type="text" name="sessionName" value="{sessionName}"/></td>
</tr>
<tr>
    <td >{sessionPathText}</td>
    <td><input type="text" name="sessionPath" value="{sessionPath}"/></td>
</tr>
<tr>
    <td >{sessionExpireText}</td>
    <td><input type="text" name="sessionExpire" value="{sessionExpire}"/></td>
</tr>
<tr>
    <td><br/></td>
</tr>
<tr>
    <td class="title" colspan="2">{siteSettings}</td>
</tr>
<tr>
    <td >{defaultLanguage}</td>
    <td><select name="language">{languages}</select></td>
</tr>
<tr>
    <td >{templateDir}</td>
    <td><select name="template">{templates}</select></td>
</tr>
<tr>
    <td><br/></td>
</tr>
<tr>
    <td class="title" colspan="2">{databaseSettings}</td>
</tr>
<tr>
    <td class="errorNotice" colspan="2">{sqlError}</td>
</tr>
<tr>
    <td>{username}</td>
    <td><input type="text" name="sqlUsername" value="{sqlUsername}"/></td>
</tr>
<tr>
    <td >{password}</td>
    <td><input type="text" name="sqlPassword" value="{sqlPassword}"/></td>
</tr>
<tr>
    <td >{database}</td>
    <td><input type="text" name="sqlDatabase" value="{sqlDatabase}"/></td>
</tr>
<tr>
    <td >{host}</td>
    <td><input type="text" name="sqlHost" value="{sqlHost}"/></td>
</tr>
<tr>
    <td >{port}</td>
    <td><input type="text" name="sqlPort" value="{sqlPort}"/></td>
</tr>
<tr>
    <td >{type}</td>
    <td><select name="databaseType">{databases}</select></td>
</tr>
<tr>
	<td>{databasePrefixText}</td>
	<td><input type="text" name="databasePrefix" value="{databasePrefix}"/></td>
</tr>
<tr>
    <td  colspan="2"><input type="submit" class="button" value="{buttonSave}"/></td>
</tr>
</table>
</form>
