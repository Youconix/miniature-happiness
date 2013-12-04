<div id="groups" class="window">
	<h1>{headerText}</h1>

	<form action="index.php?step=5" method="post">
	<table cellspacing="0" cellpadding="0" id="userTable">
	<tr>
		<td class="bold">{nickText}</td>
		<td><input type="text" name="nick" id="nick" onblur="checkForm()" value="{nick}"/></td>
		<td id="image_nick"></td>
	</tr>
	<tr>
		<td class="bold">{emailText}</td>
		<td><input type="text" name="email" id="email" onblur="checkForm()" value="{email}"/></td>
		<td id="image_email"></td>
	</tr>
	<tr>
		<td class="bold">{password}</td>
		<td><input type="password" name="password" id="password" onblur="checkForm()"/></td>
		<td id="image_password"></td>
	</tr>
	<tr>
		<td class="bold">{password2}</td>
		<td><input type="password" name="password2" id="password2" onblur="checkForm()"/></td>	
		<td id="image_password2"></td>
	</tr>
	<tr>
		<td colspan="3"><input type="submit" value="{buttonSubmit}"/></td>
	</tr>
	</table>
	</form>
</div>
