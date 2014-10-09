<section id="groups" class="window">
	<h1>{headerText}</h1>

	<p id="addNotice"></p>

	<form action="users.php" method="post" onsubmit="return editCheck()">
	<table>
	<tbody>
	<tr>
		<td><label>{nickText}</label></td>
		<td><input type="text" name="nick" id="nick" value="{nickValue}" required></td>
	</tr>
	<tr>
		<td><label>{fornameText}</label></td>
		<td><input type="text" name="forname" id="forname" value="{fornameValue}"></td>
	</tr>
	<tr>
		<td><label>{nameBetweenText}</label></td>
		<td><input type="text" name="nameBetween" id="nameBetween" value="{nameBetweenValue}"></td>
	</tr>
	<tr>
		<td><label>{surnameText}</label></td>
		<td><input type="text" name="surname" id="surname" value="{surnameValue}"></td>
	</tr>
	<tr>
		<td><label>{emailText}</label></td>
		<td><input type="email" name="email" id="email" value="{emailValue}" required></td>
	</tr>
	<tr>
		<td><label>{botText}</label></td>
		<td><input type="checkbox" name="bot" id="bot" {botValue}></td>
	</tr>
	<tr>
		<td><label>{activatedText}</label></td>
		<td><input type="checkbox" name="activated" id="activated" {activatedValue}></td>
	</tr>
	<tr>
		<td><label>{blockedText}</label></td>
		<td><input type="checkbox" name="blocked" id="blocked" {blockedValue}></td>
	</tr>
	<tr>
		<td colspan="2"><input type="hidden" name="id" value="{userid}"><br></td>
	</tr>
	<tr>
		<td colspan="2"><input type="submit" value="{buttonSubmit}" class="button"></td>
	</tr>
	</tbody>
	</table>
	</form>

	<h2>{headerChangePassword}</h2>

	<form action="users.php" method="post" onsubmit="return editPasswordForm()">
	<table id="userTable2">
	<tbody>
	<tr>
		<td><label>{password}</label></td>
		<td><input type="password" name="password" id="password"></td>
	</tr>
	<tr>
		<td><label>{password2}</label></td>
		<td><input type="password" name="password2" id="password2"></td>
	</tr>
	<tr>
		<td colspan="2"><input type="hidden" name="id" value="{userid}"><br></td>
	</tr>
	<tr>
		<td colspan="2"><input type="submit" value="{buttonChange}" class="button"></td>
	</tr>
	</tbody>
	</table>
	</form>
</section>
