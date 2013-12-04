<div id="groups" class="window">
	<h1>{headerText}</h1>

	<p id="addNotice"></p>

	<form action="users.php" method="post" onsubmit="return editCheck()">
	<table cellspacing="0" cellpadding="0" id="userTable">
	<tr>
		<td class="bold">{nickText}</td>
		<td><input type="text" name="nick" id="nick" value="{nickValue}"/></td>
	</tr>
	<tr>
		<td class="bold">{fornameText}</td>
		<td><input type="text" name="forname" id="forname" value="{fornameValue}"/></td>
	</tr>
	<tr>
		<td class="bold">{nameBetweenText}</td>
		<td><input type="text" name="nameBetween" id="nameBetween" value="{nameBetweenValue}"/></td>
	</tr>
	<tr>
		<td class="bold">{surnameText}</td>
		<td><input type="text" name="surname" id="surname" value="{surnameValue}"/></td>
	</tr>
	<tr>
		<td class="bold">{emailText}</td>
		<td><input type="text" name="email" id="email" value="{emailValue}"/></td>
	</tr>
	<tr>
		<td class="bold">{botText}</td>
		<td><input type="checkbox" name="bot" id="bot" {botValue}/></td>
	</tr>
	<tr>
		<td class="bold">{activatedText}</td>
		<td><input type="checkbox" name="activated" id="activated" {activatedValue}/></td>
	</tr>
	<tr>
		<td class="bold">{blockedText}</td>
		<td><input type="checkbox" name="blocked" id="blocked" {blockedValue}/></td>
	</tr>
	<tr>
		<td colspan="2"><input type="hidden" name="id" value="{userid}"/><br/></td>
	</tr>
	<tr>
		<td colspan="2"><input type="submit" value="{buttonSubmit}" class="button"/></td>
	</tr>
	</table>
	</form>

	<h2>{headerChangePassword}</h2>

	<form action="users.php" method="post" onsubmit="return editPasswordForm()">
	<table cellspacing="0" cellpadding="0" id="userTable2">
	<tr>
		<td class="bold">{password}</td>
		<td><input type="password" name="password" id="password"/></td>
	</tr>
	<tr>
		<td class="bold">{password2}</td>
		<td><input type="password" name="password2" id="password2"/></td>
	</tr>
	<tr>
		<td colspan="2"><input type="hidden" name="id" value="{userid}"/><br/></td>
	</tr>
	<tr>
		<td colspan="2"><input type="submit" value="{buttonChange}" class="button"/></td>
	</tr>
	</table>
	</form>
</div>
