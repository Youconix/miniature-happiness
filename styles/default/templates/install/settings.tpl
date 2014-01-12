<h1>{title}</h1>

<h2 class="errorNotice">{generalError}</h2>

<form action="index.php?step=3" method="post">
<table>
<tbody>
<tr>
    <td class="title" colspan="2">General</td>
</tr>
<tr>
    <td><label>{basedir}</label></td>
    <td><input type="text" name="base" value="{base}"></td>
</tr>
<tr>
    <td ><label>{siteUrl}</label></td>
    <td><input type="text" name="url" value="{url}"></td>
</tr>
<tr>
    <td><label>{timezoneText}</label></td>
    <td><input type="text" name="timezone" value="{timezone}"></td>
</tr>
<tr>
    <td><br/></td>
</tr>
<tr>
    <td class="title" colspan="2">{sessionTitle}</td>
</tr>
<tr>
    <td><label>{sessionNameText}</label></td>
    <td><input type="text" name="sessionName" value="{sessionName}"></td>
</tr>
<tr>
    <td><label>{sessionPathText}</label></td>
    <td><input type="text" name="sessionPath" value="{sessionPath}"></td>
</tr>
<tr>
    <td><label>{sessionExpireText}</label></td>
    <td><input type="text" name="sessionExpire" value="{sessionExpire}" pattern="^[0-9]+$"></td>
</tr>
<tr>
    <td><br/></td>
</tr>
<tr>
    <td class="title" colspan="2">{siteSettings}</td>
</tr>
<tr>
    <td><label>{defaultLanguage}</label></td>
    <td><select name="language">{languages}</select></td>
</tr>
<tr>
    <td><label>{templateDir}</label></td>
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
    <td><label>{username}</label></td>
    <td><input type="text" name="sqlUsername" value="{sqlUsername}" required></td>
</tr>
<tr>
    <td><label>{password}</label></td>
    <td><input type="passwordt" name="sqlPassword" value="{sqlPassword}" required></td>
</tr>
<tr>
    <td><label>{database}</label></td>
    <td><input type="text" name="sqlDatabase" value="{sqlDatabase}" required></td>
</tr>
<tr>
    <td><label>{host}</label></td>
    <td><input type="text" name="sqlHost" value="{sqlHost}" required></td>
</tr>
<tr>
    <td><label>{port}</label></td>
    <td><input type="text" name="sqlPort" value="{sqlPort}" pattern="^[0-9]+$"></td>
</tr>
<tr>
    <td><label>{type}</label></td>
    <td><select name="databaseType">{databases}</select></td>
</tr>
<tr>
	<td><label>{databasePrefixText}</label></td>
	<td><input type="text" name="databasePrefix" value="{databasePrefix}" required></td>
</tr>
<tr>
    <td  colspan="2"><input type="submit" class="button" value="{buttonSave}"></td>
</tr>
</tbody>
</table>
</form>
